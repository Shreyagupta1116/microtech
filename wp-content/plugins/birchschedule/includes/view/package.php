<?php

birch_ns( 'appointer.view', function( $ns ) {

        $_ns_data = new stdClass();

        birch_defn( $ns, 'init', function() use ( $ns, $_ns_data ) {
            
                global $appointer;

                $_ns_data->page_hooks = array();

                add_action( 'init', array( $ns, 'wp_init' ) );

                add_action( 'admin_init', array( $ns, 'wp_admin_init' ) );

                add_action( 'admin_menu', array( $ns, 'create_admin_menus' ) );

                add_action( 'custom_menu_order', array( $ns, 'if_change_custom_menu_order' ), 100 );

                add_action( 'menu_order', array( $ns, 'change_admin_menu_order' ), 100 );

                add_action( 'plugins_loaded', array( $ns, 'load_i18n' ) );

                add_action( 'birchpress_view_load_post_edit_after', function( $arg ) use ( $ns ) {
                        $ns->load_page_edit( $arg );
                        $ns->load_post_edit( $arg );
                    } );

                add_action( 'birchpress_view_load_post_new_after', function( $arg ) use ( $ns ) {
                        $ns->load_page_edit( $arg );
                        $ns->load_post_new( $arg );
                    } );

                add_action( 'birchpress_view_enqueue_scripts_post_new_after', function( $arg ) use ( $ns ) {
                        $ns->enqueue_scripts_post_new( $arg );
                        $ns->enqueue_scripts_edit( $arg );
                    } );

                add_action( 'birchpress_view_enqueue_scripts_post_edit_after', function( $arg ) use ( $ns ) {
                        $ns->enqueue_scripts_post_edit( $arg );
                        $ns->enqueue_scripts_edit( $arg );
                    } );

                add_action( 'birchpress_view_enqueue_scripts_post_list_after', function( $arg ) use ( $ns ) {
                        $ns->enqueue_scripts_list( $arg );
                    } );

                add_action( 'birchpress_view_save_post_after', function( $post_a ) use ( $ns ) {
                        $ns->save_post( $post_a );
                    } );

                add_filter( 'birchpress_view_pre_save_post',
                    function( $post_data, $post_data2, $post_attr ) use ( $ns ) {
                        return $ns->pre_save_post( $post_data, $post_attr );
                    }, 20, 3 );
            } );

        birch_defn( $ns, 'wp_init', function() use ( $ns ) {
                if ( !defined( 'DOING_AJAX' ) ) {
                    $ns->register_common_scripts();
                    $ns->register_common_styles();
                    $ns->register_common_scripts_data_fns();
                }
            } );

        birch_defn( $ns, 'wp_admin_init', function() {} );

        birch_defn( $ns, 'get_post_type_lookup_config', function() {
                return array(
                    'key' => 'post_type',
                    'lookup_table' => array()
                );
            } );

        birch_defmulti( $ns, 'enqueue_scripts_post_edit', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'enqueue_scripts_post_new', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'enqueue_scripts_edit', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'enqueue_scripts_list', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'load_page_edit', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'load_post_edit', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'load_post_new', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'save_post', $ns->get_post_type_lookup_config, function( $arg ) {} );

        birch_defmulti( $ns, 'pre_save_post', $ns->get_post_type_lookup_config, function( $post_data, $post_attr ) {
                return $post_data;
            } );

        birch_defn( $ns, 'get_current_post_type', function() {
                global $birchpress;

                return $birchpress->view->get_current_post_type();
            } );

        birch_defn( $ns, 'enqueue_scripts', function( $scripts ) {
                global $birchpress;

                $birchpress->view->enqueue_scripts( $scripts );
            } );

        birch_defn( $ns, 'enqueue_styles', function( $styles ) {
                global $birchpress;

                $birchpress->view->enqueue_styles( $styles );
            } );

        birch_defn( $ns, 'merge_request', function( $model, $config, $request ) {
                global $appointer;

                return $appointer->model->merge_data( $model, $config, $request );
            } );

        birch_defn( $ns, 'apply_currency_to_label', function( $label, $currency_code ) {
                global $birchpress, $appointer;

                $currencies = $birchpress->util->get_currencies();
                $currency = $currencies[$currency_code];
                $symbol = $currency['symbol_right'];
                if ( $symbol == '' ) {
                    $symbol = $currency['symbol_left'];
                }
                return $label = $label . ' (' . $symbol . ')';
            } );

        birch_defn( $ns, 'render_errors', function() use ( $ns ) {
                $errors = $ns->get_errors();
                if ( $errors && sizeof( $errors ) > 0 ) {
                    echo '<div id="appointer_errors" class="error fade">';
                    foreach ( $errors as $error ) {
                        echo '<p>' . $error . '</p>';
                    }
                    echo '</div>';
                    update_option( 'appointer_errors', '' );
                }
            } );

        birch_defn( $ns, 'get_errors', function() {
                return get_option( 'appointer_errors' );
            } );

        birch_defn( $ns, 'has_errors', function() use ( $ns ) {
                $errors = $ns->get_errors();
                if ( $errors && sizeof( $errors ) > 0 ) {
                    return true;
                } else {
                    return false;
                }
            } );

        birch_defn( $ns, 'save_errors', function( $errors ) {
                update_option( 'appointer_errors', $errors );
            } );

        birch_defn( $ns, 'get_screen', function( $hook_name ) {
                global $birchpress;

                return $birchpress->view->get_screen( $hook_name );
            } );

        birch_defn( $ns, 'show_notice', function() {} );

        birch_defn( $ns, 'add_page_hook', function( $key, $hook ) use ( $ns, $_ns_data ) {
                $_ns_data->page_hooks[$key] = $hook;
            } );

        birch_defn( $ns, 'get_page_hook', function( $key ) use ( $ns, $_ns_data ) {
                if ( isset( $_ns_data->page_hooks[$key] ) ) {
                    return $_ns_data->page_hooks[$key];
                } else {
                    return '';
                }
            } );

        birch_defn( $ns, 'get_custom_code_css', function( $shortcode ) {
                return '';
            } );

        birch_defn( $ns, 'get_shortcodes', function() {
                return array();
            } );

        birch_defn( $ns, 'get_languages_dir', function() {
                return 'appointer/languages';
            } );

        birch_defn( $ns, 'load_i18n', function() use ( $ns ) {
                $lan_dir = $ns->get_languages_dir();
                load_plugin_textdomain( 'appointer', false, $lan_dir );
            } );

        birch_defn( $ns, 'create_admin_menus', function() use ( $ns ) {
                $ns->create_menu_scheduler();
                $ns->reorder_submenus();
            } );

        birch_defn( $ns, 'if_change_custom_menu_order', function() {
                return true;
            } );

        birch_defn( $ns, 'change_admin_menu_order', function( $menu_order ) {

                $custom_menu_order = array();

                $client_menu = array_search( 'edit.php?post_type=birs_client', $menu_order );

                foreach ( $menu_order as $index => $item ) {

                    if ( ( ( 'edit.php?post_type=birs_appointment' ) == $item ) ) {
                        $custom_menu_order[] = $item;
                        $custom_menu_order[] = 'edit.php?post_type=birs_client';
                        unset( $menu_order[$client_menu] );
                    } else {
                        if ( 'edit.php?post_type=birs_client' != $item )
                        $custom_menu_order[] = $item;
                    }
                }

                return $custom_menu_order;
            } );

        birch_defn( $ns, 'create_menu_scheduler', function() use ( $ns ) {
                $page_hook_calendar =
                add_submenu_page( 'edit.php?post_type=birs_appointment', __( 'Calendar', 'appointer' ),
                    __( 'Calendar', 'appointer' ), 'edit_birs_appointments', 'appointer_calendar',
                    array( $ns, 'render_calendar_page' ) );
                $ns->add_page_hook( 'calendar', $page_hook_calendar );

                $page_hook_settings =
                add_submenu_page( 'edit.php?post_type=birs_appointment',
                    __( 'Appointer Settings', 'appointer' ),
                    __( 'Settings', 'appointer' ), 'manage_birs_settings',
                    'appointer_settings', array( $ns, 'render_settings_page' ) );
                $ns->add_page_hook( 'settings', $page_hook_settings );

                $page_hook_help = add_submenu_page( 'edit.php?post_type=birs_appointment',
                    __( 'Help', 'appointer' ), __( 'Help', 'appointer' ),
                    'read', 'appointer_help', array( $ns, 'render_help_page' ) );
                $ns->add_page_hook( 'help', $page_hook_help );
            } );

        birch_defn( $ns, 'render_calendar_page', function() {} );

        birch_defn( $ns, 'render_settings_page', function() {} );

        birch_defn( $ns, 'render_help_page', function() {} );

        birch_defn( $ns, 'reorder_submenus', function() use ( $ns ) {
                global $submenu;

                $sub_items = &$submenu['edit.php?post_type=birs_appointment'];
                $location = $ns->get_submenu( $sub_items, 'location' );
                $staff = $ns->get_submenu( $sub_items, 'staff' );
                $service = $ns->get_submenu( $sub_items, 'service' );
                $settings = $ns->get_submenu( $sub_items, 'settings' );
                $help = $ns->get_submenu( $sub_items, 'help' );
                $calendar = $ns->get_submenu( $sub_items, 'calendar' );
                $new_appointment = $ns->get_submenu( $sub_items, 'post-new.php?post_type=birs_appointment' );

                $sub_items = array(
                    $calendar,
                    $new_appointment,
                    $location,
                    $staff,
                    $service,
                    $settings,
                    $help
                );
            } );

        birch_defn( $ns, 'get_submenu', function( $submenus, $name ) use ( $ns ) {
                foreach ( $submenus as $submenu ) {
                    $pos = strpos( $submenu[2], $name );
                    if ( $pos || $pos === 0 ) {
                        return $submenu;
                    }
                }
                return false;
            } );

        birch_defn( $ns, 'register_script_data_fn', function( $handle, $data_name, $fn ) {
                global $birchpress;

                $birchpress->view->register_script_data_fn( $handle, $data_name, $fn );
            } );

        birch_defn( $ns, 'get_admin_i18n_messages', function() {
                global $appointer;
                return $appointer->view->get_frontend_i18n_messages();
            } );

        birch_defn( $ns, 'get_frontend_i18n_messages', function() {
                return array(
                    'Loading...' => __( 'Loading...', 'appointer' ),
                    'Loading appointments...' => __( 'Loading appointments...', 'appointer' ),
                    'Saving...' => __( 'Saving...', 'appointer' ),
                    'Save' => __( 'Save', 'appointer' ),
                    'Please wait...' => __( 'Please wait...', 'appointer' ),
                    'Schedule' => __( 'Schedule', 'appointer' ),
                    'Are you sure you want to cancel this appointment?' => __( 'Are you sure you want to cancel this appointment?', 'appointer' ),
                    'Your appointment has been cancelled successfully.' => __( 'Your appointment has been cancelled successfully.', 'appointer' ),
                    "The appointment doesn't exist or has been cancelled." => __( "The appointment doesn't exist or has been cancelled.", 'appointer' ),
                    'Your appointment has been rescheduled successfully.' => __( 'Your appointment has been rescheduled successfully.', 'appointer' ),
                    'Your appointment can not be cancelled now according to our booking policies.' => __( 'Your appointment can not be cancelled now according to our booking policies.', 'appointer' ),
                    'Your appointment can not be rescheduled now according to our booking policies.' => __( 'Your appointment can not be rescheduled now according to our booking policies.', 'appointer' ),
                    'There are no available times.' => __( 'There are no available times.', 'appointer' ),
                    '(Deposit)' => __( '(Deposit)', 'appointer' ),
                    'Reschedule' => __( 'Reschedule', 'appointer' ),
                    'Change' => __( 'Change', 'appointer' ),
                    'No Preference' => __( 'No Preference', 'appointer' ),
                    'All Locations' => __( 'All Locations', 'appointer' ),
                    'All Providers' => __( 'All Providers', 'appointer' )
                );
            } );

        birch_defn( $ns, 'render_ajax_success_message', function( $success ) {
                global $birchpress;

                $birchpress->view->render_ajax_success_message( $success );
            } );

        birch_defn( $ns, 'render_ajax_error_messages', function( $errors ) {
                global $birchpress;

                $birchpress->view->render_ajax_error_messages( $errors );
            } );

        birch_defn( $ns, 'get_query_array', function( $query, $keys ) {
                global $birchpress;
                return $birchpress->view->get_query_array( $query, $keys );
            } );

        birch_defn( $ns, 'get_query_string', function( $query, $keys ) {
                global $birchpress;

                return $birchpress->view->get_query_string( $query, $keys );
            } );

        birch_defn( $ns, 'get_script_data_fn_model', function() {
                global $appointer, $birchpress;
                return array(
                    'admin_url' => admin_url(),
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'all_schedule' => $appointer->model->schedule->get_all_calculated_schedule(),
                    'all_daysoff' => $appointer->model->get_all_daysoff(),
                    'gmt_offset' => $birchpress->util->get_gmt_offset(),
                    'future_time' => $appointer->model->get_future_time(),
                    'cut_off_time' => $appointer->model->get_cut_off_time(),
                    'fully_booked_days' => $appointer->model->schedule->get_fully_booked_days()
                );
            } );

        birch_defn( $ns, 'get_script_data_fn_view', function() use ( $ns ) {
                global $birchpress, $appointer;

                return array(
                    'datepicker_i18n_options' => $birchpress->util->get_datepicker_i18n_params(),
                    'fc_i18n_options' => $birchpress->util->get_fullcalendar_i18n_params(),
                    'i18n_messages' => $ns->get_frontend_i18n_messages(),
                    'i18n_countries' => $birchpress->util->get_countries(),
                    'i18n_states' => $birchpress->util->get_states(),
                    'plugin_url' => $appointer->plugin_url()
                );
            } );

        birch_defn( $ns, 'get_script_data_fn_admincommon', function() use ( $ns ) {
                return array(
                    'i18n_messages' => $ns->get_admin_i18n_messages()
                );
            } );

        birch_defn( $ns, 'register_common_scripts_data_fns', function() use ( $ns ) {
                $ns->register_script_data_fn( 'appointer_model', 'appointer_model',
                    array( $ns, 'get_script_data_fn_model' ) );
                $ns->register_script_data_fn( 'appointer_view', 'appointer_view',
                    array( $ns, 'get_script_data_fn_view' ) );
                $ns->register_script_data_fn( 'appointer_view_admincommon', 'appointer_view_admincommon',
                    array( $ns, 'get_script_data_fn_admincommon' ) );
            } );

        birch_defn( $ns, 'register_3rd_scripts', function() {
                global $birchpress, $appointer;

                $version = $appointer->get_product_version();

                $birchpress->view->register_3rd_scripts();
                wp_register_script( 'moment',
                    $appointer->plugin_url() . '/lib/assets/js/moment/moment.min.js',
                    array(), '1.7.0' );

                wp_register_script( 'jgrowl',
                    $appointer->plugin_url() . '/lib/assets/js/jgrowl/jquery.jgrowl.js',
                    array( 'jquery' ), '1.4.0' );

                wp_register_script( 'jscolor',
                    $appointer->plugin_url() . '/lib/assets/js/jscolor/jscolor.js',
                    array(), '1.4.0' );

                wp_register_script( 'bootstrap',
                    $appointer->plugin_url() . '/lib/assets/js/bootstrap/js/bootstrap.js',
                    array( 'jquery' ), '3.0.3' );

                wp_deregister_script( 'select2' );
                wp_register_script( 'select2',
                    $appointer->plugin_url() . '/lib/assets/js/select2/select2.min.js',
                    array( 'jquery' ), '3.4.2' );

                wp_register_script( 'fullcalendar_birchpress',
                    $appointer->plugin_url() . '/lib/assets/js/fullcalendar/fullcalendar_birchpress.js',
                    array( 'jquery-ui-draggable', 'jquery-ui-resizable',
                        'jquery-ui-dialog', 'jquery-ui-datepicker',
                        'jquery-ui-tabs', 'jquery-ui-autocomplete' ), '1.6.4' );

                wp_register_script( 'filedownload_birchpress',
                    $appointer->plugin_url() . '/lib/assets/js/filedownload/jquery.fileDownload.js',
                    array( 'jquery' ), '1.4.0' );
            } );

        birch_defn( $ns, 'register_3rd_styles', function() {
                global $appointer;

                $version = $appointer->get_product_version();

                wp_register_style( 'fullcalendar_birchpress',
                    $appointer->plugin_url() . '/lib/assets/js/fullcalendar/fullcalendar.css',
                    array(), '1.5.4' );

                wp_register_style( 'jquery-ui-bootstrap',
                    $appointer->plugin_url() . '/lib/assets/css/jquery-ui-bootstrap/jquery-ui-1.9.2.custom.css',
                    array(), '0.22' );
                wp_register_style( 'jquery-ui-no-theme',
                    $appointer->plugin_url() . '/lib/assets/css/jquery-ui-no-theme/jquery-ui-1.9.2.custom.css',
                    array(), '1.9.2' );
                wp_register_style( 'jquery-ui-smoothness',
                    $appointer->plugin_url() . '/lib/assets/css/jquery-ui-smoothness/jquery-ui-1.9.2.custom.css',
                    array(), '1.9.2' );

                wp_register_style( 'bootstrap',
                    $appointer->plugin_url() . '/lib/assets/js/bootstrap/css/bootstrap.css',
                    array(), '3.0.3' );

                wp_register_style( 'bootstrap-theme',
                    $appointer->plugin_url() . '/lib/assets/js/bootstrap/css/bootstrap-theme.css',
                    array( 'bootstrap' ), '3.1.1' );

                wp_deregister_style( 'select2' );
                wp_register_style( 'select2',
                    $appointer->plugin_url() . '/lib/assets/js/select2/select2.css',
                    array(), '3.4.2' );

                wp_register_style( 'jgrowl',
                    $appointer->plugin_url() . '/lib/assets/js/jgrowl/jquery.jgrowl.css',
                    array(), '1.4.0' );
            } );

        birch_defn( $ns, 'register_common_scripts', function() {
                global $appointer;

                $version = $appointer->get_product_version();

                wp_register_script( 'appointer',
                    $appointer->plugin_url() . '/assets/js/base.js',
                    array( 'jquery', 'birchpress', 'birchpress_util' ), "$version" );

                wp_register_script( 'appointer_model',
                    $appointer->plugin_url() . '/assets/js/model/base.js',
                    array( 'jquery', 'birchpress', 'appointer' ), "$version" );

                wp_register_script( 'appointer_view',
                    $appointer->plugin_url() . '/assets/js/view/base.js',
                    array( 'jquery', 'birchpress', 'appointer', 'appointer_model' ), "$version" );

                wp_register_script( 'appointer_view_admincommon',
                    $appointer->plugin_url() . '/assets/js/view/admincommon/base.js',
                    array( 'jquery', 'birchpress', 'appointer', 'jgrowl' ), "$version" );

                wp_register_script( 'appointer_view_clients_edit',
                    $appointer->plugin_url() . '/assets/js/view/clients/edit/base.js',
                    array( 'appointer_view_admincommon', 'appointer_view' ), "$version" );

                wp_register_script( 'appointer_view_locations_edit',
                    $appointer->plugin_url() . '/assets/js/view/locations/edit/base.js',
                    array( 'appointer_view_admincommon', 'appointer_view' ), "$version" );

                wp_register_script( 'appointer_view_services_edit',
                    $appointer->plugin_url() . '/assets/js/view/services/edit/base.js',
                    array( 'appointer_view_admincommon', 'appointer_view' ), "$version" );

                wp_register_script( 'appointer_view_staff_edit',
                    $appointer->plugin_url() . '/assets/js/view/staff/edit/base.js',
                    array( 'appointer_view_admincommon', 'appointer_view',
                        'jscolor' ), "$version" );

                wp_register_script( 'appointer_view_calendar',
                    $appointer->plugin_url() . '/assets/js/view/calendar/base.js',
                    array( 'appointer_view_admincommon', 'appointer_view',
                        'fullcalendar_birchpress', 'moment' ), "$version" );

                wp_register_script( 'appointer_view_bookingform',
                    $appointer->plugin_url() . '/assets/js/view/bookingform/base.js',
                    array( 'jquery-ui-datepicker', 'appointer_view' ), "$version" );
            } );

        birch_defn( $ns, 'register_common_styles', function() {
                global $appointer;

                $version = $appointer->get_product_version();

                wp_register_style( 'appointer_admincommon',
                    $appointer->plugin_url() . '/assets/css/admincommon/base.css',
                    array( 'jgrowl', 'select2' ), "$version" );

                wp_register_style( 'appointer_calendar',
                    $appointer->plugin_url() . '/assets/css/calendar/base.css',
                    array( 'jgrowl', 'bootstrap-theme' ), "$version" );

                wp_register_style( 'appointer_appointments_edit',
                    $appointer->plugin_url() . '/assets/css/appointments/edit/base.css',
                    array( 'jquery-ui-no-theme' ), "$version" );

                wp_register_style( 'appointer_appointments_new',
                    $appointer->plugin_url() . '/assets/css/appointments/new/base.css',
                    array( 'jquery-ui-no-theme' ), "$version" );

                wp_register_style( 'appointer_services_edit',
                    $appointer->plugin_url() . '/assets/css/services/edit/base.css',
                    array(), "$version" );

                wp_register_style( 'appointer_staff_edit',
                    $appointer->plugin_url() . '/assets/css/staff/edit/base.css',
                    array(), "$version" );

                wp_register_style( 'appointer_locations_edit',
                    $appointer->plugin_url() . '/assets/css/locations/edit/base.css',
                    array(), "$version" );

                wp_register_style( 'appointer_bookingform',
                    $appointer->plugin_url() . '/assets/css/bookingform/base.css',
                    array( 'jquery-ui-no-theme' ), "$version" );
            } );
    } );
