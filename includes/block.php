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
        add_filter('rest_post_dispatch', array( $this, 'preview_styles' ), 10, 3);
    }

    // the editor's panel preview gets only HTML from the block renderer; layout, gap, grid and link styles go to the
    // page's <style> instead, so they are added to the preview's HTML here
    public function preview_styles( $response, $server, $request ) {
        if ( '/wp/v2/block-renderer/core/template-part' !== $request->get_route() || ! $request->get_param( 'almm_preview' ) || ! $response instanceof \WP_REST_Response || $response->is_error() ) {
            return $response;
        }
        $data = $response->get_data();
        $css  = wp_style_engine_get_stylesheet_from_context( 'block-supports', array( 'prettify' => false ) );
        if ( isset( $data['rendered'] ) && $css ) {
            $data['rendered'] .= '<style>' . wp_strip_all_tags( $css ) . '</style>';
            $response->set_data( $data );
        }
        return $response;
    }

    public function register() {
        register_block_type( ALMM_PLUGIN_PATH . 'build' );
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
