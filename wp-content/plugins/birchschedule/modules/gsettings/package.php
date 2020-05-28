<?php

birch_ns( 'appointer.gsettings', function( $ns ) {

        $update_info = array();

        birch_defn( $ns, 'init', function() use( $ns ) {

                add_action( 'init', array( $ns, 'wp_init' ) );

                add_action( 'admin_init', array( $ns, 'wp_admin_init' ) );

            } );

        birch_defn( $ns, 'wp_init', function() use ( $ns ) {

                add_action( 'appointer_view_show_notice', array( $ns, 'show_update_notice' ) );

                add_filter( 'site_transient_update_plugins', array( $ns, 'get_update_info' ), 20 );

                add_filter( 'appointer_view_settings_get_tabs', array( $ns, 'add_tab' ) );

                add_filter( 'appointer_model_get_currency_code', array( $ns, 'get_option_currency' ) );

                add_filter( 'appointer_view_calendar_get_default_view',
                    array( $ns, 'get_option_default_calendar_view' ) );

            } );

        birch_defn( $ns, 'wp_admin_init', function() use ( $ns ) {
                register_setting( 'appointer_options', 'appointer_options', array( $ns, 'sanitize_input' ) );
                $ns->add_settings_sections();
            } );

        birch_defn( $ns, 'add_tab', function( $tabs ) use ( $ns ) {
                $tabs['general'] = array(
                    'title' => __( 'General', 'appointer' ),
                    'action' => array( $ns, 'render_page' ),
                    'order' => 0
                );

                return $tabs;
            } );

        birch_defn( $ns, 'add_settings_sections', function() use ( $ns ) {
                add_settings_section( 'appointer_general', __( 'General Options', 'appointer' ),
                    array( $ns, 'render_section_general' ), 'appointer_settings' );
                $ns->add_settings_fields();
            } );

        birch_defn( $ns, 'add_settings_fields', function() use ( $ns ) {
                add_settings_field( 'appointer_timezone', __( 'Timezone' ),
                    array( $ns, 'render_timezone' ), 'appointer_settings', 'appointer_general' );

                add_settings_field( 'appointer_date_time_format', __( 'Date Format, Time Format', 'appointer' ),
                    array( $ns, 'render_date_time_format' ), 'appointer_settings', 'appointer_general' );

                add_settings_field( 'appointer_start_of_week', __( 'Week Starts On', 'appointer' ),
                    array( $ns, 'render_start_of_week' ), 'appointer_settings', 'appointer_general' );

                add_settings_field( 'appointer_currency', __( 'Currency', 'appointer' ),
                    array( $ns, 'render_currency' ), 'appointer_settings', 'appointer_general' );

                add_settings_field( 'appointer_default_calendar_view', __( 'Default Calendar View', 'appointer' ),
                    array( $ns, 'render_default_calendar_view' ), 'appointer_settings', 'appointer_general' );

            } );

        birch_defn( $ns, 'get_option_currency', function() use ( $ns ) {
                $options = $ns->get_options();
                return $options['currency'];
            } );

        birch_defn( $ns, 'get_option_default_calendar_view', function() use ( $ns ) {
                $options = $ns->get_options();
                return $options['default_calendar_view'];
            } );

        birch_defn( $ns, 'render_section_general', function() {
                echo '';
            } );

        birch_defn( $ns, 'get_options', function() use ( $ns ) {
                $options = get_option( 'appointer_options' );
                return $options;
            } );

        birch_defn( $ns, 'render_timezone', function() {
                $timezone_url = admin_url( 'options-general.php' );
                echo sprintf(
                    __( "<label>Timezone settings are located <a href='%s'>here</a>.</label>", 'appointer' ),
                    $timezone_url );
            } );

        birch_defn( $ns, 'render_date_time_format', function() {
                $timezone_url = admin_url( 'options-general.php' );
                echo sprintf(
                    __( "<label>Date format, time format settings are located <a href='%s'>here</a>.</label>", 'appointer' ),
                    $timezone_url );
            } );

        birch_defn( $ns, 'render_start_of_week', function() {
                $timezone_url = admin_url( 'options-general.php' );
                echo sprintf(
                    __( "<label>First day of week setting is located <a href='%s'>here</a>.</label>", 'appointer' ),
                    $timezone_url );
            } );

        birch_defn( $ns, 'map_currencies', function( $currency ) {
                if ( $currency['symbol_right'] != '' ) {
                    return $currency['title'] . ' (' . $currency['symbol_right'] . ')';
                } else {
                    return $currency['title'] . ' (' . $currency['symbol_left'] . ')';
                }
            } );

        birch_defn( $ns, 'render_currency', function() use ( $ns ) {
                global $birchpress;

                $currencies = $birchpress->util->get_currencies();
                $currencies = array_map( array( $ns, 'map_currencies' ), $currencies );
                $currency = $ns->get_option_currency();
                echo '<select id="appointer_currency" name="appointer_options[currency]">';
                $birchpress->util->render_html_options( $currencies, $currency );
                echo '</select>';
            } );

        birch_defn( $ns, 'render_default_calendar_view', function() use ( $ns ) {
                global $birchpress;

                $views = $birchpress->util->get_calendar_views();
                $default_view = $ns->get_option_default_calendar_view();
                echo '<select id="appointer_default_calenar_view" name="appointer_options[default_calendar_view]">';
                $birchpress->util->render_html_options( $views, $default_view );
                echo '</select>';
            } );

        birch_defn( $ns, 'render_page', function() use ( $ns ) {
                $options = $ns->get_options();
                $version = $options['version'];
                settings_errors();
?>
                <form action="options.php" method="post">
                    <input type='hidden' name='appointer_options[version]' value='<?php echo $version; ?>'>
                    <?php settings_fields( 'appointer_options' ); ?>
                    <?php do_settings_sections( 'appointer_settings' ); ?>
                    <p class="submit">
                        <input name="Submit" type="submit" class="button-primary"
                               value="<?php _e( 'Save changes', 'appointer' ); ?>" />
                    </p>
                </form>
<?php
            } );

        birch_defn( $ns, 'sanitize_input', function( $input ) {
                return $input;
            } );

        birch_defn( $ns, 'get_update_info', function( $checked_data ) use ( &$update_info ) {
                $plugin_slug = "appointer";
                $slug_str = $plugin_slug . '/' . $plugin_slug . '.php';
                if ( isset( $checked_data->response[$slug_str] ) ) {
                    $update_info = $checked_data->response[$slug_str];
                    $update_info = array(
                        'version' => $update_info->new_version
                    );
                }
                return $checked_data;
            } );

        birch_defn( $ns, 'show_update_notice', function() use ( &$update_info ) {
                global $appointer;

                $product_name = $appointer->get_product_name();
                $update_url = admin_url( 'update-core.php' );
                $update_text = "%s %s is available! <a href='$update_url'>Please update now</a>.";
                if ( $update_info ):
?>
                <div class="updated inline">
                    <p><?php echo sprintf( $update_text, $product_name, $update_info['version'] ); ?></p>
                </div>
<?php
                endif;
            } );

    } );
