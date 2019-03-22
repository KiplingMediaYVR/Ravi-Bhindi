<?php
/**
 * -------------------------------
 *  CREA DDF Data :: Sync Settings
 * -------------------------------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 */

/**
 * Actions
 * =======
 */
if( ( ! empty( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true && get_option( 'rps-ddf-cron-type' ) == 'wordpress' ) ||
    ( ! empty( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true && get_option( 'rps-ddf-cron-type' ) == 'unix' ) ) {

    $schedule = get_option( 'rps-ddf-cron-schedule', 'daily' );

    // WordPress Cron
    wp_clear_scheduled_hook( 'realtypress_ddf_cron' );

    if( ! array_key_exists( 'realtypress_cron', wp_get_schedules() ) ) {

        // Default to daily if custom realtypress_cron schedule cannot be found
        $set_schedule = 'daily';
    }
    elseif( $schedule == 'hourly' ||
        $schedule == 'twicedaily' ||
        $schedule == 'daily' ) {

        // WordPress default crons.  Some hosting providers don't seem to like custom schedules and they're unscheduled after each run
        $set_schedule = $schedule;
    }
    elseif( $schedule == '86400' ||
        $schedule == '43200' ||
        $schedule == '21600' ||
        $schedule == '10800' ||
        $schedule == '7200' ||
        $schedule == '3600' ) {

        // Custom RealtyPress cron schedule
        $set_schedule = 'realtypress_cron';
    }
    else {

        // Default daily
        $set_schedule = 'daily';
    }

    wp_schedule_event( time() + 600, $set_schedule, 'realtypress_ddf_cron' );
}
elseif( ! empty( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true && get_option( 'rps-ddf-cron-type' ) == 'unix-cron' ) {

    // Unix Cron
    wp_clear_scheduled_hook( 'realtypress_ddf_cron' );

}

/**
 * Notices
 * =======
 */
if( get_option( 'rps-ddf-sync-enabled', false ) == false ) {
    ?>

    <!-- WordPress Cron -->
    <div class="updated">
        <h3 class="title"><?php _e( 'DDF&reg; Cron Sync Disabled', 'realtypress-premium' ) ?></h3>
        <p class="rps-text-red">
            <strong><?php _e( 'DDF&reg; Cron Sync is currently disabled.', 'realtypress-premium' ) ?></strong></p>
    </div>

    <?php
}
elseif( get_option( 'rps-ddf-cron-type' ) == 'wordpress' ) {
    ?>

    <!-- WordPress Cron -->
    <div class="updated">
        <h3 class="title"><?php _e( 'WordPress Cron (WP-CRON)', 'realtypress-premium' ) ?></h3>
        <p><?php _e( 'You are currently using WordPress Cron to trigger DDF&reg; syncs.', 'realtypress-premium' ) ?></p>
    </div>

    <?php
}
elseif( get_option( 'rps-ddf-cron-type' ) == 'unix' ) {
    ?>

    <!-- Alternative Unix WordPress Cron -->
    <div class="updated">
        <h3 class="title"><?php _e( 'Unix WordPress Cron', 'realtypress-premium' ) ?></h3>
        <?php _e( 'Use the command below when creating your unix cron task to trigger the WP-CRON.<br>This command below works in most cases but servers vary widely, <strong>you may need to adjust to suit your needs</strong>.', 'realtypress-premium' ) ?>
        <p class="unix-cron-command-notice"><strong
                    class="rps-text-red"><?php echo 'wget -q -O - ' . get_site_url() . '/wp-cron.php?doing_wp_cron >/dev/null 2>&1' ?></strong>
        </p>
        <p>Unix cron <strong>acts as a trigger to initiate a WP-CRON Sync</strong>.<br>Some users prefer this method
            rather than relying on user visits to trigger the WP-Cron</p>
    </div>

    <?php
}
elseif( get_option( 'rps-ddf-cron-type' ) == 'unix-cron' ) { ?>

    <!-- Unix Cron -->
    <div class="updated">
        <h3 class="title"><?php _e( 'Unix CRON', 'realtypress-premium' ) ?></h3>
        <?php _e( 'When creating your cron use the command below.<br>This command below works in most cases but servers vary widely, <strong>you may need to adjust to suit your needs.</strong>', 'realtypress-premium' ) ?>
        <p class="unix-cron-command-notice"><strong class="rps-text-red">php -d
                memory_limit=256M <?php echo REALTYPRESS_ADMIN_PATH; ?>/cron/unix-cron.php >/dev/null 2>&amp;1</strong>
        </p>
        <p>Unix cron utilizes it's own schedule that has been set when the cron job was created.<br>Unix cron <strong>does
                not use WP-CRON</strong> and is not limited by timeouts, or relying on user visits to trigger a sync as
            WP-CRON is.<br><br><strong>If you would like to use this option but do not know how to setup a cron job,
                please contact your hosting company. They may help you get your cron setup on your account, or can at
                least point you in the direction of documentation outlining how to.</strong>.<br><br>The above memory
            limit set is for a shared server with 512MB of RAM, if your server has more resources than this raise the
            memory limit to approx. 50% of your available memory.</p>
    </div>

<?php } ?>

<form method="post" action="options.php" class="rps-mt-40">
    <?php settings_fields( 'rps_ddf_sync_options' ); ?>
    <?php do_settings_sections( 'rps_ddf_sync_options' ); ?>
    <p><?php // _e( 'We recommend using the WordPress CRON method only if you have less than 500 listings and at least one visitor to your site every 24 hours.', 'realtypress-premium') ?></p>
    <p><?php // _e( 'We recommend using the Unix CRON method over WP-CRON in all cases but not all users are comfortable with, .', 'realtypress-premium') ?></p>
    <?php submit_button(); ?>
</form>

<h3 class="title rps-mt-40"><?php _e( 'RealtyPress CRON Schedule', 'realtypress-premium' ) ?></h3>

<p><strong><?php _e( 'IMPORTANT - How WordPress CRON Works:', 'realtypress-premium' ) ?></strong></p>

<p><?php _e( 'Every time a page is loaded in the wordpress admin or on the front-end of your site, WordPress triggers the CRON function. This function checks for any RealtyPress scheduled tasks and runs them if they are due to be run.  The possible issue here is that if your site is not visited within a 24 hours period than wordpress cannot trigger the CRON function and will not check is scheduled tasks need to be run.  This is why we only recommend WordPress CRON if you have more than one visitor per day.', 'realtypress-premium' ) ?></p>

<p class="rps-text-red"><strong>Current Date Time: <?php echo date( 'Y-m-d H:i:s' ); ?></strong></p>

<table class="wp-list-table widefat" cellspacing="0">
    <thead>
    <tr>
        <th scope="col" id="job" class="manage-column column-job">
            <span><?php _e( 'Job', 'realtypress-premium' ); ?></span></th>
        <th scope="col" id="hook" class="manage-column column-hook">
            <span><?php _e( 'Hook', 'realtypress-premium' ); ?></span></th>
        <th scope="col" id="schedule" class="manage-column column-schedule">
            <span><?php _e( 'Schedule', 'realtypress-premium' ); ?></span></th>
        <th scope="col" id="interval" class="manage-column column-interval">
            <span><?php _e( 'Interval', 'realtypress-premium' ); ?></span></th>
        <th scope="col" id="last-run" class="manage-column column-last-run">
            <span><?php _e( 'Last Run', 'realtypress-premium' ); ?></span></th>
        <th scope="col" id="next-run" class="manage-column column-next-run">
            <span><?php _e( 'Next Run', 'realtypress-premium' ); ?></span></th>
    </tr>
    </thead>
    <tbody>
    <?php $wp_cron = _get_cron_array(); ?>
    <?php foreach( $wp_cron as $scheduled ) { ?>
        <?php foreach( $scheduled as $hook => $job ) { ?>
            <?php foreach( $job as $timeslot ) { ?>
                
                <?php if( $hook == 'realtypress_ddf_cron' ) { ?>
                    
                    <tr class="alternate">
                        <td><strong>DDF&reg; <?php _e( 'Listing Data', 'realtypress-premium' ) ?></strong></td>
                        <td><?php echo $hook ?></td>
                        <td><?php echo $timeslot['schedule']; ?></td>
                        <td>
                            <?php
                                if( is_numeric( $timeslot['interval'] ) ) {
                                    echo ( ( $timeslot['interval'] / 60 ) / 60 ) . ' ' . __( 'hours', 'realtypress-premium' );
                                }
                                else {
                                    echo $timeslot['interval'];
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                            $start_time = get_option( 'rps-cron-start-time', '' );
                            if( ! empty( $start_time ) ) {
                                echo $start_time;
                            }
                            else {
                                _e( 'Not Run', 'realtypress-premium' );
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $timestamp = wp_next_scheduled( $hook );
                            echo date( 'Y-m-d H:i:s', $timestamp );
                            ?>
                        </td>
                    </tr>
                <?php } ?>

            <?php } ?>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>