<?php
namespace ALMM_Mega_Menu;
if ( !defined('ABSPATH') ) { exit;}

// Appearance → Mega Menus: every panel in one list, a new panel in one click, and how to add a mega menu
class admin {

    const SLUG = 'softicon-mega-menu';

    private $hook = '';

    // the panel made by the last form post: array( title, edit url )
    private $created = array();

    public function __construct()
    {
        add_action('admin_menu', array( $this, 'menu' ));
        add_action('admin_enqueue_scripts', array( $this, 'styles' ));
        add_filter('plugin_action_links_' . plugin_basename( ALMM_PLUGIN_PATH . 'softicon-mega-menu.php' ), array( $this, 'action_links' ));
    }

    public static function url() {
        return admin_url( 'themes.php?page=' . self::SLUG );
    }

    public function menu() {
        $this->hook = add_theme_page( __( 'Mega Menus', 'softicon-mega-menu' ), __( 'Mega Menus', 'softicon-mega-menu' ), 'edit_theme_options', self::SLUG, array( $this, 'render' ) );
        if ( $this->hook ) {
            add_action( 'load-' . $this->hook, array( $this, 'create' ) );
        }
    }

    public function action_links( $links ) {
        array_unshift( $links, '<a href="' . esc_url( self::url() ) . '">' . esc_html__( 'Manage panels', 'softicon-mega-menu' ) . '</a>' );
        return $links;
    }

    public function styles( $hook ) {
        if ( $hook !== $this->hook ) {
            return;
        }
        wp_register_style( 'almm-admin', false, array(), ALMM_PLUGIN_VERSION );
        wp_enqueue_style( 'almm-admin' );
        wp_add_inline_style( 'almm-admin', '
            .almm-admin .card { max-width: 820px; }
            .almm-admin .almm-steps { margin: 0 0 4px 18px; }
            .almm-admin .almm-steps li { margin-bottom: 6px; }
            .almm-admin .almm-create { max-width: 820px; margin: 12px 0 16px; }
            .almm-admin .almm-create-row { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
            .almm-admin .almm-create input[type=text] { width: 280px; max-width: 100%; }
            .almm-admin .almm-designs { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin: 0 0 12px; padding: 0; border: 0; }
            .almm-admin .almm-design { position: relative; display: block; padding: 10px; border: 1px solid #dcdcde; border-radius: 8px; background: #fff; cursor: pointer; }
            .almm-admin .almm-design:hover { border-color: #8c8f94; }
            .almm-admin .almm-design input { position: absolute; opacity: 0; }
            .almm-admin .almm-design svg { display: block; width: 100%; height: auto; margin-bottom: 8px; border-radius: 4px; background: #f6f7f7; }
            .almm-admin .almm-design strong { display: block; }
            .almm-admin .almm-design small { display: block; color: #646970; line-height: 1.4; }
            .almm-admin .almm-design:has(input:checked) { border-color: #4f46e5; box-shadow: 0 0 0 1px #4f46e5; background: #f5f3ff; }
            .almm-admin .almm-design:has(input:focus-visible) { outline: 2px solid #4f46e5; outline-offset: 2px; }
            .almm-admin .almm-panels { max-width: 820px; }
            .almm-admin .almm-panels code { font-size: 12px; }
            .almm-admin .almm-panels + .description { margin-top: 12px; }
            .almm-admin .almm-videos { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 10px; margin: 16px 0 4px; }
            .almm-admin .almm-video { display: flex; align-items: center; gap: 12px; padding: 12px 14px; border: 1px solid #dcdcde; border-radius: 8px; background: #f6f7f7; color: #1d2327; text-decoration: none; }
            .almm-admin .almm-video:hover, .almm-admin .almm-video:focus { border-color: #4f46e5; background: #f5f3ff; color: #1d2327; }
            .almm-admin .almm-video svg { flex: none; width: 36px; height: 36px; }
            .almm-admin .almm-video strong { display: block; }
            .almm-admin .almm-video small { color: #646970; }
            .almm-video-dialog { width: min(960px, 92vw); padding: 0; border: 0; border-radius: 10px; background: #000; overflow: visible; }
            .almm-video-dialog::backdrop { background: rgba(15, 23, 42, .75); }
            .almm-video-dialog .almm-video-frame { aspect-ratio: 16 / 9; }
            .almm-video-dialog iframe { display: block; width: 100%; height: 100%; border: 0; border-radius: 10px; }
            .almm-video-dialog .almm-video-close { position: absolute; top: -40px; right: 0; padding: 4px 8px; border: 0; background: none; color: #fff; font-size: 28px; line-height: 1; cursor: pointer; }
        ' );

        // video popup (youtube-nocookie.com, only after a click) and opening a new panel in the Site Editor
        wp_enqueue_script( 'almm-admin', plugins_url( 'admin.js', __FILE__ ), array(), ALMM_PLUGIN_VERSION, true );
    }

    // the theme's header template part, where the Navigation block usually lives; themes with several headers name the main one "header"
    private function header_url() {
        $headers = get_block_templates( array( 'area' => 'header' ), 'wp_template_part' );
        if ( ! $headers ) {
            return admin_url( 'site-editor.php' );
        }
        foreach ( $headers as $header ) {
            if ( 'header' === $header->slug ) {
                return block::edit_url( $header->id );
            }
        }
        return block::edit_url( $headers[0]->id );
    }

    // video guides on the softicon YouTube channel
    private function videos() {
        return array(
            array(
                'id'     => 'X_APEz4RrdM',
                'title'  => __( 'Step-by-step guide', 'softicon-mega-menu' ),
                'text'   => __( 'Build a mega menu from start to finish', 'softicon-mega-menu' ),
                'length' => '2:28',
            ),
            array(
                'id'     => 'dujCbVTPBlQ',
                'title'  => __( 'Quick tour', 'softicon-mega-menu' ),
                'text'   => __( 'What the plugin can do', 'softicon-mega-menu' ),
                'length' => '0:32',
            ),
        );
    }

    // a small wireframe of a design: rectangles as x, y, width, height and l(ine), d(ark), i(mage) or b(utton)
    private function thumb( $key ) {
        $col = function ( $x, $lines = 3 ) {
            $rects = array( array( $x, 12, 18, 4, 'd' ) );
            for ( $i = 0; $i < $lines; $i++ ) {
                $rects[] = array( $x, 22 + $i * 9, 24, 3, 'l' );
            }
            return $rects;
        };
        switch ( $key ) {
            case 'shop':
                $rects = array_merge( $col( 8 ), $col( 30 ), $col( 52 ), array( array( 80, 10, 32, 24, 'i' ), array( 80, 38, 26, 4, 'd' ), array( 80, 46, 30, 3, 'l' ), array( 80, 54, 16, 6, 'b' ) ) );
                break;
            case 'services':
                $rects = array( array( 80, 10, 32, 52, 'd' ), array( 86, 18, 20, 4, 'l' ), array( 86, 48, 16, 6, 'l' ) );
                foreach ( array( 8, 42 ) as $x ) {
                    for ( $i = 0; $i < 3; $i++ ) {
                        $rects[] = array( $x, 12 + $i * 17, 18, 4, 'd' );
                        $rects[] = array( $x, 19 + $i * 17, 28, 3, 'l' );
                    }
                }
                break;
            case 'posts':
                $rects = array( array( 8, 8, 22, 3, 'd' ) );
                foreach ( array( 8, 44, 80 ) as $x ) {
                    $rects[] = array( $x, 16, 32, 20, 'i' );
                    $rects[] = array( $x, 41, 14, 3, 'l' );
                    $rects[] = array( $x, 48, 30, 4, 'd' );
                    $rects[] = array( $x, 55, 22, 4, 'd' );
                }
                break;
            case 'promo':
                $rects = array( array( 8, 16, 18, 3, 'l' ), array( 8, 24, 46, 6, 'd' ), array( 8, 33, 34, 6, 'd' ), array( 8, 44, 40, 3, 'l' ), array( 8, 53, 16, 7, 'b' ), array( 28, 53, 16, 7, 'l' ), array( 62, 12, 50, 48, 'i' ) );
                break;
            default:
                $rects = array_merge( $col( 8 ), $col( 46 ), $col( 84 ) );
        }

        $fills = array( 'l' => '#c3c4c7', 'd' => '#50575e', 'i' => '#c7d2fe', 'b' => '#4f46e5' );
        echo '<svg viewBox="0 0 120 72" aria-hidden="true" focusable="false">';
        foreach ( $rects as $r ) {
            printf( '<rect x="%d" y="%d" width="%d" height="%d" rx="1.5" fill="%s"/>', (int) $r[0], (int) $r[1], (int) $r[2], (int) $r[3], esc_attr( $fills[ $r[4] ] ) );
        }
        echo '</svg>';
    }

    // the "Create panel" form posts back to this page; the new panel then opens in the Site Editor (see admin.js)
    public function create() {
        if ( ! isset( $_POST['almm_name'] ) ) {
            return;
        }
        if ( ! current_user_can( 'edit_theme_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to create mega menu panels.', 'softicon-mega-menu' ) );
        }
        check_admin_referer( 'almm_create_panel' );

        $title  = sanitize_text_field( wp_unslash( $_POST['almm_name'] ) );
        $design = isset( $_POST['almm_design'] ) ? sanitize_key( wp_unslash( $_POST['almm_design'] ) ) : '';
        if ( '' === $title ) {
            $title = __( 'Mega menu', 'softicon-mega-menu' );
        }

        $id = wp_insert_post( array(
            'post_type'    => 'wp_template_part',
            'post_status'  => 'publish',
            'post_title'   => $title,
            'post_name'    => sanitize_title( $title ),
            'post_content' => patterns::content( $design ),
        ), true );
        if ( is_wp_error( $id ) ) {
            wp_die( esc_html( $id->get_error_message() ) );
        }
        wp_set_object_terms( $id, get_stylesheet(), 'wp_theme' );
        wp_set_object_terms( $id, 'menu', 'wp_template_part_area' );

        $this->created = array( $title, block::edit_url( get_stylesheet() . '//' . get_post_field( 'post_name', $id ) ) );
    }

    public function render() {
        $block_theme = wp_is_block_theme();
        $panels      = $block_theme ? get_block_templates( array( 'area' => 'menu' ), 'wp_template_part' ) : array();
        ?>
        <div class="wrap almm-admin">
            <h1 class="wp-heading-inline"><?php esc_html_e( 'Mega Menus', 'softicon-mega-menu' ); ?></h1>
            <?php if ( $block_theme ) : ?>
                <a href="<?php echo esc_url( $this->header_url() ); ?>" class="page-title-action"><?php esc_html_e( 'Open header editor', 'softicon-mega-menu' ); ?></a>
            <?php endif; ?>
            <hr class="wp-header-end">

            <?php if ( $this->created ) : ?>
                <div class="notice notice-success"><p>
                    <?php
                    /* translators: %s: the new panel's name */
                    echo esc_html( sprintf( __( 'Panel "%s" created.', 'softicon-mega-menu' ), $this->created[0] ) );
                    ?>
                    <a class="almm-open" href="<?php echo esc_url( $this->created[1] ); ?>"><?php esc_html_e( 'Design it in the Site Editor', 'softicon-mega-menu' ); ?></a>
                </p></div>
            <?php endif; ?>

            <?php if ( ! $block_theme ) : ?>
                <div class="notice notice-warning inline"><p><?php esc_html_e( 'Mega menus work with block themes, such as Twenty Twenty-Five. Your current theme is a classic theme, which uses Appearance → Menus instead.', 'softicon-mega-menu' ); ?></p></div>
            <?php else : ?>

                <div class="card">
                    <h2 class="title"><?php esc_html_e( 'How to add a mega menu', 'softicon-mega-menu' ); ?></h2>
                    <ol class="almm-steps">
                        <li><?php esc_html_e( 'Pick a design below and create a panel. Change its text, links and images, or design it with any blocks.', 'softicon-mega-menu' ); ?></li>
                        <li><?php esc_html_e( 'Click "Open header editor", select the Navigation block, click its +, then "Add block", and choose Mega Menu.', 'softicon-mega-menu' ); ?></li>
                        <li><?php esc_html_e( 'Type the item\'s text, choose the panel in the block settings on the right and save.', 'softicon-mega-menu' ); ?></li>
                    </ol>

                    <div class="almm-videos">
                        <?php foreach ( $this->videos() as $video ) : ?>
                            <a class="almm-video" href="<?php echo esc_url( 'https://www.youtube.com/watch?v=' . $video['id'] ); ?>" target="_blank" rel="noopener noreferrer" data-video="<?php echo esc_attr( $video['id'] ); ?>" data-title="<?php echo esc_attr( $video['title'] ); ?>">
                                <svg viewBox="0 0 36 36" aria-hidden="true" focusable="false"><circle cx="18" cy="18" r="18" fill="#4f46e5"/><path d="M14.5 11.5v13l10-6.5z" fill="#fff"/></svg>
                                <span>
                                    <strong><?php echo esc_html( $video['title'] ); ?></strong>
                                    <small><?php echo esc_html( $video['text'] . ' · ' . $video['length'] ); ?></small>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <dialog class="almm-video-dialog" aria-label="<?php esc_attr_e( 'Video guide', 'softicon-mega-menu' ); ?>">
                    <button type="button" class="almm-video-close" aria-label="<?php esc_attr_e( 'Close video', 'softicon-mega-menu' ); ?>">&times;</button>
                    <div class="almm-video-frame"></div>
                </dialog>

                <h2><?php esc_html_e( 'Panels', 'softicon-mega-menu' ); ?></h2>

                <form class="almm-create" method="post" action="<?php echo esc_url( self::url() ); ?>">
                    <?php wp_nonce_field( 'almm_create_panel' ); ?>
                    <fieldset class="almm-designs">
                        <legend class="screen-reader-text"><?php esc_html_e( 'Design', 'softicon-mega-menu' ); ?></legend>
                        <?php foreach ( patterns::designs() as $key => $design ) : ?>
                            <label class="almm-design">
                                <input type="radio" name="almm_design" value="<?php echo esc_attr( $key ); ?>" <?php checked( 'links', $key ); ?>>
                                <?php $this->thumb( $key ); ?>
                                <strong><?php echo esc_html( $design['title'] ); ?></strong>
                                <small><?php echo esc_html( $design['text'] ); ?></small>
                            </label>
                        <?php endforeach; ?>
                    </fieldset>
                    <div class="almm-create-row">
                        <label class="screen-reader-text" for="almm-name"><?php esc_html_e( 'Panel name', 'softicon-mega-menu' ); ?></label>
                        <input type="text" id="almm-name" name="almm_name" placeholder="<?php esc_attr_e( 'For example: Shop menu', 'softicon-mega-menu' ); ?>">
                        <?php submit_button( __( 'Create panel', 'softicon-mega-menu' ), 'primary', 'submit', false ); ?>
                    </div>
                </form>

                <table class="wp-list-table widefat striped almm-panels">
                    <thead>
                        <tr>
                            <th scope="col"><?php esc_html_e( 'Panel', 'softicon-mega-menu' ); ?></th>
                            <th scope="col"><?php esc_html_e( 'Slug', 'softicon-mega-menu' ); ?></th>
                            <th scope="col"><span class="screen-reader-text"><?php esc_html_e( 'Actions', 'softicon-mega-menu' ); ?></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( ! $panels ) : ?>
                            <tr><td colspan="3"><?php esc_html_e( 'No panels yet. Create your first one above.', 'softicon-mega-menu' ); ?></td></tr>
                        <?php endif; ?>
                        <?php foreach ( $panels as $panel ) : ?>
                            <tr>
                                <td><strong><a href="<?php echo esc_url( block::edit_url( $panel->id ) ); ?>"><?php echo esc_html( $panel->title ? $panel->title : $panel->slug ); ?></a></strong></td>
                                <td><code><?php echo esc_html( $panel->slug ); ?></code></td>
                                <td><a class="button" href="<?php echo esc_url( block::edit_url( $panel->id ) ); ?>"><?php esc_html_e( 'Edit', 'softicon-mega-menu' ); ?></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p class="description"><?php esc_html_e( 'Panels are template parts of your theme. You can also find them in Appearance → Editor → Patterns → Mega Menu, where they can be renamed or deleted.', 'softicon-mega-menu' ); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
}
