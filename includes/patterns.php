<?php
namespace ALMM_Mega_Menu;
if ( !defined('ABSPATH') ) { exit;}

// ready-made panel designs: block patterns for the "menu" template part area, also offered by "Create panel"
class patterns {

    const CATEGORY = 'softicon-mega-menu';

    public function __construct()
    {
        add_action('init', array( $this, 'register' ));
    }

    public function register() {
        register_block_pattern_category( self::CATEGORY, array( 'label' => __( 'Mega Menu', 'softicon-mega-menu' ) ) );

        foreach ( self::designs() as $key => $design ) {
            register_block_pattern( self::CATEGORY . '/' . $key, array(
                'title'         => $design['title'],
                'description'   => $design['text'],
                'categories'    => array( self::CATEGORY ),
                'blockTypes'    => array( 'core/template-part/menu' ),
                'postTypes'     => array( 'wp_template_part' ),
                'viewportWidth' => 1200,
                'content'       => self::content( $key ),
            ) );
        }
    }

    // key => title and a short line for the design pickers, in the order they are offered
    public static function designs() {
        return array(
            'links'    => array( 'title' => __( 'Links in columns', 'softicon-mega-menu' ), 'text' => __( 'Three columns of links with small headings.', 'softicon-mega-menu' ) ),
            'shop'     => array( 'title' => __( 'Shop', 'softicon-mega-menu' ), 'text' => __( 'Columns of links and a featured product with a button.', 'softicon-mega-menu' ) ),
            'services' => array( 'title' => __( 'Services', 'softicon-mega-menu' ), 'text' => __( 'Services with a short line each and a call to action box.', 'softicon-mega-menu' ) ),
            'posts'    => array( 'title' => __( 'Latest posts', 'softicon-mega-menu' ), 'text' => __( 'Your three newest posts with their featured images.', 'softicon-mega-menu' ) ),
            'promo'    => array( 'title' => __( 'Promo', 'softicon-mega-menu' ), 'text' => __( 'A big heading, two buttons and an image.', 'softicon-mega-menu' ) ),
        );
    }

    // a design's blocks; unknown keys get the links design
    public static function content( $key ) {
        switch ( $key ) {
            case 'shop':
                $blocks = self::columns(
                    self::links_column( __( 'Shop', 'softicon-mega-menu' ), array( __( 'New arrivals', 'softicon-mega-menu' ), __( 'Best sellers', 'softicon-mega-menu' ), __( 'Gift cards', 'softicon-mega-menu' ), __( 'Sale', 'softicon-mega-menu' ) ) )
                    . self::links_column( __( 'Collections', 'softicon-mega-menu' ), array( __( 'Spring', 'softicon-mega-menu' ), __( 'Home and living', 'softicon-mega-menu' ), __( 'Outdoor', 'softicon-mega-menu' ), __( 'Limited edition', 'softicon-mega-menu' ) ) )
                    . self::links_column( __( 'Help', 'softicon-mega-menu' ), array( __( 'Shipping', 'softicon-mega-menu' ), __( 'Returns', 'softicon-mega-menu' ), __( 'Size guide', 'softicon-mega-menu' ), __( 'Contact us', 'softicon-mega-menu' ) ) )
                    . self::column(
                        self::cover(
                            'botany-flowers.webp', __( 'White flowers with green leaves', 'softicon-mega-menu' ),
                            self::heading( __( 'The spring collection', 'softicon-mega-menu' ), 3, 'large' )
                            . self::paragraph( esc_html__( 'Fresh designs, just in.', 'softicon-mega-menu' ), 'small' )
                            . self::buttons( self::button( __( 'Shop now', 'softicon-mega-menu' ), 'light' ) )
                        ),
                        '', '32%'
                    )
                );
                break;

            case 'services':
                $items = array(
                    array( __( 'Web design', 'softicon-mega-menu' ), __( 'Websites that look good on every screen.', 'softicon-mega-menu' ) ),
                    array( __( 'Branding', 'softicon-mega-menu' ), __( 'Logos, colors and type that fit you.', 'softicon-mega-menu' ) ),
                    array( __( 'Content', 'softicon-mega-menu' ), __( 'Words and pictures that tell your story.', 'softicon-mega-menu' ) ),
                    array( __( 'Marketing', 'softicon-mega-menu' ), __( 'Get found by the right people.', 'softicon-mega-menu' ) ),
                    array( __( 'Support', 'softicon-mega-menu' ), __( 'Updates, backups and quick fixes.', 'softicon-mega-menu' ) ),
                    array( __( 'Consulting', 'softicon-mega-menu' ), __( 'A clear plan before you build.', 'softicon-mega-menu' ) ),
                );
                $columns = '';
                foreach ( array_chunk( $items, 3 ) as $chunk ) {
                    $column = '';
                    foreach ( $chunk as $item ) {
                        $column .= self::group( self::paragraph( '<strong><a href="#">' . esc_html( $item[0] ) . '</a></strong>', 'medium' ) . self::paragraph( esc_html( $item[1] ), 'small' ), '0.2rem' );
                    }
                    $columns .= self::column( $column, '1.4rem' );
                }
                $cta = '<!-- wp:group {"style":{"border":{"radius":"10px"},"spacing":{"padding":{"top":"1.5rem","bottom":"1.5rem","left":"1.5rem","right":"1.5rem"},"blockGap":"0.8rem"}},"backgroundColor":"contrast","textColor":"base","layout":{"type":"default"}} -->' . "\n"
                    . '<div class="wp-block-group has-base-color has-contrast-background-color has-text-color has-background" style="border-radius:10px;padding-top:1.5rem;padding-right:1.5rem;padding-bottom:1.5rem;padding-left:1.5rem">'
                    . self::heading( __( 'Not sure where to start?', 'softicon-mega-menu' ), 3, 'large' )
                    . self::paragraph( esc_html__( 'Tell us about your project in a free 20-minute call.', 'softicon-mega-menu' ), 'small' )
                    . self::buttons( self::button( __( 'Book a call', 'softicon-mega-menu' ), 'base' ) )
                    . "</div>\n<!-- /wp:group -->";
                $blocks = self::columns( $columns . self::column( $cta, '', '34%' ) );
                break;

            case 'posts':
                $blog = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );
                $blocks = self::label( __( 'From the blog', 'softicon-mega-menu' ) )
                    . '<!-- wp:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->' . "\n"
                    . '<div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"1.5rem"}},"layout":{"type":"grid","columnCount":3}} -->' . "\n"
                    . '<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9","style":{"border":{"radius":"10px"}}} /-->' . "\n\n"
                    . '<!-- wp:post-date {"fontSize":"small"} /-->' . "\n\n"
                    . '<!-- wp:post-title {"level":3,"isLink":true,"style":{"spacing":{"margin":{"top":"0.3rem"}}},"fontSize":"medium"} /-->' . "\n"
                    . "<!-- /wp:post-template --></div>\n<!-- /wp:query -->"
                    . self::paragraph( '<a href="' . esc_url( $blog ) . '">' . esc_html__( 'All posts →', 'softicon-mega-menu' ) . '</a>', 'small' );
                break;

            case 'promo':
                $blocks = '<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"2.5rem"}}}} -->' . "\n"
                    . '<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","style":{"spacing":{"blockGap":"0.9rem"}}} -->' . "\n"
                    . '<div class="wp-block-column is-vertically-aligned-center">'
                    . self::label( __( 'New season', 'softicon-mega-menu' ) )
                    . self::heading( __( 'Fresh picks for brighter days', 'softicon-mega-menu' ), 3, 'x-large' )
                    . self::paragraph( esc_html__( 'Hand-picked favourites, new every week. Find something you love.', 'softicon-mega-menu' ) )
                    . self::buttons( self::button( __( 'Shop now', 'softicon-mega-menu' ) ) . self::button( __( 'Learn more', 'softicon-mega-menu' ), 'outline' ) )
                    . "</div>\n<!-- /wp:column -->\n\n"
                    . '<!-- wp:column {"verticalAlignment":"center"} -->' . "\n"
                    . '<div class="wp-block-column is-vertically-aligned-center">'
                    . self::image( 'northern-buttercups.webp', __( 'A yellow buttercup flower', 'softicon-mega-menu' ), '16/9' )
                    . "</div>\n<!-- /wp:column --></div>\n<!-- /wp:columns -->";
                break;

            default:
                $blocks = self::columns(
                    self::links_column( __( 'Shop', 'softicon-mega-menu' ), array( __( 'New arrivals', 'softicon-mega-menu' ), __( 'Best sellers', 'softicon-mega-menu' ), __( 'Sale', 'softicon-mega-menu' ) ) )
                    . self::links_column( __( 'Company', 'softicon-mega-menu' ), array( __( 'About us', 'softicon-mega-menu' ), __( 'Careers', 'softicon-mega-menu' ), __( 'Contact', 'softicon-mega-menu' ) ) )
                    . self::links_column( __( 'Help', 'softicon-mega-menu' ), array( __( 'Shipping', 'softicon-mega-menu' ), __( 'Returns', 'softicon-mega-menu' ), __( 'FAQ', 'softicon-mega-menu' ) ) )
                );
        }

        // links without underline until hovered, for every design
        return '<!-- wp:group {"style":{"elements":{"link":{"typography":{"textDecoration":"none"},":hover":{"typography":{"textDecoration":"underline"}}}}},"layout":{"type":"default"}} -->' . "\n"
            . '<div class="wp-block-group">' . $blocks . "</div>\n<!-- /wp:group -->";
    }

    private static function columns( $inner ) {
        return '<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"2.5rem"}}}} -->' . "\n"
            . '<div class="wp-block-columns">' . rtrim( $inner ) . "</div>\n<!-- /wp:columns -->";
    }

    private static function column( $inner, $gap = '0.7rem', $width = '' ) {
        $attrs = array();
        if ( $width ) {
            $attrs['width'] = $width;
        }
        if ( $gap ) {
            $attrs['style'] = array( 'spacing' => array( 'blockGap' => $gap ) );
        }
        return '<!-- wp:column ' . ( $attrs ? wp_json_encode( $attrs ) . ' ' : '' ) . '-->' . "\n"
            . '<div class="wp-block-column"' . ( $width ? ' style="flex-basis:' . esc_attr( $width ) . '"' : '' ) . '>' . $inner . "</div>\n<!-- /wp:column -->\n\n";
    }

    private static function group( $inner, $gap ) {
        return '<!-- wp:group {"style":{"spacing":{"blockGap":"' . esc_attr( $gap ) . '"}},"layout":{"type":"default"}} -->' . "\n"
            . '<div class="wp-block-group">' . $inner . "</div>\n<!-- /wp:group -->\n\n";
    }

    private static function links_column( $title, $links ) {
        $html = self::label( $title, 4 );
        foreach ( $links as $link ) {
            $html .= self::paragraph( '<a href="#">' . esc_html( $link ) . '</a>', 'medium' );
        }
        return self::column( $html );
    }

    // small, bold, uppercase: column headings and section labels
    private static function label( $text, $level = 0 ) {
        $style = '{"typography":{"textTransform":"uppercase","letterSpacing":"0.12em","fontWeight":"700"}}';
        $css   = 'font-weight:700;letter-spacing:0.12em;text-transform:uppercase';
        if ( $level ) {
            return '<!-- wp:heading {"level":' . (int) $level . ',"style":' . $style . ',"fontSize":"small"} -->' . "\n"
                . '<h' . (int) $level . ' class="wp-block-heading has-small-font-size" style="' . $css . '">' . esc_html( $text ) . '</h' . (int) $level . ">\n<!-- /wp:heading -->\n\n";
        }
        return '<!-- wp:paragraph {"style":' . $style . ',"fontSize":"small"} -->' . "\n"
            . '<p class="has-small-font-size" style="' . $css . '">' . esc_html( $text ) . "</p>\n<!-- /wp:paragraph -->\n\n";
    }

    private static function heading( $text, $level, $size ) {
        return '<!-- wp:heading {"level":' . (int) $level . ',"fontSize":"' . esc_attr( $size ) . '"} -->' . "\n"
            . '<h' . (int) $level . ' class="wp-block-heading has-' . esc_attr( $size ) . '-font-size">' . esc_html( $text ) . '</h' . (int) $level . ">\n<!-- /wp:heading -->\n\n";
    }

    // $html is already escaped
    private static function paragraph( $html, $size = '' ) {
        if ( $size ) {
            return '<!-- wp:paragraph {"fontSize":"' . esc_attr( $size ) . '"} -->' . "\n"
                . '<p class="has-' . esc_attr( $size ) . '-font-size">' . $html . "</p>\n<!-- /wp:paragraph -->\n\n";
        }
        return "<!-- wp:paragraph -->\n<p>" . $html . "</p>\n<!-- /wp:paragraph -->\n\n";
    }

    private static function image( $file, $alt, $ratio ) {
        return '<!-- wp:image {"aspectRatio":"' . esc_attr( $ratio ) . '","scale":"cover","style":{"border":{"radius":"10px"}}} -->' . "\n"
            . '<figure class="wp-block-image has-custom-border"><img src="' . esc_url( ALMM_PLUGIN_URL . 'assets/images/' . $file ) . '" alt="' . esc_attr( $alt ) . '" style="border-radius:10px;aspect-ratio:' . esc_attr( $ratio ) . ';object-fit:cover"/></figure>' . "\n<!-- /wp:image -->\n\n";
    }

    // a photo with the text on it, darkened so white text stays readable
    private static function cover( $file, $alt, $inner ) {
        return '<!-- wp:cover {"url":"' . esc_url( ALMM_PLUGIN_URL . 'assets/images/' . $file ) . '","alt":"' . esc_attr( $alt ) . '","dimRatio":40,"customOverlayColor":"#000000","isUserOverlayColor":true,"minHeight":260,"minHeightUnit":"px","contentPosition":"bottom left","style":{"border":{"radius":"10px"},"spacing":{"padding":{"top":"1.5rem","bottom":"1.5rem","left":"1.5rem","right":"1.5rem"},"blockGap":"0.6rem"}},"layout":{"type":"default"}} -->' . "\n"
            . '<div class="wp-block-cover has-custom-content-position is-position-bottom-left" style="border-radius:10px;padding-top:1.5rem;padding-right:1.5rem;padding-bottom:1.5rem;padding-left:1.5rem;min-height:260px"><img class="wp-block-cover__image-background" alt="' . esc_attr( $alt ) . '" src="' . esc_url( ALMM_PLUGIN_URL . 'assets/images/' . $file ) . '" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim-40 has-background-dim" style="background-color:#000000"></span><div class="wp-block-cover__inner-container">'
            . rtrim( $inner ) . "</div></div>\n<!-- /wp:cover -->";
    }

    private static function buttons( $inner ) {
        return "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\">" . $inner . "</div>\n<!-- /wp:buttons -->";
    }

    // $look: '' for the theme's button, 'outline', 'base' for a light button on a dark box, 'light' for a white one on a photo
    private static function button( $text, $look = '' ) {
        if ( 'outline' === $look ) {
            return "<!-- wp:button {\"className\":\"is-style-outline\"} -->\n<div class=\"wp-block-button is-style-outline\"><a class=\"wp-block-button__link wp-element-button\" href=\"#\">" . esc_html( $text ) . "</a></div>\n<!-- /wp:button -->\n\n";
        }
        if ( 'light' === $look ) {
            return "<!-- wp:button {\"style\":{\"color\":{\"background\":\"#ffffff\",\"text\":\"#111111\"}}} -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-text-color has-background wp-element-button\" href=\"#\" style=\"color:#111111;background-color:#ffffff\">" . esc_html( $text ) . "</a></div>\n<!-- /wp:button -->\n\n";
        }
        if ( 'base' === $look ) {
            return "<!-- wp:button {\"backgroundColor\":\"base\",\"textColor\":\"contrast\"} -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-contrast-color has-base-background-color has-text-color has-background wp-element-button\" href=\"#\">" . esc_html( $text ) . "</a></div>\n<!-- /wp:button -->\n\n";
        }
        return "<!-- wp:button -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"#\">" . esc_html( $text ) . "</a></div>\n<!-- /wp:button -->\n\n";
    }
}
