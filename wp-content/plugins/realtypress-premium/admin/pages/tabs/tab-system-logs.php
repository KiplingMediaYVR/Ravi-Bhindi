<?php
/**
 * -------------------------------
 *  System :: Logs
 * -------------------------------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 */
?>

<h3 class="title rps-mt-40"><?php _e( 'RealtyPress Logs', 'realtypress-premium' ) ?></h3>

<table class="wp-list-table widefat" cellspacing="0">
    <thead>
    <tr>
        <th scope="col" id="name"><span><?php _e( 'Name', 'realtypress-premium' ); ?></span></th>
        <th scope="col" id="size" class="manage-column"><span><?php _e( 'Size', 'realtypress-premium' ); ?></span></th>
        <th scope="col" id="date-created" class="manage-column">
            <span><?php _e( 'Date Created', 'realtypress-premium' ); ?></span></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $logs = glob( REALTYPRESS_LOGS_PATH . "/*.txt" );

    rsort( $logs );
    $logs = array_slice( $logs, 0, 90 );

    foreach( $logs as $file ) {

        $file_url  = str_replace( REALTYPRESS_LOGS_PATH, REALTYPRESS_LOGS_URL, $file );
        $file_size = rps_human_filesize( filesize( $file ) ); ?>

        <tr>
            <td><a href="<?php echo $file_url; ?>" target=""><?php echo $file_url; ?></a></td>
            <td><?php echo $file_size; ?></td>
            <td><?php echo date( "F d Y H:i:s", filemtime( $file ) ); ?></td>
        </tr>

    <?php } ?>
    </tbody>
</table>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>