<?php
/**
 * --------------------------
 *  Support :: Documentation
 * --------------------------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 */
?>
<style>
    #realtypress-docs {
        border: 1px solid #ccc;
        margin-top: 20px;
        width: 100%;
        height: 100%;
        min-height: 600px;
        overflow: scroll;
    }
</style>
<h3 class="rps-mt-40"><?php _e( 'Documentation', 'realtypress-premium' ) ?></h3>
<p style="margin-bottom:0;">
    <a href="<?php echo REALTYPRESS_PLUGIN_DOCS ?>" target="_blank" class="button button-primary">Open in a New Window</a>
</p>
<iframe id="realtypress-docs" src="<?php echo REALTYPRESS_PLUGIN_DOCS ?>"></iframe>
