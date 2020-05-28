<?php

birch_ns( 'appointer.view.services', function( $ns ) {

        global $appointer;

        birch_defn( $ns, 'init', function() use( $ns, $appointer ) {

                add_action( 'admin_init', array( $ns, 'wp_admin_init' ) );

                add_action( 'init', array( $ns, 'wp_init' ) );

                birch_defmethod( $appointer->view, 'load_page_edit',
                    'birs_service', $ns->load_page_edit );

                birch_defmethod( $appointer->view, 'enqueue_scripts_edit',
                    'birs_service', $ns->enqueue_scripts_edit );

                birch_defmethod( $appointer->view, 'save_post',
                    'birs_service', $ns->save_post );

            } );

        birch_defn( $ns, 'wp_init', function() use ( $ns ) {
                $ns->register_post_type();
            } );

        birch_defn( $ns, 'wp_admin_init', function() use ( $ns ) {
                add_filter( 'manage_edit-birs_service_columns', array( $ns, 'get_edit_columns' ) );
                add_action( 'manage_birs_service_posts_custom_column', array( $ns, 'render_custom_columns' ), 2 );
            } );

        birch_defn( $ns, 'register_post_type', function() use ( $ns ) {
                register_post_type( 'birs_service', array(
                        'labels' => array(
                            'name' => __( 'Services', 'appointer' ),
                            'singular_name' => __( 'Service', 'appointer' ),
                            'add_new' => __( 'Add Service', 'appointer' ),
                            'add_new_item' => __( 'Add New Service', 'appointer' ),
                            'edit' => __( 'Edit', 'appointer' ),
                            'edit_item' => __( 'Edit Service', 'appointer' ),
                            'new_item' => __( 'New Service', 'appointer' ),
                            'view' => __( 'View Service', 'appointer' ),
                            'view_item' => __( 'View Service', 'appointer' ),
                            'search_items' => __( 'Search Services', 'appointer' ),
                            'not_found' => __( 'No Services found', 'appointer' ),
                            'not_found_in_trash' => __( 'No Services found in trash', 'appointer' ),
                            'parent' => __( 'Parent Service', 'appointer' )
                        ),
                        'description' => __( 'This is where services are stored.', 'appointer' ),
                        'public' => false,
                        'show_ui' => true,
                        'capability_type' => 'birs_service',
                        'map_meta_cap' => true,
                        'publicly_queryable' => false,
                        'exclude_from_search' => true,
                        'show_in_menu' => 'edit.php?post_type=birs_appointment',
                        'hierarchical' => false,
                        'show_in_nav_menus' => false,
                        'rewrite' => false,
                        'query_var' => true,
                        'supports' => array( 'title', 'editor' ),
                        'has_archive' => false
                    )
                );
            } );

        birch_defn( $ns, 'get_edit_columns', function( $columns ) {
                $columns = array();

                $columns["cb"] = "<input type=\"checkbox\" />";
                $columns["title"] = __( "Service Name", 'appointer' );
                $columns["description"] = __( "Description", 'appointer' );
                return $columns;
            } );

        birch_defn( $ns, 'render_custom_columns', function( $column ) {
                global $post;

                if ( $column == 'description' ) {
                    the_content();
                    return;
                }
                $value = get_post_meta( $post->ID, '_' . $column, true );

                echo $value;
            } );

        birch_defn( $ns, 'load_page_edit', function( $arg ) use ( $ns ) {

                add_action( 'add_meta_boxes', array( $ns, 'add_meta_boxes' ) );
                add_filter( 'post_updated_messages', array( $ns, 'get_updated_messages' ) );
            } );

        birch_defn( $ns, 'enqueue_scripts_edit', function( $arg ) {

                global $appointer;

                $appointer->view->register_3rd_scripts();
                $appointer->view->register_3rd_styles();
                $appointer->view->enqueue_scripts(
                    array(
                        'appointer_view_services_edit', 'appointer_model',
                        'appointer_view_admincommon', 'appointer_view'
                    )
                );
                $appointer->view->enqueue_styles( array( 'appointer_admincommon',
                        'appointer_services_edit' ) );
            } );

        birch_defn( $ns, 'add_meta_boxes', function() use ( $ns ) {
                remove_meta_box( 'slugdiv', 'birs_service', 'normal' );
                remove_meta_box( 'postcustom', 'birs_service', 'normal' );
                add_meta_box( 'appointer-service-info', __( 'Service Settings', 'appointer' ),
                    array( $ns, 'render_service_info' ), 'birs_service', 'normal', 'high' );
                add_meta_box( 'appointer-service-staff', __( 'Providers', 'appointer' ),
                    array( $ns, 'render_assign_staff' ), 'birs_service', 'side', 'default' );
            } );

        birch_defn( $ns, 'get_updated_messages', function( $messages ) {
                global $post, $post_ID;

                $messages['birs_service'] = array(
                    0 => '', // Unused. Messages start at index 1.
                    1 => __( 'Service updated.', 'appointer' ),
                    2 => __( 'Custom field updated.', 'appointer' ),
                    3 => __( 'Custom field deleted.', 'appointer' ),
                    4 => __( 'Service updated.', 'appointer' ),
                    5 => isset( $_GET['revision'] ) ? sprintf( __( 'Service restored to revision from %s', 'appointer' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
                    6 => __( 'Service updated.', 'appointer' ),
                    7 => __( 'Service saved.', 'appointer' ),
                    8 => __( 'Service submitted.', 'appointer' ),
                    9 => sprintf( __( 'Service scheduled for: <strong>%1$s</strong>.', 'appointer' ), date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ) ),
                    10 => __( 'Service draft updated.', 'appointer' )
                );

                return $messages;
            } );

        birch_defn( $ns, 'get_length_types', function() {
                return array(
                    'minutes' => __( 'minutes', 'appointer' ),
                    'hours' => __( 'hours', 'appointer' )
                );
            } );

        birch_defn( $ns, 'get_padding_types', function() {
                return array(
                    'before' => __( 'Before', 'appointer' ),
                    'after' => __( 'After', 'appointer' ),
                    'before-and-after' => __( 'Before & After', 'appointer' )
                );
            } );

        birch_defn( $ns, 'get_price_types', function() {
                return array(
                    'fixed' => __( 'Fixed', 'appointer' ),
                    'varies' => __( 'Varies', 'appointer' ),
                );
            } );

        birch_defn( $ns, 'save_post', function( $post ) {

                global $appointer;
                $config = array(
                    'base_keys' => array(),
                    'meta_keys' => array(
                        '_birs_service_length', '_birs_service_length_type',
                        '_birs_service_padding', '_birs_service_padding_type',
                        '_birs_service_price', '_birs_service_price_type',
                        '_birs_assigned_staff'
                    )
                );
                $service_data =
                $appointer->view->merge_request( $post, $config, $_POST );
                if ( !isset( $_POST['birs_assigned_staff'] ) ) {
                    $service_data['_birs_assigned_staff'] = array();
                }
                $appointer->model->save( $service_data, $config );
                $appointer->model->update_model_relations( $post['ID'], '_birs_assigned_staff',
                    'birs_staff', '_birs_assigned_services' );
                $appointer->model->booking->async_recheck_fully_booked_days();
            } );

        birch_defn( $ns, 'render_service_info', function( $post ) use( $ns ) {
                global $appointer, $birchpress;

                $post_id = $post->ID;
                $length = get_post_meta( $post_id, '_birs_service_length', true );
                $length_type = get_post_meta( $post_id, '_birs_service_length_type', true );
                $padding = get_post_meta( $post_id, '_birs_service_padding', true );
                $padding_type = get_post_meta( $post_id, '_birs_service_padding_type', true );
                $price = get_post_meta( $post_id, '_birs_service_price', true );
                $price_type = get_post_meta( $post_id, '_birs_service_price_type', true );
?>
                <div class="panel-wrap appointer">
                    <table class="form-table">
                        <tr class="form-field">
                            <th><label><?php _e( 'Length', 'appointer' ); ?> </label>
                            </th>
                            <td>
                                <input type="text" name="birs_service_length"
                                       id="birs_service_length" value="<?php echo $length; ?>">
                               <select name="birs_service_length_type">
                                   <?php $birchpress->util->render_html_options( $ns->get_length_types(), $length_type ); ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="form-field">
                            <th><label><?php _e( 'Padding', 'appointer' ); ?> </label>
                            </th>
                            <td><input type="text" name="birs_service_padding"
                                       id="birs_service_padding" value="<?php echo $padding; ?>"> <span><?php echo _e( 'mins padding time', 'appointer' ); ?>
                                </span> <select name="birs_service_padding_type">
                                    <?php $birchpress->util->render_html_options( $ns->get_padding_types(), $padding_type ) ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="form-field">
                            <th><label><?php echo apply_filters( 'appointer_price_label', __( 'Price', 'appointer' ) ); ?> </label></th>
                            <td><select name="birs_service_price_type" id="birs_service_price_type">
                                    <?php $birchpress->util->render_html_options( $ns->get_price_types(), $price_type ); ?>
                                </select>
                                <input type="text"
                                       name="birs_service_price" id="birs_service_price" value="<?php echo $price; ?>">
                            </td>
                        </tr>
                    </table>
                </div>
<?php
            } );

        birch_defn( $ns, 'render_staff_checkboxes', function( $staff, $assigned_staff ) {
                foreach ( $staff as $thestaff ) {
                    if ( array_key_exists( $thestaff->ID, $assigned_staff ) ) {
                        $checked = 'checked="checked"';
                    } else {
                        $checked = '';
                    }
                    echo '<li><label>' .
                    "<input type=\"checkbox\" " .
                    "name=\"birs_assigned_staff[$thestaff->ID]\" $checked >" .
                    $thestaff->post_title .
                    '</label></li>';
                }
            } );

        birch_defn( $ns, 'render_assign_staff', function( $post ) use ( $ns ) {
                $staff = get_posts(
                    array(
                        'post_type' => 'birs_staff',
                        'nopaging' => true
                    )
                );
                $assigned_staff = get_post_meta( $post->ID, '_birs_assigned_staff', true );
                $assigned_staff = unserialize( $assigned_staff );
                if ( $assigned_staff === false ) {
                    $assigned_staff = array();
                }
?>
                <div class="panel-wrap appointer">
<?php
                if ( sizeof( $staff ) > 0 ) {
?>
                    <p>
                        <?php _e( 'Assign providers that can perform this service:', 'appointer' ); ?>
                    </p>
                    <div>
                        <ul>
                            <?php $ns->render_staff_checkboxes( $staff, $assigned_staff ); ?>
                        </ul>
                    </div>
<?php
                } else {
?>
                    <p>
<?php
                    printf( __( 'There is no providers to assign. Click %s here %s to add one.', 'appointer' ), '<a
                            href="post-new.php?post_type=birs_staff">', '</a>' );
?>
                    </p>
<?php
                }
?>
                </div>
<?php
            } );

    } );
