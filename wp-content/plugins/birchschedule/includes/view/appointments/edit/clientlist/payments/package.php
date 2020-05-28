<?php

birch_ns( 'appointer.view.appointments.edit.clientlist.payments', function( $ns ) {

        birch_defn( $ns, 'init', function() use ( $ns ) {
                add_action( 'admin_init', array( $ns, 'wp_admin_init' ) );
                $ns->register_post_type();
                add_action( 'appointer_view_register_common_scripts_after',
                    array( $ns, 'register_scripts' ) );
            } );

        birch_defn( $ns, 'wp_admin_init', function() use ( $ns ) {

                add_action( 'appointer_view_enqueue_scripts_post_edit_after',
                    array( $ns, 'enqueue_scripts_post_edit' ) );

                add_action( 'appointer_view_appointments_edit_clientlist_render_more_rows_after',
                    array( $ns, 'render_row' ), 30, 3 );

                add_filter( 'appointer_view_appointments_edit_clientlist_get_item_actions',
                    array( $ns, 'add_item_action' ), 30, 2 );

                add_action(
                    'wp_ajax_appointer_view_appointments_edit_clientlist_payments_add_new_payment',
                    array( $ns, 'ajax_add_new_payment' )
                );

                add_action(
                    'wp_ajax_appointer_view_appointments_edit_clientlist_payments_make_payments',
                    array( $ns, 'ajax_make_payments' )
                );
            } );

        birch_defn( $ns, 'register_post_type', function() {
                register_post_type( 'birs_payment', array(
                        'labels' => array(
                            'name' => __( 'Payments', 'appointer' ),
                            'singular_name' => __( 'Appointment', 'appointer' ),
                            'add_new' => __( 'Add Payment', 'appointer' ),
                            'add_new_item' => __( 'Add New Payment', 'appointer' ),
                            'edit' => __( 'Edit', 'appointer' ),
                            'edit_item' => __( 'Edit Payment', 'appointer' ),
                            'new_item' => __( 'New Payment', 'appointer' ),
                            'view' => __( 'View Payment', 'appointer' ),
                            'view_item' => __( 'View Payment', 'appointer' ),
                            'search_items' => __( 'Search Payments', 'appointer' ),
                            'not_found' => __( 'No Payments found', 'appointer' ),
                            'not_found_in_trash' => __( 'No Payments found in trash', 'appointer' ),
                            'parent' => __( 'Parent Payment', 'appointer' )
                        ),
                        'description' => __( 'This is where payments are stored.', 'appointer' ),
                        'public' => false,
                        'show_ui' => false,
                        'capability_type' => 'birs_payment',
                        'map_meta_cap' => true,
                        'publicly_queryable' => false,
                        'exclude_from_search' => true,
                        'show_in_menu' => 'appointer_schedule',
                        'hierarchical' => false,
                        'show_in_nav_menus' => false,
                        'rewrite' => false,
                        'query_var' => true,
                        'supports' => array( 'custom-fields' ),
                        'has_archive' => false
                    ) );
            } );

        birch_defn( $ns, 'register_scripts', function() use ( $ns ) {
                global $appointer;

                $version = $appointer->get_product_version();

                wp_register_script( 'appointer_view_appointments_edit_clientlist_payments',
                    $appointer->plugin_url() . '/assets/js/view/appointments/edit/clientlist/payments/base.js',
                    array( 'appointer_view_admincommon', 'appointer_view' ), "$version" );

            } );

        birch_defn( $ns, 'enqueue_scripts_post_edit', function( $arg ) {

                if ( $arg['post_type'] != 'birs_appointment' ) {
                    return;
                }


                global $appointer;

                $appointer->view->register_3rd_scripts();
                $appointer->view->register_3rd_styles();
                $appointer->view->enqueue_scripts(
                    array(
                        'appointer_view_appointments_edit_clientlist_payments'
                    )
                );
            } );

        birch_defn( $ns, 'get_payment_types', function() {
                global $appointer;

                return $appointer->model->booking->get_payment_types();
            } );

        birch_defn( $ns, 'add_item_action', function( $item_actions, $item ) {
                $action_html = '<a href="javascript:void(0);" data-item-id="%s">%s</a>';
                $item_actions['payments'] = sprintf( $action_html, $item['ID'], __( 'Payments', 'appointer' ) );
                return $item_actions;
            } );

        birch_defn( $ns, 'ajax_make_payments', function() {
                global $appointer;

                $appointment_id = $_POST['birs_appointment_id'];
                $client_id = $_POST['birs_client_id'];
                $appointment1on1_config =
                array(
                    'appointment1on1_keys' => array(
                        '_birs_appointment1on1_price'
                    )
                );
                $appointment1on1 =
                $appointer->model->booking->get_appointment1on1(
                    $appointment_id,
                    $client_id,
                    $appointment1on1_config
                );
                $appointment1on1['_birs_appointment1on1_price'] = $_POST['birs_appointment1on1_price'];
                $appointer->model->save( $appointment1on1, $appointment1on1_config );
                $payments = array();
                if ( isset( $_POST['birs_appointment_payments'] ) ) {
                    $payments = $_POST['birs_appointment_payments'];
                }
                $config = array(
                    'meta_keys' => $appointer->model->get_payment_fields(),
                    'base_keys' => array()
                );
                foreach ( $payments as $payment_trid => $payment ) {
                    $payment_info = $appointer->view->merge_request( array(), $config, $payment );
                    $payment_info['_birs_payment_appointment'] = $appointment_id;
                    $payment_info['_birs_payment_client'] = $client_id;
                    $payment_info['_birs_payment_trid'] = $payment_trid;
                    $payment_info['_birs_payment_currency'] = $appointer->model->get_currency_code();
                    $appointer->model->booking->make_payment( $payment_info );
                }

                $appointer->view->render_ajax_success_message( array(
                        'code' => 'success',
                        'message' => ''
                    ) );
            } );

        birch_defn( $ns, 'render_row', function( $wp_list_table, $item, $row_class ) use( $ns ) {
                $client_id = $item['ID'];
                $appointment_id = $wp_list_table->appointment_id;
                $column_count = $wp_list_table->get_column_count();
                $payments_html = $ns->get_payments_details_html( $appointment_id, $client_id );
?>
                <tr class="<?php echo $row_class; ?> birs_row_payments"
                    id="birs_client_list_row_payments_<?php echo $item['ID']; ?>"
                    data-item-id = "<?php echo $item['ID']; ?>"
                    data-payments-html = "<?php echo esc_attr( $payments_html ); ?>">

                    <td colspan = "<?php echo $column_count; ?>"></td>
                </tr>
<?php
            } );

        birch_defn( $ns, 'get_payments_details_html', function( $appointment_id, $client_id ) use ( $ns ) {
                global $appointer, $birchpress;

                $price = 0;
                $payment_types = $ns->get_payment_types();
                $payments = array();

                if ( $appointment_id ) {
                    $appointment1on1 = $appointer->model->booking->get_appointment1on1(
                        $appointment_id,
                        $client_id,
                        array(
                            'appointment1on1_keys' => array(
                                '_birs_appointment1on1_price'
                            ),
                            'base_keys' => array()
                        ) );
                    $price = $appointment1on1['_birs_appointment1on1_price'];
                    $payments =
                    $appointer->model->booking->get_payments_by_appointment1on1( $appointment_id, $client_id );
                }
                ob_start();
?>
                <ul>
                    <li class="birs_form_field">
                        <label>
<?php
                $currency_code = $appointer->model->get_currency_code();
                echo $appointer->view->
                apply_currency_to_label( __( 'Price', 'appointer' ), $currency_code );
?>
                        </label>
                        <div class="birs_field_content">
                            <input type="text" id="birs_appointment1on1_price"
                                name="birs_appointment1on1_price"
                                value="<?php echo $price; ?>">
                        </div>
                    </li>
                    <li class="birs_form_field">
                        <label>
                            <?php _e( 'Paid', 'appointer' ); ?>
                        </label>
                        <div class="birs_field_content">
                            <span class="birs_money"
                                id="birs_appointment1on1_paid"></span>
                        </div>
                    </li>
                    <li class="birs_form_field">
                        <label>
                            <?php _e( 'Due', 'appointer' ); ?>
                        </label>
                        <div class="birs_field_content">
                            <span class="birs_money"
                                id="birs_appointment1on1_due"></span>
                        </div>
                    </li>
                </ul>
                <div class="splitter"></div>
                <ul>
                    <li class="birs_form_field">
                        <label>
                            <?php _e( 'Amount to Pay', 'appointer' ); ?>
                        </label>
                        <div class="birs_field_content">
                            <input type="text" id="birs_appointment1on1_amount_to_pay"
                                name="birs_payment_amount"
                                value="" >
                        </div>
                    </li>
                    <li class="birs_form_field">
                        <label>
                            <?php _e( 'Payment Type', 'appointer' ); ?>
                        </label>
                        <div class="birs_field_content">
                            <select name="birs_payment_type">
                                <?php $birchpress->util->render_html_options( $payment_types ); ?>
                            </select>
                        </div>
                    </li>
                    <li class="birs_form_field">
                        <label>
                            <?php _e( 'Payment Notes', 'appointer' ); ?>
                        </label>
                        <div class="birs_field_content">
                            <textarea name="birs_payment_notes"></textarea>
                        </div>
                    </li>
                    <li class="birs_form_field">
                        <label>
                            &nbsp;
                        </label>
                        <div class="birs_field_content">
                            <a id="birs_add_payment"
                               class="button" href="javascript:void(0);">
                                <?php _e( 'Add Payment', 'appointer' ); ?>
                            </a>
                        </div>
                    </li>
                </ul>
                <div class="splitter"></div>
                <ul>
                    <li class="birs_form_field">
                        <label>
                            <?php _e( 'Payment History', 'appointer' ); ?>
                        </label>
                    </li>
                </ul>
                <table class="wp-list-table fixed widefat" id="birs_payments_table">
                    <thead>
                        <tr>
                            <th><?php _e( 'Date', 'appointer' ); ?></th>
                            <th class="column-author"><?php _e( 'Amount', 'appointer' ); ?></th>
                            <th class="column-author"><?php _e( 'Type', 'appointer' ); ?></th>
                            <th><?php _e( 'Notes', 'appointer' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
<?php
                foreach ( $payments as $payment_id => $payment ) {
                    $payment_datetime =
                    $birchpress->util->convert_to_datetime( $payment['_birs_payment_timestamp'] );
                    $amount = $payment['_birs_payment_amount'];
?>
                        <tr data-payment-amount="<?php echo $amount; ?>">
                            <td><?php echo $payment_datetime; ?></td>
                            <td>
                                <?php echo $appointer->model->apply_currency_symbol(
                        $payment['_birs_payment_amount'],
                        $payment['_birs_payment_currency'] ); ?>
                            </td>
                            <td>
                                <?php echo $payment_types[$payment['_birs_payment_type']]; ?>
                            </td>
                            <td>
                                <?php echo $payment['_birs_payment_notes']; ?>
                            </td>
                        </tr>
<?php
                }
?>
                    </tbody>
                </table>
                <input type="hidden" name="birs_client_id" id="birs_client_id" value="<?php echo $client_id; ?>" />
                <ul>
                    <li class="birs_form_field">
                        <label>
                            &nbsp;
                        </label>
                        <div class="birs_field_content">
                            <input name="birs_appointment_client_payments_save"
                                id="birs_appointment_client_payments_save"
                                type="button" class="button-primary"
                                value="<?php _e( 'Save', 'appointer' ); ?>" />
                            <a href="javascript:void(0);"
                                id="birs_appointment_client_payments_cancel"
                                style="padding: 4px 0 0 4px; display: inline-block;">
                                <?php _e( 'Cancel', 'appointer' ); ?>
                            </a>
                        </div>
                    </li>
                </ul>
<?php
                $content = ob_get_clean();
                return $content;
            } );

        birch_defn( $ns, 'ajax_add_new_payment', function() use ( $ns ) {
                global $birchpress, $appointer;

                $payment_types = $ns->get_payment_types();
                $timestamp = time();
                $amount = 0;
                if ( isset( $_POST['birs_payment_amount'] ) ) {
                    $amount = floatval( $_POST['birs_payment_amount'] );
                }
                $payment_type = $_POST['birs_payment_type'];
                if ( isset( $_POST['birs_payment_notes'] ) ) {
                    $payment_notes = $_POST['birs_payment_notes'];
                }
                $payment_trid = uniqid();
?>
                <tr data-payment-amount="<?php echo $amount; ?>"
                    data-payment-trid="<?php echo $payment_trid; ?>" >
                    <td>
                        <?php echo $birchpress->util->convert_to_datetime( $timestamp ); ?>
                        <input type="hidden"
                             name="birs_appointment_payments[<?php echo $payment_trid ?>][birs_payment_timestamp]"
                             value="<?php echo $timestamp; ?>" />
                        <div class="row-actions">
                            <span class="delete">
                                <a href="javascript:void(0);"
                                    data-payment-trid="<?php echo $payment_trid; ?>">
                                    <?php _e( 'Delete', 'appointer' ); ?>
                                </a>
                            </span>
                        </div>
                    </td>
                    <td>
<?php
                $currency_code = $appointer->model->get_currency_code();
                echo $appointer->model->apply_currency_symbol( $amount, $currency_code );
?>
                        <input type="hidden"
                             name="birs_appointment_payments[<?php echo $payment_trid ?>][birs_payment_amount]"
                             value="<?php echo $amount; ?>" />
                    </td>
                    <td>
                        <?php echo $payment_types[$payment_type]; ?>
                        <input type="hidden"
                             name="birs_appointment_payments[<?php echo $payment_trid ?>][birs_payment_type]"
                             value="<?php echo $payment_type; ?>" />
                    </td>
                    <td>
                        <?php echo $payment_notes; ?>
                        <input type="hidden"
                             name="birs_appointment_payments[<?php echo $payment_trid ?>][birs_payment_notes]"
                             value="<?php echo $payment_notes; ?>" />
                    </td>
                </tr>
<?php
                die();
            } );

    } );
