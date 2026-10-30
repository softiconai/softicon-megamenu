<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$almm_label = wp_kses_post( $attributes['label'] ?? '' );
if ( '' === trim( wp_strip_all_tags( $almm_label ) ) ) {
    return;
}

$almm_url   = esc_url( $attributes['url'] ?? '' );
$almm_panel = ALMM_Mega_Menu\block::panel( $attributes['menuSlug'] ?? '' );

// no panel chosen yet, or it was deleted: a plain menu link, or nothing
if ( '' === $almm_panel ) {
    if ( $almm_url ) {
        printf(
            '<li %1$s><a class="wp-block-navigation-item__content" href="%2$s"><span class="wp-block-navigation-item__label">%3$s</span></a></li>',
            get_block_wrapper_attributes( array( 'class' => 'wp-block-navigation-item' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes
            $almm_url, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url() above
            $almm_label // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post() above
        );
    }
    return;
}

$almm_config = ALMM_Mega_Menu\block::config( $attributes );
$almm_id     = wp_unique_id( 'almm-panel-' );

$almm_classes = array( 'wp-block-navigation-item', 'almm_item' );
if ( ! isset( $attributes['shadow'] ) || $attributes['shadow'] ) {
    $almm_classes[] = 'almm_shadow';
}
if ( 'link' === $almm_config['mobile'] ) {
    $almm_classes[] = 'almm_mobile_link';
}

$almm_wrapper = get_block_wrapper_attributes( array(
    'class'     => implode( ' ', $almm_classes ),
    'style'     => ALMM_Mega_Menu\block::style( $attributes, $almm_config ),
    // hex flags keep quotes and ampersands from breaking the attribute or JSON.parse
    'data-almm' => wp_json_encode( $almm_config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ),
) );

$almm_arrow = '<span class="almm_arrow" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" focusable="false"><path d="M1.5 4L6 8l4.5-4" stroke="currentColor" stroke-width="1.5"></path></svg></span>';
?>
<li <?php echo $almm_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() escapes ?>>
    <span class="almm_head">
        <?php if ( $almm_url ) : ?>
            <a class="wp-block-navigation-item__content almm_link" href="<?php echo $almm_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url() above ?>"><span class="wp-block-navigation-item__label"><?php echo $almm_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post() above ?></span></a>
            <?php /* translators: %s: the menu item's label */ ?>
            <button type="button" class="almm_toggle almm_toggle_arrow" aria-expanded="false" aria-controls="<?php echo esc_attr( $almm_id ); ?>" aria-label="<?php echo esc_attr( sprintf( __( '%s submenu', 'softicon-mega-menu' ), wp_strip_all_tags( $almm_label ) ) ); ?>"><?php echo $almm_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup ?></button>
        <?php else : ?>
            <button type="button" class="wp-block-navigation-item__content almm_toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $almm_id ); ?>"><span class="wp-block-navigation-item__label"><?php echo $almm_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post() above ?></span><?php echo $almm_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup ?></button>
        <?php endif; ?>
    </span>
    <div class="almm_panel" id="<?php echo esc_attr( $almm_id ); ?>">
        <?php echo $almm_panel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered blocks ?>
    </div>
</li>
