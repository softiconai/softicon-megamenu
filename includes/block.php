<?php
namespace ALMM_Mega_Menu;
if ( !defined('ABSPATH') ) { exit;}

class block {

    // template parts being rendered right now, so a panel that holds its own menu item can't loop
    private static $rendering = array();

    public function __construct()
    {
        add_action('init', array( $this, 'register' ));
        add_filter('default_wp_template_part_areas', array( $this, 'area' ));
    }

    public function register() {
        register_block_type( ALMM_PLUGIN_PATH . 'build' );

        // what a new panel starts with and the Mega Menus screen, for the block settings
        $data = 'window.almmData = ' . wp_json_encode( array(
            'starter'   => self::starter(),
            'panelsUrl' => admin_url( 'themes.php?page=softicon-mega-menu' ),
        ) ) . ';';
        wp_add_inline_script( generate_block_asset_handle( 'softicon/mega-menu', 'editorScript' ), $data, 'before' );
    }

    // three columns of links, so a new panel shows something to edit
    public static function starter() {
        $columns = array(
            array( __( 'Shop', 'softicon-mega-menu' ), __( 'New arrivals', 'softicon-mega-menu' ), __( 'Best sellers', 'softicon-mega-menu' ), __( 'Sale', 'softicon-mega-menu' ) ),
            array( __( 'Company', 'softicon-mega-menu' ), __( 'About us', 'softicon-mega-menu' ), __( 'Careers', 'softicon-mega-menu' ), __( 'Contact', 'softicon-mega-menu' ) ),
            array( __( 'Help', 'softicon-mega-menu' ), __( 'Shipping', 'softicon-mega-menu' ), __( 'Returns', 'softicon-mega-menu' ), __( 'FAQ', 'softicon-mega-menu' ) ),
        );

        $html = '';
        foreach ( $columns as $column ) {
            $html .= "<!-- wp:column -->\n<div class=\"wp-block-column\"><!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">" . esc_html( array_shift( $column ) ) . "</h4>\n<!-- /wp:heading -->";
            foreach ( $column as $link ) {
                $html .= "\n\n<!-- wp:paragraph -->\n<p><a href=\"#\">" . esc_html( $link ) . "</a></p>\n<!-- /wp:paragraph -->";
            }
            $html .= "</div>\n<!-- /wp:column -->\n\n";
        }

        return "<!-- wp:columns -->\n<div class=\"wp-block-columns\">" . rtrim( $html ) . "</div>\n<!-- /wp:columns -->";
    }

    // the Site Editor opened on one template part, id is "theme//slug"
    public static function edit_url( $id ) {
        return add_query_arg( array(
            'postType' => 'wp_template_part',
            'postId'   => rawurlencode( $id ),
            'canvas'   => 'edit',
        ), admin_url( 'site-editor.php' ) );
    }

    // a "Mega Menu" area in the Site Editor for the panels; the same "menu" area other mega menu plugins use is kept, not added twice
    public function area( $areas ) {
        foreach ( (array) $areas as $area ) {
            if ( isset( $area['area'] ) && 'menu' === $area['area'] ) {
                return $areas;
            }
        }

        $areas[] = array(
            'area'        => 'menu',
            'area_tag'    => 'div',
            'label'       => __( 'Mega Menu', 'softicon-mega-menu' ),
            'description' => __( 'Panels that open from a Mega Menu item in the Navigation block.', 'softicon-mega-menu' ),
            'icon'        => 'layout',
        );
        return $areas;
    }

    // the template part's blocks, or '' when it is missing or already being rendered
    public static function panel( $slug ) {
        $slug = sanitize_title( $slug );
        if ( ! $slug || isset( self::$rendering[ $slug ] ) ) {
            return '';
        }

        self::$rendering[ $slug ] = true;
        ob_start();
        block_template_part( $slug );
        $html = ob_get_clean();
        unset( self::$rendering[ $slug ] );

        return trim( $html );
    }

    // hex, rgb(a), hsl(a) or a preset var(), anything else is dropped
    public static function color( $value ) {
        $value = is_string( $value ) ? trim( $value ) : '';
        if ( preg_match( '/^(#[0-9a-f]{3,8}|(rgb|hsl)a?\([0-9.,%\s\/]+\)|var\(--[a-z0-9-]+\))$/i', $value ) ) {
            return $value;
        }
        return '';
    }

    private static function choice( $attributes, $key, $choices ) {
        return isset( $attributes[ $key ] ) && in_array( $attributes[ $key ], $choices, true ) ? $attributes[ $key ] : $choices[0];
    }

    // settings read by view.js
    public static function config( $attributes ) {
        $url = ! empty( $attributes['url'] );

        $config = array(
            'openOn' => self::choice( $attributes, 'openOn', array( 'hover', 'click' ) ),
            'width'  => self::choice( $attributes, 'width', array( 'wide', 'content', 'full', 'custom' ) ),
            'align'  => self::choice( $attributes, 'panelAlign', array( 'center', 'left', 'right' ) ),
            // "go to the link" on phones needs a link
            'mobile' => $url ? self::choice( $attributes, 'mobile', array( 'accordion', 'link' ) ) : 'accordion',
        );

        return apply_filters( 'almm_config', $config, $attributes );
    }

    // the panel's size and colors as css variables on the menu item
    public static function style( $attributes, $config ) {
        $widths = array(
            'content' => 'var(--wp--style--global--content-size, 800px)',
            'wide'    => 'var(--wp--style--global--wide-size, 1200px)',
            'full'    => '100vw',
            'custom'  => min( 2400, max( 200, absint( $attributes['customWidth'] ?? 800 ) ) ) . 'px',
        );

        $vars = array(
            '--almm-width'  => $widths[ $config['width'] ],
            '--almm-gap'    => min( 100, absint( $attributes['gap'] ?? 0 ) ) . 'px',
            '--almm-pad'    => min( 100, absint( $attributes['padding'] ?? 24 ) ) . 'px',
            '--almm-radius' => 'full' === $config['width'] ? '0px' : min( 60, absint( $attributes['radius'] ?? 8 ) ) . 'px',
            '--almm-bg'     => self::color( $attributes['panelBg'] ?? '' ),
            '--almm-color'  => self::color( $attributes['panelColor'] ?? '' ),
        );

        $style = '';
        foreach ( $vars as $name => $value ) {
            if ( '' !== $value ) {
                $style .= $name . ':' . $value . ';';
            }
        }
        return $style;
    }
}
