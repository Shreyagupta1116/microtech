<?php

birch_ns( 'appointer.view.locations', function( $ns ) {

        global $appointer;

        birch_defn( $ns, 'init', function() use( $ns, $appointer ) {

                add_action( 'admin_init', array( $ns, 'wp_admin_init' ) );

                add_action( 'init', array( $ns, 'wp_init' ) );

                birch_defmethod( $appointer->view, 'load_page_edit',
                    'birs_location', $ns->load_page_edit );

                birch_defmethod( $appointer->view, 'enqueue_scripts_edit',
                    'birs_location', $ns->enqueue_scripts_edit );

                birch_defmethod( $appointer->view, 'save_post',
                    'birs_location', $ns->save_post );

            } );

        birch_defn( $ns, 'wp_init', function() use ( $ns ) {
                $ns->register_post_type();
            } );

        birch_defn( $ns, 'wp_admin_init', function() use ( $ns ) {
                add_filter( 'manage_edit-birs_location_columns', array( $ns, 'get_edit_columns' ) );
                add_action( 'manage_birs_location_posts_custom_column', array( $ns, 'render_custom_columns' ), 2 );
            } );

        birch_defn( $ns, 'get_register_options', function() {
                return array(
                    'labels' => array(
                        'name' => __( 'Locations', 'appointer' ),
                        'singular_name' => __( 'Location', 'appointer' ),
                        'add_new' => __( 'Add Location', 'appointer' ),
                        'add_new_item' => __( 'Add New Location', 'appointer' ),
                        'edit' => __( 'Edit', 'appointer' ),
                        'edit_item' => __( 'Edit Location', 'appointer' ),
                        'new_item' => __( 'New Location', 'appointer' ),
                        'view' => __( 'View Location', 'appointer' ),
                        'view_item' => __( 'View Location', 'appointer' ),
                        'search_items' => __( 'Search Locations', 'appointer' ),
                        'not_found' => __( 'No Locations found', 'appointer' ),
                        'not_found_in_trash' => __( 'No Locations found in trash', 'appointer' ),
                        'parent' => __( 'Parent Location', 'appointer' )
                    ),
                    'description' => __( 'This is where locations are stored.', 'appointer' ),
                    'public' => false,
                    'show_ui' => true,
                    'capability_type' => 'birs_location',
                    'map_meta_cap' => true,
                    'publicly_queryable' => false,
                    'exclude_from_search' => true,
                    'show_in_menu' => 'edit.php?post_type=birs_appointment',
                    'hierarchical' => false,
                    'show_in_nav_menus' => false,
                    'rewrite' => false,
                    'query_var' => true,
                    'supports' => array( 'title' ),
                    'has_archive' => false
                );
            } );

        birch_defn( $ns, 'register_post_type', function() use( $ns ) {
                register_post_type( 'birs_location', $ns->get_register_options() );
            } );

        birch_defn( $ns, 'get_edit_columns', function( $columns ) {
                $columns = array();

                $columns["cb"] = "<input type=\"checkbox\" />";
                $columns["title"] = __( "Location Name", 'appointer' );
                $columns["birs_location_address"] = __( "Address", 'appointer' );
                $columns["birs_location_city"] = __( "City", 'appointer' );
                $columns["birs_location_state"] = __( "State/Province", 'appointer' );
                return $columns;
            } );

        birch_defn( $ns, 'get_updated_messages', function( $messages ) {
                global $post, $post_ID;

                $messages['birs_location'] = array(
                    0 => '', // Unused. Messages start at index 1.
                    1 => __( 'Location updated.', 'appointer' ),
                    2 => __( 'Custom field updated.', 'appointer' ),
                    3 => __( 'Custom field deleted.', 'appointer' ),
                    4 => __( 'Location updated.', 'appointer' ),
                    5 => isset( $_GET['revision'] ) ? sprintf( __( 'Location restored to revision from %s', 'appointer' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
                    6 => __( 'Location updated.', 'appointer' ),
                    7 => __( 'Location saved.', 'appointer' ),
                    8 => __( 'Location submitted.', 'appointer' ),
                    9 => sprintf( __( 'Location scheduled for: <strong>%1$s</strong>.', 'appointer' ), date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ) ),
                    10 => __( 'Location draft updated.', 'appointer' )
                );

                return $messages;
            } );

        birch_defn( $ns, 'render_custom_columns', function( $column ) {
                global $post, $birchpress;

                if ( $column === "birs_location_address" ) {
                    $address1 = get_post_meta( $post->ID, '_birs_location_address1', true );
                    $address2 = get_post_meta( $post->ID, '_birs_location_address2', true );
                    $value = $address1 . '<br>' . $address2;
                } else {
                    $value = get_post_meta( $post->ID, '_' . $column, true );
                }

                if ( $column === 'birs_location_state' ) {
                    $states = $birchpress->util->get_us_states();
                    if ( isset( $states[$value] ) ) {
                        $value = $states[$value];
                    } else {
                        $value = '';
                    }
                }
                echo $value;
            } );

        birch_defn( $ns, 'load_page_edit', function( $arg ) use( $ns ) {

                add_action( 'add_meta_boxes', array( $ns, 'add_meta_boxes' ) );
                add_filter( 'post_updated_messages', array( $ns, 'get_updated_messages' ) );
            } );

        birch_defn( $ns, 'enqueue_scripts_edit', function( $arg ) use ( $ns ) {

                global $appointer;

                $appointer->view->register_3rd_scripts();
                $appointer->view->register_3rd_styles();
                $appointer->view->enqueue_scripts(
                    array(
                        'appointer_view_locations_edit', 'appointer_model',
                        'appointer_view_admincommon', 'appointer_view'
                    )
                );
                $appointer->view->enqueue_styles( array( 'appointer_admincommon', 'appointer_locations_edit' ) );
            } );

        birch_defn( $ns, 'save_post', function( $post ) use ( $ns ) {

                global $appointer;

                $config = array(
                    'meta_keys' => array(
                        '_birs_location_phone', '_birs_location_address1',
                        '_birs_location_address2', '_birs_location_city',
                        '_birs_location_state', '_birs_location_country',
                        '_birs_location_zip'
                    ),
                    'base_keys' => array()
                );
                $post_data = $appointer->view->merge_request( $post, $config, $_REQUEST );
                $appointer->model->save( $post_data, $config );
            } );


        birch_defn( $ns, 'add_meta_boxes', function() use ( $ns ) {
                remove_meta_box( 'slugdiv', 'birs_location', 'normal' );
                remove_meta_box( 'postcustom', 'birs_location', 'normal' );
                add_meta_box( 'appointer-location-info', __( 'Location Details', 'appointer' ),
                    array( $ns, 'render_location_info' ), 'birs_location', 'normal', 'high' );
            } );

        birch_defn( $ns, 'render_location_info', function( $post ) use ( $ns ) {
                global $birchpress, $appointer;

                $post_id = $post->ID;
                $addresss1 = get_post_meta( $post_id, '_birs_location_address1', true );
                $addresss2 = get_post_meta( $post_id, '_birs_location_address2', true );
                $phone = get_post_meta( $post_id, '_birs_location_phone', true );
                $city = get_post_meta( $post_id, '_birs_location_city', true );
                $zip = get_post_meta( $post_id, '_birs_location_zip', true );
                $state = get_post_meta( $post_id, '_birs_location_state', true );
                $country = get_post_meta( $post_id, '_birs_location_country', true );
                if ( !$country ) {
                    $country = $appointer->model->get_default_country();
                }
                $countries = $birchpress->util->get_countries();
                $all_states = $birchpress->util->get_states();
                if ( isset( $all_states[$country] ) ) {
                    $select_display = "";
                    $text_display = "display:none;";
                    $states = $all_states[$country];
                } else {
                    $select_display = "display:none;";
                    $text_display = "";
                    $states = array();
                }
?>
                <div class="panel-wrap appointer">
                    <table class="form-table">
                        <tr class="form-field">
                            <th><label><?php _e( 'Phone Number', 'appointer' ); ?> </label>
                            </th>
                            <td><input type="text" name="birs_location_phone"
                                       id="birs_location_phone" value="<?php echo esc_attr( $phone ); ?>">
                            </td>
                        </tr>
                        <tr class="form-field">
                            <th><label><?php _e( 'Address', 'appointer' ); ?> </label>
                            </th>
                            <td><input type="text" name="birs_location_address1"
                                       id="birs_location_address1"
                                       value="<?php echo esc_attr( $addresss1 ); ?>"> <br> <input type="text"
                                       name="birs_location_address2" id="birs_location_address2"
                                       value="<?php echo esc_attr( $addresss2 ); ?>">
                            </td>
                        </tr>
                        <tr class="form-field">
                            <th><label><?php _e( 'City', 'appointer' ); ?> </label>
                            </th>
                            <td><input type="text" name="birs_location_city"
                                       id="birs_location_city" value="<?php echo esc_attr( $city ); ?>">
                            </td>
                        </tr>
                        <tr class="form-field">
                            <th><label><?php _e( 'State/Province', 'appointer' ); ?> </label>
                            </th>
                            <td>
                                <select name="birs_location_state_select" id="birs_location_state_select" style="<?php echo $select_display; ?>">
                                    <?php $birchpress->util->render_html_options( $states, $state ); ?>
                                </select>
                                <input type="text" name="birs_location_state" id="birs_location_state" value="<?php echo esc_attr( $state ); ?>" style="<?php echo $text_display; ?>">
                            </td>
                        </tr>
                        <tr class="form-field">
                            <th><label><?php _e( 'Country', 'appointer' ); ?></label></th>
                            <td>
                                <select name="birs_location_country" id="birs_location_country">
                                    <?php $birchpress->util->render_html_options( $countries, $country ); ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="form-field">
                            <th><label><?php _e( 'Zip Code', 'appointer' ); ?> </label>
                            </th>
                            <td><input type="text" name="birs_location_zip"
                                       id="birs_location_zip" value="<?php echo esc_attr( $zip ); ?>">
                            </td>
                        </tr>
                    </table>
                </div>
<?php
            } );

    } );
