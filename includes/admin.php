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
            .almm-admin .almm-create { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin: 12px 0 16px; }
            .almm-admin .almm-create input[type=text] { width: 280px; max-width: 100%; }
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

    // the "Create panel" form posts back to this page; the new panel then opens in the Site Editor (see admin.js)
    public function create() {
        if ( ! isset( $_POST['almm_name'] ) ) {
            return;
        }
        if ( ! current_user_can( 'edit_theme_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to create mega menu panels.', 'softicon-mega-menu' ) );
        }
        check_admin_referer( 'almm_create_panel' );

        $title = sanitize_text_field( wp_unslash( $_POST['almm_name'] ) );
        if ( '' === $title ) {
            $title = __( 'Mega menu', 'softicon-mega-menu' );
        }

        $id = wp_insert_post( array(
            'post_type'    => 'wp_template_part',
            'post_status'  => 'publish',
            'post_title'   => $title,
            'post_name'    => sanitize_title( $title ),
            'post_content' => block::starter(),
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
                        <li><?php esc_html_e( 'Create a panel below and design it with any blocks: columns of links, images, buttons.', 'softicon-mega-menu' ); ?></li>
                        <li><?php esc_html_e( 'Click "Open header editor", select the Navigation block, click its + and add a Mega Menu item.', 'softicon-mega-menu' ); ?></li>
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
                    <label class="screen-reader-text" for="almm-name"><?php esc_html_e( 'Panel name', 'softicon-mega-menu' ); ?></label>
                    <input type="text" id="almm-name" name="almm_name" placeholder="<?php esc_attr_e( 'For example: Shop menu', 'softicon-mega-menu' ); ?>">
                    <?php submit_button( __( 'Create panel', 'softicon-mega-menu' ), 'primary', 'submit', false ); ?>
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
