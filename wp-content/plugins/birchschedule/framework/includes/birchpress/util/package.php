<?php


birch_ns( 'birchpress.util', function( $ns ) {

        $date_time_format_php_pattern = array(
            //day of month
            'd', //Numeric, with leading zeros
            'j', //Numeric, without leading zeros

            //weekday
            'l', //full name of the day
            'D', //Three letter name

            //month
            'F', //Month name full
            'M', //Month name short
            'n', //numeric month no leading zeros
            'm', //numeric month leading zeros

            //year
            'Y', //full numeric year
            'y', //numeric year: 2 digit

            //time
            'a',
            'A',
            'g', //Hour, 12-hour, without leading zeros
            'h', //Hour, 12-hour, with leading zeros
            'G', //Hour, 24-hour, without leading zeros
            'H', //Hour, 24-hour, with leading zeros
            'i' //Minutes, with leading zeros
        );

        birch_defn( $ns, 'date_time_format_php_to_jquery', function( $date_time_format )
            use ( $ns, &$date_time_format_php_pattern ) {

                $pattern = $date_time_format_php_pattern;
                $replace = array(
                    'dd', 'd',
                    'DD', 'D',
                    'MM', 'M', 'm', 'mm',
                    'yy', 'y',
                    'am', 'AM', '', '', '', '', ''
                );
                foreach ( $pattern as &$p ) {
                    $p = '/'.$p.'/';
                }
                return preg_replace( $pattern, $replace, $date_time_format );
            } );

        birch_defn( $ns, 'date_time_format_php_to_fullcalendar', function( $date_time_format )
            use ( $ns, &$date_time_format_php_pattern ) {

                $pattern = $date_time_format_php_pattern;
                $replace = array(
                    'dd', 'd',
                    'dddd', 'ddd',
                    'MMMM', 'MMM', 'M', 'MM',
                    'yyyy', 'yy',
                    'tt', 'TT', 'h', 'hh', 'H', 'HH', 'mm'
                );
                foreach ( $pattern as &$p ) {
                    $p = '/'.$p.'/';
                }
                return preg_replace( $pattern, $replace, $date_time_format );
            } );

        birch_defn( $ns, 'wp_format_time', function( $datetime ) use ( $ns ) {
                $time_format = get_option( 'time_format' );
                return $datetime->format( $time_format );
            } );

        birch_defn( $ns, 'wp_format_date', function( $datetime ) use ( $ns ) {
                $date_format = get_option( 'date_format' );
                $timestamp = $datetime->format( 'U' );
                return $ns->date_i18n( $date_format, $timestamp );
            } );

        birch_defn( $ns, 'convert_to_datetime', function ( $timestamp ) use ( $ns ) {
                $date_format = get_option( 'date_format' );
                $time_format = get_option( 'time_format' );
                $datetime = $ns->get_wp_datetime( $timestamp );
                $datetime_separator = $ns->get_datetime_separator();
                return $ns->date_i18n( $date_format, $timestamp ) . $datetime_separator .
                $datetime->format( $time_format );
            } );

        birch_defn( $ns, 'get_datetime_separator', function () use ( $ns ) {
                return ' ';
            } );

        birch_defn( $ns, 'get_wp_timezone', function () use ( $ns ) {
                $timezone = get_option( 'timezone_string' );
                $offset = get_option( 'gmt_offset' );
                if ( $timezone ) {
                    $timezone = new DateTimeZone( $timezone );
                } else if ( $offset ) {
                    $offset = -round( $offset );
                    if ( $offset > 0 ) {
                        $offset = '+' . $offset;
                    }
                    $timezone = new DateTimeZone( 'Etc/GMT' . $offset );
                } else {
                    $timezone = date_default_timezone_get();
                    $timezone = new DateTimeZone( $timezone );
                }
                return $timezone;
            } );

        birch_defn( $ns, 'date_i18n', function( $dateformatstring, $unixtimestamp ) use ( $ns ) {

                global $wp_locale;

                $datetime = $ns->get_wp_datetime( $unixtimestamp );
                if ( ( !empty( $wp_locale->month ) ) && ( !empty( $wp_locale->weekday ) ) ) {
                    $datemonth = $datetime->format( 'm' );
                    $datemonth = $wp_locale->get_month( $datemonth );
                    $datemonth_abbrev = $wp_locale->get_month_abbrev( $datemonth );

                    $dateweekday = $datetime->format( 'w' );
                    $dateweekday = $wp_locale->get_weekday( $dateweekday );
                    $dateweekday_abbrev = $wp_locale->get_weekday_abbrev( $dateweekday );

                    $datemeridiem = $datetime->format( 'a' );
                    $datemeridiem = $wp_locale->get_meridiem( $datemeridiem );
                    $datemeridiem_capital = $datetime->format( 'A' );
                    $datemeridiem_capital = $wp_locale->get_meridiem( $datemeridiem_capital );

                    $dateformatstring = ' '.$dateformatstring;
                    $dateformatstring = preg_replace( "/([^\\\])D/", "\\1" . backslashit( $dateweekday_abbrev ), $dateformatstring );
                    $dateformatstring = preg_replace( "/([^\\\])F/", "\\1" . backslashit( $datemonth ), $dateformatstring );
                    $dateformatstring = preg_replace( "/([^\\\])l/", "\\1" . backslashit( $dateweekday ), $dateformatstring );
                    $dateformatstring = preg_replace( "/([^\\\])M/", "\\1" . backslashit( $datemonth_abbrev ), $dateformatstring );
                    $dateformatstring = preg_replace( "/([^\\\])a/", "\\1" . backslashit( $datemeridiem ), $dateformatstring );
                    $dateformatstring = preg_replace( "/([^\\\])A/", "\\1" . backslashit( $datemeridiem_capital ), $dateformatstring );

                    $dateformatstring = substr( $dateformatstring, 1, strlen( $dateformatstring ) -1 );
                }
                $timezone_formats = array( 'P', 'I', 'O', 'T', 'Z', 'e' );
                $timezone_formats_re = implode( '|', $timezone_formats );
                if ( preg_match( "/$timezone_formats_re/", $dateformatstring ) ) {
                    $timezone_object = $ns->get_wp_timezone();
                    $date_object = date_create( null, $timezone_object );
                    foreach ( $timezone_formats as $timezone_format ) {
                        if ( false !== strpos( $dateformatstring, $timezone_format ) ) {
                            $formatted = date_format( $date_object, $timezone_format );
                            $dateformatstring = ' '.$dateformatstring;
                            $dateformatstring = preg_replace( "/([^\\\])$timezone_format/", "\\1" . backslashit( $formatted ), $dateformatstring );
                            $dateformatstring = substr( $dateformatstring, 1, strlen( $dateformatstring ) -1 );
                        }
                    }
                }
                return $datetime->format( $dateformatstring );
            } );

        birch_defn( $ns, 'get_day_minutes', function( $datetime ) use( $ns ) {
                $time = $datetime->format( 'H' ) * 60 + $datetime->format( 'i' );
                return $time;
            } );

        birch_defn( $ns, 'has_shortcode', function( $shortcode = NULL ) use( $ns ) {

                $post_to_check = get_post( get_the_ID() );

                // false because we have to search through the post content first
                $found = false;

                // if no short code was provided, return false
                if ( !$shortcode ) {
                    return $found;
                }
                // check the post content for the short code
                if ( stripos( $post_to_check->post_content, '[' . $shortcode ) !== FALSE && stripos( $post_to_check->post_content, '[[' . $shortcode ) == FALSE ) {
                    // we have found the short code
                    $found = TRUE;
                }

                // return our final results
                return $found;
            } );

        birch_defn( $ns, 'to_mysql_date', function ( $arg ) {
                $date = $arg['date'];
                $date = explode( '/', $date );
                $date = $date[2] . '-' . $date[0] . '-' . $date[1];
                $time = $arg['time'];
                $hours = floor( $time / 60 );
                $minutes = $time % 60;
                $date .= ' ' . $hours . ':' . $minutes . ':00';
                return $date;
            } );

        birch_defn( $ns, 'get_wp_datetime', function ( $arg ) use ( $ns ) {
                $timezone = $ns->get_wp_timezone();
                try {
                    if ( is_array( $arg ) ) {
                        $datetime = $ns->to_mysql_date( $arg );
                        $datetime = new DateTime( $datetime, $timezone );
                        return $datetime;
                    }
                    if ( (string) (int) $arg == $arg && (int) $arg > 0 ) {
                        $datetime = new DateTime( '@' . $arg );
                        $datetime->setTimezone( $timezone );
                        return $datetime;
                    }
                    $datetime = new DateTime( $arg, $timezone );
                    return $datetime;
                } catch( Exception $e ) {
                    return new DateTime( '@408240000' );
                }
            } );

        birch_defn( $ns, 'get_weekdays_short', function() use ( $ns ) {
                return array(
                    __( 'Sun', 'appointer' ),
                    __( 'Mon', 'appointer' ),
                    __( 'Tue', 'appointer' ),
                    __( 'Wed', 'appointer' ),
                    __( 'Thu', 'appointer' ),
                    __( 'Fri', 'appointer' ),
                    __( 'Sat', 'appointer' )
                );
            } );

        birch_defn( $ns, 'get_calendar_views', function() use ( $ns ) {
                return array(
                    'month' => __( 'Month', 'appointer' ),
                    'agendaWeek' => __( 'Week', 'appointer' ),
                    'agendaDay' => __( 'Day', 'appointer' )
                );
            } );

        birch_defn( $ns, 'convert_mins_to_time_option', function( $mins ) use ( $ns ) {
                $hour = $mins / 60;
                $min = $mins % 60;
                $date_sample = '2013-01-01 %02d:%02d:00';
                $timezone = $ns->get_wp_timezone();
                $datetime = new DateTime( sprintf( $date_sample, $hour, $min ), $timezone );
                $option_text = $datetime->format( get_option( 'time_format' ) );
                return $option_text;
            } );

        birch_defn( $ns, 'get_time_options', function( $interval = 15 ) use ( $ns ) {
                $options = array();
                $value = 0;
                $format1 = '%d:%02d AM';
                $format2 = '%d:%02d PM';
                $date_sample = '2013-01-01 %02d:%02d:00';
                for ( $i = 0; $i < 24; $i++ ) {
                    if ( $i === 0 ) {
                        $hour = 12;
                        $format = $format1;
                    } else if ( $i === 12 ) {
                        $hour = 12;
                        $format = $format2;
                    } else if ( $i < 12 ) {
                        $hour = $i;
                        $format = $format1;
                    } else if ( $i > 12 ) {
                        $hour = $i - 12;
                        $format = $format2;
                    }
                    for ( $min = 0; $min < 60; $min += $interval ) {
                        $timezone = $ns->get_wp_timezone();
                        $datetime = new DateTime( sprintf( $date_sample, $i, $min ), $timezone );
                        $option_text = $datetime->format( get_option( 'time_format' ) );
                        $options[$value] = $option_text;
                        $value += $interval;
                    }
                }
                return $options;
            } );

        birch_defn( $ns, 'get_client_title_options', function() use ( $ns ) {
                return array( 'Mr' => __( 'Mr', 'appointer' ),
                    'Mrs' => __( 'Mrs', 'appointer' ),
                    'Miss' => __( 'Miss', 'appointer' ),
                    'Ms' => __( 'Ms', 'appointer' ),
                    'Dr' => __( 'Dr', 'appointer' ) );
            } );

        birch_defn( $ns, 'get_gmt_offset', function() use ( $ns ) {
                return -round( $ns->get_wp_datetime( time() )->getOffset() / 60 );
            } );

        birch_defn( $ns, 'render_html_options', function( $options, $selection = false, $default = false ) use ( $ns ) {
                if ( $selection == false && $default != false ) {
                    $selection = $default;
                }
                foreach ( $options as $val => $text ) {
                    if ( $selection == $val ) {
                        $selected = ' selected="selected" ';
                    } else {
                        $selected = '';
                    }
                    echo "<option value='$val' $selected>$text</option>";
                }
            } );

        birch_defn( $ns, 'get_first_day_of_week', function() use ( $ns ) {
                return get_option( 'start_of_week', 0 );
            } );

        birch_defn( $ns, 'get_fullcalendar_i18n_params', function () use ( $ns ) {
                return array(
                    'firstDay' => $ns->get_first_day_of_week(),
                    'monthNames'=> array(
                        __( 'Jan', 'appointer' ),
                        __( 'Feb', 'appointer' ),
                        __( 'Mar', 'appointer' ),
                        __( 'Apr', 'appointer' ),
                        __( 'May', 'appointer' ),
                        __( 'Jun', 'appointer' ),
                        __( 'Jul', 'appointer' ),
                        __( 'Aug', 'appointer' ),
                        __( 'Sep', 'appointer' ),
                        __( 'Oct', 'appointer' ),
                        __( 'Nov', 'appointer' ),
                        __( 'Dec', 'appointer' )
                    ),
                    'monthNamesShort'=> array(
                        __( 'Jan', 'appointer' ),
                        __( 'Feb', 'appointer' ),
                        __( 'Mar', 'appointer' ),
                        __( 'Apr', 'appointer' ),
                        __( 'May', 'appointer' ),
                        __( 'Jun', 'appointer' ),
                        __( 'Jul', 'appointer' ),
                        __( 'Aug', 'appointer' ),
                        __( 'Sep', 'appointer' ),
                        __( 'Oct', 'appointer' ),
                        __( 'Nov', 'appointer' ),
                        __( 'Dec', 'appointer' )
                    ),
                    'dayNames'=> array(
                        __( 'Sun', 'appointer' ),
                        __( 'Mon', 'appointer' ),
                        __( 'Tue', 'appointer' ),
                        __( 'Wed', 'appointer' ),
                        __( 'Thu', 'appointer' ),
                        __( 'Fri', 'appointer' ),
                        __( 'Sat', 'appointer' )
                    ),
                    'dayNamesShort'=> array(
                        __( 'Sun', 'appointer' ),
                        __( 'Mon', 'appointer' ),
                        __( 'Tue', 'appointer' ),
                        __( 'Wed', 'appointer' ),
                        __( 'Thu', 'appointer' ),
                        __( 'Fri', 'appointer' ),
                        __( 'Sat', 'appointer' )
                    ),
                    'buttonText' => array(
                        'today' => __( 'today', 'appointer' ),
                        'month' => __( 'month', 'appointer' ),
                        'week' => __( 'week', 'appointer' ),
                        'day' => __( 'day', 'appointer' )
                    ),
                    'columnFormat' => array(
                        'month' => 'ddd',    // Mon
                        'week' => 'ddd M/d', // Mon 9/7
                        'day' => 'dddd M/d'  // Monday 9/7
                    ),
                    'titleFormat' => array(
                        'month' => 'MMMM yyyy',                             // September 2009
                        'week' => "MMM d[ yyyy]{ '&#8212;'[ MMM] d yyyy}", // Sep 7 - 13 2009
                        'day' => 'dddd, MMM d, yyyy'                  // Tuesday, Sep 8, 2009
                    ),
                    'timeFormat' => $ns->date_time_format_php_to_fullcalendar( get_option( 'time_format', 'g:i a' ) ),
                    'axisFormat' => $ns->date_time_format_php_to_fullcalendar( get_option( 'time_format', 'g:i a' ) )
                );
            } );

        birch_defn( $ns, 'get_datepicker_i18n_params', function () use ( $ns ) {
                return array(
                    'firstDay' => $ns->get_first_day_of_week(),
                    'monthNames'=> array(
                        __( 'January', 'appointer' ),
                        __( 'February', 'appointer' ),
                        __( 'March', 'appointer' ),
                        __( 'April', 'appointer' ),
                        __( 'May', 'appointer' ),
                        __( 'June', 'appointer' ),
                        __( 'July', 'appointer' ),
                        __( 'August', 'appointer' ),
                        __( 'September', 'appointer' ),
                        __( 'October', 'appointer' ),
                        __( 'November', 'appointer' ),
                        __( 'December', 'appointer' )
                    ),
                    'monthNamesShort'=> array(
                        __( 'Jan', 'appointer' ),
                        __( 'Feb', 'appointer' ),
                        __( 'Mar', 'appointer' ),
                        __( 'Apr', 'appointer' ),
                        __( 'May', 'appointer' ),
                        __( 'Jun', 'appointer' ),
                        __( 'Jul', 'appointer' ),
                        __( 'Aug', 'appointer' ),
                        __( 'Sep', 'appointer' ),
                        __( 'Oct', 'appointer' ),
                        __( 'Nov', 'appointer' ),
                        __( 'Dec', 'appointer' )
                    ),
                    'dayNames'=> array(
                        __( 'Sunday', 'appointer' ),
                        __( 'Monday', 'appointer' ),
                        __( 'Tuesday', 'appointer' ),
                        __( 'Wednesday', 'appointer' ),
                        __( 'Thursday', 'appointer' ),
                        __( 'Friday', 'appointer' ),
                        __( 'Saturday', 'appointer' )
                    ),
                    'dayNamesShort'=> array(
                        __( 'Sun', 'appointer' ),
                        __( 'Mon', 'appointer' ),
                        __( 'Tue', 'appointer' ),
                        __( 'Wed', 'appointer' ),
                        __( 'Thu', 'appointer' ),
                        __( 'Fri', 'appointer' ),
                        __( 'Sat', 'appointer' )
                    ),
                    'dayNamesMin'=> array(
                        __( 'Su', 'appointer' ),
                        __( 'Mo', 'appointer' ),
                        __( 'Tu', 'appointer' ),
                        __( 'We', 'appointer' ),
                        __( 'Th', 'appointer' ),
                        __( 'Fr', 'appointer' ),
                        __( 'Sa', 'appointer' )
                    ),
                    'dateFormat' => $ns->date_time_format_php_to_jquery( get_option( 'date_format' ) )
                );
            } );

        birch_defn( $ns, 'starts_with', function ( $haystack, $needle ) {
                return !strncmp( $haystack, $needle, strlen( $needle ) );
            } );

        birch_defn( $ns, 'ends_with', function ( $haystack, $needle ) {
                $length = strlen( $needle );
                if ( $length == 0 ) {
                    return true;
                }

                return substr( $haystack, -$length ) === $needle;
            } );

        birch_defn( $ns, 'current_page_url', function () use ( $ns ) {
                $pageURL = 'http';
                if ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) {
                    $pageURL .= "s";
                }
                $pageURL .= "://";
                if ( $_SERVER["SERVER_PORT"] != "80" ) {
                    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
                } else {
                    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
                }
                return $pageURL;
            } );

        birch_defn( $ns, 'new_error', function ( $code = '', $message = '', $data = '' ) use ( $ns ) {
                return new WP_Error( $code, $message, $data );
            } );

        birch_defn( $ns, 'add_error_item', function ( $errors, $code, $message, $data = '' ) use( $ns ) {
                $errors->add( $code, $message, $data );
                return $errors;
            } );

        birch_defn( $ns, 'get_error_codes', function ( $errors ) use ( $ns ) {
                return $errors->get_error_codes();
            } );

        birch_defn( $ns, 'get_error_code', function ( $errors ) use( $ns ) {
                return $errors->get_error_code();
            } );

        birch_defn( $ns, 'get_error_message', function ( $errors, $code ) use ( $ns ) {
                return $errors->get_error_message( $code );
            } );

        birch_defn( $ns, 'is_error', function ( $errors ) use ( $ns ) {
                return is_wp_error( $errors );
            } );

        birch_defn( $ns, 'merge_errors', function () use ( $ns ) {
                $errors = $ns->new_error();
                $args = func_get_args();
                foreach ( $args as $arg ) {
                    if ( $ns->is_error( $arg ) ) {
                        $codes = $ns->get_error_codes();
                        foreach ( $codes as $code ) {
                            $message = $ns->get_error_message( $code );
                            $ns->add_error_item( $errors, $code, $message );
                        }
                    }
                    else if ( is_array( $arg ) ) {
                        foreach ( $arg as $code => $message ) {
                            $ns->add_error_item( $errors, $code, $message );
                        }
                    }
                }
                return $errors;
            } );

        birch_defn( $ns, 'urlencode', function ( $arg ) use ( $ns ) {
                if ( is_array( $arg ) ) {
                    $new_array = array();
                    foreach ( $arg as $field_name => $field_value ) {
                        $new_array[$field_name] = $ns->urlencode( $field_value );
                    }
                    return $new_array;
                }
                if ( is_string( $arg ) ) {
                    return urlencode( $arg );
                } else {
                    return $arg;
                }
            } );

        birch_defn($ns, 'get_countries', function() use ($ns) {
                return array(
                    "AF" => __("Afghanistan", 'appointer'),
                    "AL" => __("Albania", 'appointer'),
                    "DZ" => __("Algeria", 'appointer'),
                    "AS" => __("American Samoa", 'appointer'),
                    "AD" => __("Andorra", 'appointer'),
                    "AO" => __("Angola", 'appointer'),
                    "AI" => __("Anguilla", 'appointer'),
                    "AQ" => __("Antarctica", 'appointer'),
                    "AG" => __("Antigua And Barbuda", 'appointer'),
                    "AR" => __("Argentina", 'appointer'),
                    "AM" => __("Armenia", 'appointer'),
                    "AW" => __("Aruba", 'appointer'),
                    "AU" => __("Australia", 'appointer'),
                    "AT" => __("Austria", 'appointer'),
                    "AZ" => __("Azerbaijan", 'appointer'),
                    "BS" => __("Bahamas", 'appointer'),
                    "BH" => __("Bahrain", 'appointer'),
                    "BD" => __("Bangladesh", 'appointer'),
                    "BB" => __("Barbados", 'appointer'),
                    "BY" => __("Belarus", 'appointer'),
                    "BE" => __("Belgium", 'appointer'),
                    "BZ" => __("Belize", 'appointer'),
                    "BJ" => __("Benin", 'appointer'),
                    "BM" => __("Bermuda", 'appointer'),
                    "BT" => __("Bhutan", 'appointer'),
                    "BO" => __("Bolivia", 'appointer'),
                    "BA" => __("Bosnia And Herzegowina", 'appointer'),
                    "BW" => __("Botswana", 'appointer'),
                    "BV" => __("Bouvet Island", 'appointer'),
                    "BR" => __("Brazil", 'appointer'),
                    "IO" => __("British Indian Ocean Territory", 'appointer'),
                    "BN" => __("Brunei Darussalam", 'appointer'),
                    "BG" => __("Bulgaria", 'appointer'),
                    "BF" => __("Burkina Faso", 'appointer'),
                    "BI" => __("Burundi", 'appointer'),
                    "KH" => __("Cambodia", 'appointer'),
                    "CM" => __("Cameroon", 'appointer'),
                    "CA" => __("Canada", 'appointer'),
                    "CV" => __("Cape Verde", 'appointer'),
                    "KY" => __("Cayman Islands", 'appointer'),
                    "CF" => __("Central African Republic", 'appointer'),
                    "TD" => __("Chad", 'appointer'),
                    "CL" => __("Chile", 'appointer'),
                    "CN" => __("China", 'appointer'),
                    "CX" => __("Christmas Island", 'appointer'),
                    "CC" => __("Cocos (Keeling) Islands", 'appointer'),
                    "CO" => __("Colombia", 'appointer'),
                    "KM" => __("Comoros", 'appointer'),
                    "CG" => __("Congo", 'appointer'),
                    "CD" => __("Congo, The Democratic Republic Of The", 'appointer'),
                    "CK" => __("Cook Islands", 'appointer'),
                    "CR" => __("Costa Rica", 'appointer'),
                    "CI" => __("Cote D'Ivoire", 'appointer'),
                    "HR" => __("Croatia (Local Name: Hrvatska)", 'appointer'),
                    "CU" => __("Cuba", 'appointer'),
                    "CY" => __("Cyprus", 'appointer'),
                    "CZ" => __("Czech Republic", 'appointer'),
                    "DK" => __("Denmark", 'appointer'),
                    "DJ" => __("Djibouti", 'appointer'),
                    "DM" => __("Dominica", 'appointer'),
                    "DO" => __("Dominican Republic", 'appointer'),
                    "TP" => __("East Timor", 'appointer'),
                    "EC" => __("Ecuador", 'appointer'),
                    "EG" => __("Egypt", 'appointer'),
                    "SV" => __("El Salvador", 'appointer'),
                    "GQ" => __("Equatorial Guinea", 'appointer'),
                    "ER" => __("Eritrea", 'appointer'),
                    "EE" => __("Estonia", 'appointer'),
                    "ET" => __("Ethiopia", 'appointer'),
                    "FK" => __("Falkland Islands (Malvinas)", 'appointer'),
                    "FO" => __("Faroe Islands", 'appointer'),
                    "FJ" => __("Fiji", 'appointer'),
                    "FI" => __("Finland", 'appointer'),
                    "FR" => __("France", 'appointer'),
                    "FX" => __("France, Metropolitan", 'appointer'),
                    "GF" => __("French Guiana", 'appointer'),
                    "PF" => __("French Polynesia", 'appointer'),
                    "TF" => __("French Southern Territories", 'appointer'),
                    "GA" => __("Gabon", 'appointer'),
                    "GM" => __("Gambia", 'appointer'),
                    "GE" => __("Georgia", 'appointer'),
                    "DE" => __("Germany", 'appointer'),
                    "GH" => __("Ghana", 'appointer'),
                    "GI" => __("Gibraltar", 'appointer'),
                    "GR" => __("Greece", 'appointer'),
                    "GL" => __("Greenland", 'appointer'),
                    "GD" => __("Grenada", 'appointer'),
                    "GP" => __("Guadeloupe", 'appointer'),
                    "GU" => __("Guam", 'appointer'),
                    "GT" => __("Guatemala", 'appointer'),
                    "GN" => __("Guinea", 'appointer'),
                    "GW" => __("Guinea-Bissau", 'appointer'),
                    "GY" => __("Guyana", 'appointer'),
                    "HT" => __("Haiti", 'appointer'),
                    "HM" => __("Heard And Mc Donald Islands", 'appointer'),
                    "VA" => __("Holy See (Vatican City State)", 'appointer'),
                    "HN" => __("Honduras", 'appointer'),
                    "HK" => __("Hong Kong", 'appointer'),
                    "HU" => __("Hungary", 'appointer'),
                    "IS" => __("Iceland", 'appointer'),
                    "IN" => __("India", 'appointer'),
                    "ID" => __("Indonesia", 'appointer'),
                    "IR" => __("Iran (Islamic Republic Of)", 'appointer'),
                    "IQ" => __("Iraq", 'appointer'),
                    "IE" => __("Ireland", 'appointer'),
                    "IL" => __("Israel", 'appointer'),
                    "IT" => __("Italy", 'appointer'),
                    "JM" => __("Jamaica", 'appointer'),
                    "JP" => __("Japan", 'appointer'),
                    "JO" => __("Jordan", 'appointer'),
                    "KZ" => __("Kazakhstan", 'appointer'),
                    "KE" => __("Kenya", 'appointer'),
                    "KI" => __("Kiribati", 'appointer'),
                    "KP" => __("Korea, Democratic People's Republic Of", 'appointer'),
                    "KR" => __("Korea, Republic Of", 'appointer'),
                    "KW" => __("Kuwait", 'appointer'),
                    "KG" => __("Kyrgyzstan", 'appointer'),
                    "LA" => __("Lao People's Democratic Republic", 'appointer'),
                    "LV" => __("Latvia", 'appointer'),
                    "LB" => __("Lebanon", 'appointer'),
                    "LS" => __("Lesotho", 'appointer'),
                    "LR" => __("Liberia", 'appointer'),
                    "LY" => __("Libyan Arab Jamahiriya", 'appointer'),
                    "LI" => __("Liechtenstein", 'appointer'),
                    "LT" => __("Lithuania", 'appointer'),
                    "LU" => __("Luxembourg", 'appointer'),
                    "MO" => __("Macau", 'appointer'),
                    "MK" => __("Macedonia, Former Yugoslav Republic Of", 'appointer'),
                    "MG" => __("Madagascar", 'appointer'),
                    "MW" => __("Malawi", 'appointer'),
                    "MY" => __("Malaysia", 'appointer'),
                    "MV" => __("Maldives", 'appointer'),
                    "ML" => __("Mali", 'appointer'),
                    "MT" => __("Malta", 'appointer'),
                    "MH" => __("Marshall Islands", 'appointer'),
                    "MQ" => __("Martinique", 'appointer'),
                    "MR" => __("Mauritania", 'appointer'),
                    "MU" => __("Mauritius", 'appointer'),
                    "YT" => __("Mayotte", 'appointer'),
                    "MX" => __("Mexico", 'appointer'),
                    "FM" => __("Micronesia, Federated States Of", 'appointer'),
                    "MD" => __("Moldova, Republic Of", 'appointer'),
                    "MC" => __("Monaco", 'appointer'),
                    "MN" => __("Mongolia", 'appointer'),
                    "MS" => __("Montserrat", 'appointer'),
                    "MA" => __("Morocco", 'appointer'),
                    "MZ" => __("Mozambique", 'appointer'),
                    "MM" => __("Myanmar", 'appointer'),
                    "NA" => __("Namibia", 'appointer'),
                    "NR" => __("Nauru", 'appointer'),
                    "NP" => __("Nepal", 'appointer'),
                    "NL" => __("Netherlands", 'appointer'),
                    "AN" => __("Netherlands Antilles", 'appointer'),
                    "NC" => __("New Caledonia", 'appointer'),
                    "NZ" => __("New Zealand", 'appointer'),
                    "NI" => __("Nicaragua", 'appointer'),
                    "NE" => __("Niger", 'appointer'),
                    "NG" => __("Nigeria", 'appointer'),
                    "NU" => __("Niue", 'appointer'),
                    "NF" => __("Norfolk Island", 'appointer'),
                    "MP" => __("Northern Mariana Islands", 'appointer'),
                    "NO" => __("Norway", 'appointer'),
                    "OM" => __("Oman", 'appointer'),
                    "PK" => __("Pakistan", 'appointer'),
                    "PW" => __("Palau", 'appointer'),
                    "PA" => __("Panama", 'appointer'),
                    "PG" => __("Papua New Guinea", 'appointer'),
                    "PY" => __("Paraguay", 'appointer'),
                    "PE" => __("Peru", 'appointer'),
                    "PH" => __("Philippines", 'appointer'),
                    "PN" => __("Pitcairn", 'appointer'),
                    "PL" => __("Poland", 'appointer'),
                    "PT" => __("Portugal", 'appointer'),
                    "PR" => __("Puerto Rico", 'appointer'),
                    "QA" => __("Qatar", 'appointer'),
                    "RE" => __("Reunion", 'appointer'),
                    "RO" => __("Romania", 'appointer'),
                    "RU" => __("Russian Federation", 'appointer'),
                    "RW" => __("Rwanda", 'appointer'),
                    "KN" => __("Saint Kitts And Nevis", 'appointer'),
                    "LC" => __("Saint Lucia", 'appointer'),
                    "VC" => __("Saint Vincent And The Grenadines", 'appointer'),
                    "WS" => __("Samoa", 'appointer'),
                    "SM" => __("San Marino", 'appointer'),
                    "ST" => __("Sao Tome And Principe", 'appointer'),
                    "SA" => __("Saudi Arabia", 'appointer'),
                    "SN" => __("Senegal", 'appointer'),
                    "SC" => __("Seychelles", 'appointer'),
                    "SL" => __("Sierra Leone", 'appointer'),
                    "SG" => __("Singapore", 'appointer'),
                    "SK" => __("Slovakia (Slovak Republic)", 'appointer'),
                    "SI" => __("Slovenia", 'appointer'),
                    "SB" => __("Solomon Islands", 'appointer'),
                    "SO" => __("Somalia", 'appointer'),
                    "ZA" => __("South Africa", 'appointer'),
                    "GS" => __("South Georgia, South Sandwich Islands", 'appointer'),
                    "ES" => __("Spain", 'appointer'),
                    "LK" => __("Sri Lanka", 'appointer'),
                    "SH" => __("St. Helena", 'appointer'),
                    "PM" => __("St. Pierre And Miquelon", 'appointer'),
                    "SD" => __("Sudan", 'appointer'),
                    "SR" => __("Suriname", 'appointer'),
                    "SJ" => __("Svalbard And Jan Mayen Islands", 'appointer'),
                    "SZ" => __("Swaziland", 'appointer'),
                    "SE" => __("Sweden", 'appointer'),
                    "CH" => __("Switzerland", 'appointer'),
                    "SY" => __("Syrian Arab Republic", 'appointer'),
                    "TW" => __("Taiwan", 'appointer'),
                    "TJ" => __("Tajikistan", 'appointer'),
                    "TZ" => __("Tanzania, United Republic Of", 'appointer'),
                    "TH" => __("Thailand", 'appointer'),
                    "TG" => __("Togo", 'appointer'),
                    "TK" => __("Tokelau", 'appointer'),
                    "TO" => __("Tonga", 'appointer'),
                    "TT" => __("Trinidad And Tobago", 'appointer'),
                    "TN" => __("Tunisia", 'appointer'),
                    "TR" => __("Turkey", 'appointer'),
                    "TM" => __("Turkmenistan", 'appointer'),
                    "TC" => __("Turks And Caicos Islands", 'appointer'),
                    "TV" => __("Tuvalu", 'appointer'),
                    "UG" => __("Uganda", 'appointer'),
                    "UA" => __("Ukraine", 'appointer'),
                    "AE" => __("United Arab Emirates", 'appointer'),
                    "GB" => __("United Kingdom", 'appointer'),
                    "US" => __("United States", 'appointer'),
                    "UM" => __("United States Minor Outlying Islands", 'appointer'),
                    "UY" => __("Uruguay", 'appointer'),
                    "UZ" => __("Uzbekistan", 'appointer'),
                    "VU" => __("Vanuatu", 'appointer'),
                    "VE" => __("Venezuela", 'appointer'),
                    "VN" => __("Viet Nam", 'appointer'),
                    "VG" => __("Virgin Islands (British)", 'appointer'),
                    "VI" => __("Virgin Islands (U.S.)", 'appointer'),
                    "WF" => __("Wallis And Futuna Islands", 'appointer'),
                    "EH" => __("Western Sahara", 'appointer'),
                    "YE" => __("Yemen", 'appointer'),
                    "YU" => __("Yugoslavia", 'appointer'),
                    "ZM" => __("Zambia", 'appointer'),
                    "ZW" => __("Zimbabwe", 'appointer')
                );
            });

        birch_defn($ns, 'get_us_states', function() use ($ns) {
                $states = $ns->get_states();
                return $states['US'];
            });

        birch_defn($ns, 'get_states', function() use ($ns) {
                return array(
                    'US' => array(
                        'AL' => __('Alabama (AL)', 'appointer'),
                        'AK' => __('Alaska (AK)', 'appointer'),
                        'AZ' => __('Arizona (AZ)', 'appointer'),
                        'AR' => __('Arkansas (AR)', 'appointer'),
                        'CA' => __('California (CA)', 'appointer'),
                        'CO' => __('Colorado (CO)', 'appointer'),
                        'CT' => __('Connecticut (CT)', 'appointer'),
                        'DC' => __('District of Columbia (DC)', 'appointer'),
                        'DE' => __('Delaware (DE)', 'appointer'),
                        'FL' => __('Florida (FL)', 'appointer'),
                        'GA' => __('Georgia (GA)', 'appointer'),
                        'HI' => __('Hawaii (HI)', 'appointer'),
                        'ID' => __('Idaho (ID)', 'appointer'),
                        'IL' => __('Illinois (IL)', 'appointer'),
                        'IN' => __('Indiana (IN)', 'appointer'),
                        'IA' => __('Iowa (IA)', 'appointer'),
                        'KS' => __('Kansas (KS)', 'appointer'),
                        'KY' => __('Kentucky (KY)', 'appointer'),
                        'LA' => __('Louisiana (LA)', 'appointer'),
                        'ME' => __('Maine (ME)', 'appointer'),
                        'MD' => __('Maryland (MD)', 'appointer'),
                        'MA' => __('Massachusetts (MA)', 'appointer'),
                        'MI' => __('Michigan (MI)', 'appointer'),
                        'MN' => __('Minnesota (MN)', 'appointer'),
                        'MS' => __('Mississippi (MS)', 'appointer'),
                        'MO' => __('Missouri (MO)', 'appointer'),
                        'MT' => __('Montana (MT)', 'appointer'),
                        'NE' => __('Nebraska (NE)', 'appointer'),
                        'NV' => __('Nevada (NV)', 'appointer'),
                        'NH' => __('New Hampshire (NH)', 'appointer'),
                        'NJ' => __('New Jersey (NJ)', 'appointer'),
                        'NM' => __('New Mexico (NM)', 'appointer'),
                        'NY' => __('New York (NY)', 'appointer'),
                        'NC' => __('North Carolina(NC)', 'appointer'),
                        'ND' => __('North Dakota (ND)', 'appointer'),
                        'OH' => __('Ohio (OH)', 'appointer'),
                        'OK' => __('Oklahoma (OK)', 'appointer'),
                        'OR' => __('Oregon (OR)', 'appointer'),
                        'PA' => __('Pennsylvania (PA)', 'appointer'),
                        'PR' => __('Puerto Rico (PR)', 'appointer'),
                        'RI' => __('Rhode Island (RI)', 'appointer'),
                        'SC' => __('South Carolina (SC)', 'appointer'),
                        'SD' => __('South Dakota', 'appointer'),
                        'TN' => __('Tennessee (TN)', 'appointer'),
                        'TX' => __('Texas (TX)', 'appointer'),
                        'UT' => __('Utah (UT)', 'appointer'),
                        'VA' => __('Virginia (VA)', 'appointer'),
                        'VI' => __('Virgin Islands (VI)', 'appointer'),
                        'VT' => __('Vermont (VT)', 'appointer'),
                        'WA' => __('Washington (WA)', 'appointer'),
                        'WV' => __('West Virginia (WV)', 'appointer'),
                        'WI' => __('Wisconsin (WI)', 'appointer'),
                        'WY' => __('Wyoming (WY)', 'appointer')
                    ),
                    'AU' => array(
                        'ACT' => __( 'Australian Capital Territory', 'appointer' ),
                        'NSW' => __( 'New South Wales', 'appointer' ),
                        'NT'  => __( 'Northern Territory', 'appointer' ),
                        'QLD' => __( 'Queensland', 'appointer' ),
                        'SA'  => __( 'South Australia', 'appointer' ),
                        'TAS' => __( 'Tasmania', 'appointer' ),
                        'VIC' => __( 'Victoria', 'appointer' ),
                        'WA'  => __( 'Western Australia', 'appointer' )
                    ),
                    'BR' => array(
                        'AC' => __( 'Acre', 'appointer' ),
                        'AL' => __( 'Alagoas', 'appointer' ),
                        'AP' => __( 'Amap&aacute;', 'appointer' ),
                        'AM' => __( 'Amazonas', 'appointer' ),
                        'BA' => __( 'Bahia', 'appointer' ),
                        'CE' => __( 'Cear&aacute;', 'appointer' ),
                        'DF' => __( 'Distrito Federal', 'appointer' ),
                        'ES' => __( 'Esp&iacute;rito Santo', 'appointer' ),
                        'GO' => __( 'Goi&aacute;s', 'appointer' ),
                        'MA' => __( 'Maranh&atilde;o', 'appointer' ),
                        'MT' => __( 'Mato Grosso', 'appointer' ),
                        'MS' => __( 'Mato Grosso do Sul', 'appointer' ),
                        'MG' => __( 'Minas Gerais', 'appointer' ),
                        'PA' => __( 'Par&aacute;', 'appointer' ),
                        'PB' => __( 'Para&iacute;ba', 'appointer' ),
                        'PR' => __( 'Paran&aacute;', 'appointer' ),
                        'PE' => __( 'Pernambuco', 'appointer' ),
                        'PI' => __( 'Piau&iacute;', 'appointer' ),
                        'RJ' => __( 'Rio de Janeiro', 'appointer' ),
                        'RN' => __( 'Rio Grande do Norte', 'appointer' ),
                        'RS' => __( 'Rio Grande do Sul', 'appointer' ),
                        'RO' => __( 'Rond&ocirc;nia', 'appointer' ),
                        'RR' => __( 'Roraima', 'appointer' ),
                        'SC' => __( 'Santa Catarina', 'appointer' ),
                        'SP' => __( 'S&atilde;o Paulo', 'appointer' ),
                        'SE' => __( 'Sergipe', 'appointer' ),
                        'TO' => __( 'Tocantins', 'appointer' )
                    ),
                    'CA' => array(
                        'AB' => __( 'Alberta', 'appointer' ),
                        'BC' => __( 'British Columbia', 'appointer' ),
                        'MB' => __( 'Manitoba', 'appointer' ),
                        'NB' => __( 'New Brunswick', 'appointer' ),
                        'NF' => __( 'Newfoundland', 'appointer' ),
                        'NT' => __( 'Northwest Territories', 'appointer' ),
                        'NS' => __( 'Nova Scotia', 'appointer' ),
                        'NU' => __( 'Nunavut', 'appointer' ),
                        'ON' => __( 'Ontario', 'appointer' ),
                        'PE' => __( 'Prince Edward Island', 'appointer' ),
                        'QC' => __( 'Quebec', 'appointer' ),
                        'SK' => __( 'Saskatchewan', 'appointer' ),
                        'YT' => __( 'Yukon Territory', 'appointer' )
                    ),
                    'CN' => array(
                        'CN1'  => __( 'Yunnan / &#20113;&#21335;', 'appointer' ),
                        'CN2'  => __( 'Beijing / &#21271;&#20140;', 'appointer' ),
                        'CN3'  => __( 'Tianjin / &#22825;&#27941;', 'appointer' ),
                        'CN4'  => __( 'Hebei / &#27827;&#21271;', 'appointer' ),
                        'CN5'  => __( 'Shanxi / &#23665;&#35199;', 'appointer' ),
                        'CN6'  => __( 'Inner Mongolia / &#20839;&#33945;&#21476;', 'appointer' ),
                        'CN7'  => __( 'Liaoning / &#36797;&#23425;', 'appointer' ),
                        'CN8'  => __( 'Jilin / &#21513;&#26519;', 'appointer' ),
                        'CN9'  => __( 'Heilongjiang / &#40657;&#40857;&#27743;', 'appointer' ),
                        'CN10' => __( 'Shanghai / &#19978;&#28023;', 'appointer' ),
                        'CN11' => __( 'Jiangsu / &#27743;&#33487;', 'appointer' ),
                        'CN12' => __( 'Zhejiang / &#27993;&#27743;', 'appointer' ),
                        'CN13' => __( 'Anhui / &#23433;&#24509;', 'appointer' ),
                        'CN14' => __( 'Fujian / &#31119;&#24314;', 'appointer' ),
                        'CN15' => __( 'Jiangxi / &#27743;&#35199;', 'appointer' ),
                        'CN16' => __( 'Shandong / &#23665;&#19996;', 'appointer' ),
                        'CN17' => __( 'Henan / &#27827;&#21335;', 'appointer' ),
                        'CN18' => __( 'Hubei / &#28246;&#21271;', 'appointer' ),
                        'CN19' => __( 'Hunan / &#28246;&#21335;', 'appointer' ),
                        'CN20' => __( 'Guangdong / &#24191;&#19996;', 'appointer' ),
                        'CN21' => __( 'Guangxi Zhuang / &#24191;&#35199;&#22766;&#26063;', 'appointer' ),
                        'CN22' => __( 'Hainan / &#28023;&#21335;', 'appointer' ),
                        'CN23' => __( 'Chongqing / &#37325;&#24198;', 'appointer' ),
                        'CN24' => __( 'Sichuan / &#22235;&#24029;', 'appointer' ),
                        'CN25' => __( 'Guizhou / &#36149;&#24030;', 'appointer' ),
                        'CN26' => __( 'Shaanxi / &#38485;&#35199;', 'appointer' ),
                        'CN27' => __( 'Gansu / &#29976;&#32899;', 'appointer' ),
                        'CN28' => __( 'Qinghai / &#38738;&#28023;', 'appointer' ),
                        'CN29' => __( 'Ningxia Hui / &#23425;&#22799;', 'appointer' ),
                        'CN30' => __( 'Macau / &#28595;&#38376;', 'appointer' ),
                        'CN31' => __( 'Tibet / &#35199;&#34255;', 'appointer' ),
                        'CN32' => __( 'Xinjiang / &#26032;&#30086;', 'appointer' )
                    ),
                    "ES" => array(
                        'C' => __('A Coru&ntilde;a', 'appointer'),
                        'VI' => __('&Aacute;lava', 'appointer'),
                        'AB' => __('Albacete', 'appointer'),
                        'A' => __('Alicante', 'appointer'),
                        'AL' => __('Almer&iacute;a', 'appointer'),
                        'O' => __('Asturias', 'appointer'),
                        'AV' => __('&Aacute;vila', 'appointer'),
                        'BA' => __('Badajoz', 'appointer'),
                        'PM' => __('Baleares', 'appointer'),
                        'B' => __('Barcelona', 'appointer'),
                        'BU' => __('Burgos', 'appointer'),
                        'CC' => __('C&aacute;ceres', 'appointer'),
                        'CA' => __('C&aacute;diz', 'appointer'),
                        'S' => __('Cantabria', 'appointer'),
                        'CS' => __('Castell&oacute;n', 'appointer'),
                        'CE' => __('Ceuta', 'appointer'),
                        'CR' => __('Ciudad Real', 'appointer'),
                        'CO' => __('C&oacute;rdoba', 'appointer'),
                        'CU' => __('Cuenca', 'appointer'),
                        'GI' => __('Girona', 'appointer'),
                        'GR' => __('Granada', 'appointer'),
                        'GU' => __('Guadalajara', 'appointer'),
                        'SS' => __('Guip&uacute;zcoa', 'appointer'),
                        'H' => __('Huelva', 'appointer'),
                        'HU' => __('Huesca', 'appointer'),
                        'J' => __('Ja&eacute;n', 'appointer'),
                        'LO' => __('La Rioja', 'appointer'),
                        'GC' => __('Las Palmas', 'appointer'),
                        'LE' => __('Le&oacute;n', 'appointer'),
                        'L' => __('Lleida', 'appointer'),
                        'LU' => __('Lugo', 'appointer'),
                        'M' => __('Madrid', 'appointer'),
                        'MA' => __('M&aacute;laga', 'appointer'),
                        'ML' => __('Melilla', 'appointer'),
                        'MU' => __('Murcia', 'appointer'),
                        'NA' => __('Navarra', 'appointer'),
                        'OR' => __('Ourense', 'appointer'),
                        'P' => __('Palencia', 'appointer'),
                        'PO' => __('Pontevedra', 'appointer'),
                        'SA' => __('Salamanca', 'appointer'),
                        'TF' => __('Santa Cruz de Tenerife', 'appointer'),
                        'SG' => __('Segovia', 'appointer'),
                        'SE' => __('Sevilla', 'appointer'),
                        'SO' => __('Soria', 'appointer'),
                        'T' => __('Tarragona', 'appointer'),
                        'TE' => __('Teruel', 'appointer'),
                        'TO' => __('Toledo', 'appointer'),
                        'V' => __('Valencia', 'appointer'),
                        'VA' => __('Valladolid', 'appointer'),
                        'BI' => __('Vizcaya', 'appointer'),
                        'ZA' => __('Zamora', 'appointer'),
                        'Z' => __('Zaragoza', 'appointer')
                    ),
                    'HK' => array(
                        'HONG KONG'       => __( 'Hong Kong Island', 'appointer' ),
                        'KOWLOON'         => __( 'Kowloon', 'appointer' ),
                        'NEW TERRITORIES' => __( 'New Territories', 'appointer' )
                    ),
                    'HU' => array(
                        'BK' => __( 'Bács-Kiskun', 'appointer' ),
                        'BE' => __( 'Békés', 'appointer' ),
                        'BA' => __( 'Baranya', 'appointer' ),
                        'BZ' => __( 'Borsod-Abaúj-Zemplén', 'appointer' ),
                        'BU' => __( 'Budapest', 'appointer' ),
                        'CS' => __( 'Csongrád', 'appointer' ),
                        'FE' => __( 'Fejér', 'appointer' ),
                        'GS' => __( 'Győr-Moson-Sopron', 'appointer' ),
                        'HB' => __( 'Hajdú-Bihar', 'appointer' ),
                        'HE' => __( 'Heves', 'appointer' ),
                        'JN' => __( 'Jász-Nagykun-Szolnok', 'appointer' ),
                        'KE' => __( 'Komárom-Esztergom', 'appointer' ),
                        'NO' => __( 'Nógrád', 'appointer' ),
                        'PE' => __( 'Pest', 'appointer' ),
                        'SO' => __( 'Somogy', 'appointer' ),
                        'SZ' => __( 'Szabolcs-Szatmár-Bereg', 'appointer' ),
                        'TO' => __( 'Tolna', 'appointer' ),
                        'VA' => __( 'Vas', 'appointer' ),
                        'VE' => __( 'Veszprém', 'appointer' ),
                        'ZA' => __( 'Zala', 'appointer' )
                    ),
                    'HZ' => array(
                        'AK' => __( 'Auckland', 'appointer' ),
                        'BP' => __( 'Bay of Plenty', 'appointer' ),
                        'CT' => __( 'Canterbury', 'appointer' ),
                        'HB' => __( 'Hawke&rsquo;s Bay', 'appointer' ),
                        'MW' => __( 'Manawatu-Wanganui', 'appointer' ),
                        'MB' => __( 'Marlborough', 'appointer' ),
                        'NS' => __( 'Nelson', 'appointer' ),
                        'NL' => __( 'Northland', 'appointer' ),
                        'OT' => __( 'Otago', 'appointer' ),
                        'SL' => __( 'Southland', 'appointer' ),
                        'TK' => __( 'Taranaki', 'appointer' ),
                        'TM' => __( 'Tasman', 'appointer' ),
                        'WA' => __( 'Waikato', 'appointer' ),
                        'WE' => __( 'Wellington', 'appointer' ),
                        'WC' => __( 'West Coast', 'appointer' )
                    ),
                    'ID' => array(
                        'AC'    => __( 'Daerah Istimewa Aceh', 'appointer' ),
                        'SU' => __( 'Sumatera Utara', 'appointer' ),
                        'SB' => __( 'Sumatera Barat', 'appointer' ),
                        'RI' => __( 'Riau', 'appointer' ),
                        'KR' => __( 'Kepulauan Riau', 'appointer' ),
                        'JA' => __( 'Jambi', 'appointer' ),
                        'SS' => __( 'Sumatera Selatan', 'appointer' ),
                        'BB' => __( 'Bangka Belitung', 'appointer' ),
                        'BE' => __( 'Bengkulu', 'appointer' ),
                        'LA' => __( 'Lampung', 'appointer' ),
                        'JK' => __( 'DKI Jakarta', 'appointer' ),
                        'JB' => __( 'Jawa Barat', 'appointer' ),
                        'BT' => __( 'Banten', 'appointer' ),
                        'JT' => __( 'Jawa Tengah', 'appointer' ),
                        'JI' => __( 'Jawa Timur', 'appointer' ),
                        'YO' => __( 'Daerah Istimewa Yogyakarta', 'appointer' ),
                        'BA' => __( 'Bali', 'appointer' ),
                        'NB' => __( 'Nusa Tenggara Barat', 'appointer' ),
                        'NT' => __( 'Nusa Tenggara Timur', 'appointer' ),
                        'KB' => __( 'Kalimantan Barat', 'appointer' ),
                        'KT' => __( 'Kalimantan Tengah', 'appointer' ),
                        'KI' => __( 'Kalimantan Timur', 'appointer' ),
                        'KS' => __( 'Kalimantan Selatan', 'appointer' ),
                        'KU' => __( 'Kalimantan Utara', 'appointer' ),
                        'SA' => __( 'Sulawesi Utara', 'appointer' ),
                        'ST' => __( 'Sulawesi Tengah', 'appointer' ),
                        'SG' => __( 'Sulawesi Tenggara', 'appointer' ),
                        'SR' => __( 'Sulawesi Barat', 'appointer' ),
                        'SN' => __( 'Sulawesi Selatan', 'appointer' ),
                        'GO' => __( 'Gorontalo', 'appointer' ),
                        'MA' => __( 'Maluku', 'appointer' ),
                        'MU' => __( 'Maluku Utara', 'appointer' ),
                        'PA' => __( 'Papua', 'appointer' ),
                        'PB' => __( 'Papua Barat', 'appointer' )
                    ),
                    'IN' => array(
                        'AP' => __( 'Andra Pradesh', 'appointer' ),
                        'AR' => __( 'Arunachal Pradesh', 'appointer' ),
                        'AS' => __( 'Assam', 'appointer' ),
                        'BR' => __( 'Bihar', 'appointer' ),
                        'CT' => __( 'Chhattisgarh', 'appointer' ),
                        'GA' => __( 'Goa', 'appointer' ),
                        'GJ' => __( 'Gujarat', 'appointer' ),
                        'HR' => __( 'Haryana', 'appointer' ),
                        'HP' => __( 'Himachal Pradesh', 'appointer' ),
                        'JK' => __( 'Jammu and Kashmir', 'appointer' ),
                        'JH' => __( 'Jharkhand', 'appointer' ),
                        'KA' => __( 'Karnataka', 'appointer' ),
                        'KL' => __( 'Kerala', 'appointer' ),
                        'MP' => __( 'Madhya Pradesh', 'appointer' ),
                        'MH' => __( 'Maharashtra', 'appointer' ),
                        'MN' => __( 'Manipur', 'appointer' ),
                        'ML' => __( 'Meghalaya', 'appointer' ),
                        'MZ' => __( 'Mizoram', 'appointer' ),
                        'NL' => __( 'Nagaland', 'appointer' ),
                        'OR' => __( 'Orissa', 'appointer' ),
                        'PB' => __( 'Punjab', 'appointer' ),
                        'RJ' => __( 'Rajasthan', 'appointer' ),
                        'SK' => __( 'Sikkim', 'appointer' ),
                        'TN' => __( 'Tamil Nadu', 'appointer' ),
                        'TR' => __( 'Tripura', 'appointer' ),
                        'UT' => __( 'Uttaranchal', 'appointer' ),
                        'UP' => __( 'Uttar Pradesh', 'appointer' ),
                        'WB' => __( 'West Bengal', 'appointer' ),
                        'AN' => __( 'Andaman and Nicobar Islands', 'appointer' ),
                        'CH' => __( 'Chandigarh', 'appointer' ),
                        'DN' => __( 'Dadar and Nagar Haveli', 'appointer' ),
                        'DD' => __( 'Daman and Diu', 'appointer' ),
                        'DL' => __( 'Delhi', 'appointer' ),
                        'LD' => __( 'Lakshadeep', 'appointer' ),
                        'PY' => __( 'Pondicherry (Puducherry)', 'appointer' )
                    ),
                    'MY' => array(
                        'JHR' => __( 'Johor', 'appointer' ),
                        'KDH' => __( 'Kedah', 'appointer' ),
                        'KTN' => __( 'Kelantan', 'appointer' ),
                        'MLK' => __( 'Melaka', 'appointer' ),
                        'NSN' => __( 'Negeri Sembilan', 'appointer' ),
                        'PHG' => __( 'Pahang', 'appointer' ),
                        'PRK' => __( 'Perak', 'appointer' ),
                        'PLS' => __( 'Perlis', 'appointer' ),
                        'PNG' => __( 'Pulau Pinang', 'appointer' ),
                        'SBH' => __( 'Sabah', 'appointer' ),
                        'SWK' => __( 'Sarawak', 'appointer' ),
                        'SGR' => __( 'Selangor', 'appointer' ),
                        'TRG' => __( 'Terengganu', 'appointer' ),
                        'KUL' => __( 'W.P. Kuala Lumpur', 'appointer' ),
                        'LBN' => __( 'W.P. Labuan', 'appointer' ),
                        'PJY' => __( 'W.P. Putrajaya', 'appointer' )
                    ),
                    'NZ' => array(
                        'NL' => __( 'Northland', 'appointer' ),
                        'AK' => __( 'Auckland', 'appointer' ),
                        'WA' => __( 'Waikato', 'appointer' ),
                        'BP' => __( 'Bay of Plenty', 'appointer' ),
                        'TK' => __( 'Taranaki', 'appointer' ),
                        'HB' => __( 'Hawke&rsquo;s Bay', 'appointer' ),
                        'MW' => __( 'Manawatu-Wanganui', 'appointer' ),
                        'WE' => __( 'Wellington', 'appointer' ),
                        'NS' => __( 'Nelson', 'appointer' ),
                        'MB' => __( 'Marlborough', 'appointer' ),
                        'TM' => __( 'Tasman', 'appointer' ),
                        'WC' => __( 'West Coast', 'appointer' ),
                        'CT' => __( 'Canterbury', 'appointer' ),
                        'OT' => __( 'Otago', 'appointer' ),
                        'SL' => __( 'Southland', 'appointer')
                    ),
                    'TH' => array(
                        'TH-37' => __( 'Amnat Charoen (&#3629;&#3635;&#3609;&#3634;&#3592;&#3648;&#3592;&#3619;&#3636;&#3597;)', 'appointer' ),
                        'TH-15' => __( 'Ang Thong (&#3629;&#3656;&#3634;&#3591;&#3607;&#3629;&#3591;)', 'appointer' ),
                        'TH-14' => __( 'Ayutthaya (&#3614;&#3619;&#3632;&#3609;&#3588;&#3619;&#3624;&#3619;&#3637;&#3629;&#3618;&#3640;&#3608;&#3618;&#3634;)', 'appointer' ),
                        'TH-10' => __( 'Bangkok (&#3585;&#3619;&#3640;&#3591;&#3648;&#3607;&#3614;&#3617;&#3627;&#3634;&#3609;&#3588;&#3619;)', 'appointer' ),
                        'TH-38' => __( 'Bueng Kan (&#3610;&#3638;&#3591;&#3585;&#3634;&#3628;)', 'appointer' ),
                        'TH-31' => __( 'Buri Ram (&#3610;&#3640;&#3619;&#3637;&#3619;&#3633;&#3617;&#3618;&#3660;)', 'appointer' ),
                        'TH-24' => __( 'Chachoengsao (&#3593;&#3632;&#3648;&#3594;&#3636;&#3591;&#3648;&#3607;&#3619;&#3634;)', 'appointer' ),
                        'TH-18' => __( 'Chai Nat (&#3594;&#3633;&#3618;&#3609;&#3634;&#3607;)', 'appointer' ),
                        'TH-36' => __( 'Chaiyaphum (&#3594;&#3633;&#3618;&#3616;&#3641;&#3617;&#3636;)', 'appointer' ),
                        'TH-22' => __( 'Chanthaburi (&#3592;&#3633;&#3609;&#3607;&#3610;&#3640;&#3619;&#3637;)', 'appointer' ),
                        'TH-50' => __( 'Chiang Mai (&#3648;&#3594;&#3637;&#3618;&#3591;&#3651;&#3627;&#3617;&#3656;)', 'appointer' ),
                        'TH-57' => __( 'Chiang Rai (&#3648;&#3594;&#3637;&#3618;&#3591;&#3619;&#3634;&#3618;)', 'appointer' ),
                        'TH-20' => __( 'Chonburi (&#3594;&#3621;&#3610;&#3640;&#3619;&#3637;)', 'appointer' ),
                        'TH-86' => __( 'Chumphon (&#3594;&#3640;&#3617;&#3614;&#3619;)', 'appointer' ),
                        'TH-46' => __( 'Kalasin (&#3585;&#3634;&#3628;&#3626;&#3636;&#3609;&#3608;&#3640;&#3660;)', 'appointer' ),
                        'TH-62' => __( 'Kamphaeng Phet (&#3585;&#3635;&#3649;&#3614;&#3591;&#3648;&#3614;&#3594;&#3619;)', 'appointer' ),
                        'TH-71' => __( 'Kanchanaburi (&#3585;&#3634;&#3597;&#3592;&#3609;&#3610;&#3640;&#3619;&#3637;)', 'appointer' ),
                        'TH-40' => __( 'Khon Kaen (&#3586;&#3629;&#3609;&#3649;&#3585;&#3656;&#3609;)', 'appointer' ),
                        'TH-81' => __( 'Krabi (&#3585;&#3619;&#3632;&#3610;&#3637;&#3656;)', 'appointer' ),
                        'TH-52' => __( 'Lampang (&#3621;&#3635;&#3611;&#3634;&#3591;)', 'appointer' ),
                        'TH-51' => __( 'Lamphun (&#3621;&#3635;&#3614;&#3641;&#3609;)', 'appointer' ),
                        'TH-42' => __( 'Loei (&#3648;&#3621;&#3618;)', 'appointer' ),
                        'TH-16' => __( 'Lopburi (&#3621;&#3614;&#3610;&#3640;&#3619;&#3637;)', 'appointer' ),
                        'TH-58' => __( 'Mae Hong Son (&#3649;&#3617;&#3656;&#3630;&#3656;&#3629;&#3591;&#3626;&#3629;&#3609;)', 'appointer' ),
                        'TH-44' => __( 'Maha Sarakham (&#3617;&#3627;&#3634;&#3626;&#3634;&#3619;&#3588;&#3634;&#3617;)', 'appointer' ),
                        'TH-49' => __( 'Mukdahan (&#3617;&#3640;&#3585;&#3604;&#3634;&#3627;&#3634;&#3619;)', 'appointer' ),
                        'TH-26' => __( 'Nakhon Nayok (&#3609;&#3588;&#3619;&#3609;&#3634;&#3618;&#3585;)', 'appointer' ),
                        'TH-73' => __( 'Nakhon Pathom (&#3609;&#3588;&#3619;&#3611;&#3600;&#3617;)', 'appointer' ),
                        'TH-48' => __( 'Nakhon Phanom (&#3609;&#3588;&#3619;&#3614;&#3609;&#3617;)', 'appointer' ),
                        'TH-30' => __( 'Nakhon Ratchasima (&#3609;&#3588;&#3619;&#3619;&#3634;&#3594;&#3626;&#3637;&#3617;&#3634;)', 'appointer' ),
                        'TH-60' => __( 'Nakhon Sawan (&#3609;&#3588;&#3619;&#3626;&#3623;&#3619;&#3619;&#3588;&#3660;)', 'appointer' ),
                        'TH-80' => __( 'Nakhon Si Thammarat (&#3609;&#3588;&#3619;&#3624;&#3619;&#3637;&#3608;&#3619;&#3619;&#3617;&#3619;&#3634;&#3594;)', 'appointer' ),
                        'TH-55' => __( 'Nan (&#3609;&#3656;&#3634;&#3609;)', 'appointer' ),
                        'TH-96' => __( 'Narathiwat (&#3609;&#3619;&#3634;&#3608;&#3636;&#3623;&#3634;&#3626;)', 'appointer' ),
                        'TH-39' => __( 'Nong Bua Lam Phu (&#3627;&#3609;&#3629;&#3591;&#3610;&#3633;&#3623;&#3621;&#3635;&#3616;&#3641;)', 'appointer' ),
                        'TH-43' => __( 'Nong Khai (&#3627;&#3609;&#3629;&#3591;&#3588;&#3634;&#3618;)', 'appointer' ),
                        'TH-12' => __( 'Nonthaburi (&#3609;&#3609;&#3607;&#3610;&#3640;&#3619;&#3637;)', 'appointer' ),
                        'TH-13' => __( 'Pathum Thani (&#3611;&#3607;&#3640;&#3617;&#3608;&#3634;&#3609;&#3637;)', 'appointer' ),
                        'TH-94' => __( 'Pattani (&#3611;&#3633;&#3605;&#3605;&#3634;&#3609;&#3637;)', 'appointer' ),
                        'TH-82' => __( 'Phang Nga (&#3614;&#3633;&#3591;&#3591;&#3634;)', 'appointer' ),
                        'TH-93' => __( 'Phatthalung (&#3614;&#3633;&#3607;&#3621;&#3640;&#3591;)', 'appointer' ),
                        'TH-56' => __( 'Phayao (&#3614;&#3632;&#3648;&#3618;&#3634;)', 'appointer' ),
                        'TH-67' => __( 'Phetchabun (&#3648;&#3614;&#3594;&#3619;&#3610;&#3641;&#3619;&#3603;&#3660;)', 'appointer' ),
                        'TH-76' => __( 'Phetchaburi (&#3648;&#3614;&#3594;&#3619;&#3610;&#3640;&#3619;&#3637;)', 'appointer' ),
                        'TH-66' => __( 'Phichit (&#3614;&#3636;&#3592;&#3636;&#3605;&#3619;)', 'appointer' ),
                        'TH-65' => __( 'Phitsanulok (&#3614;&#3636;&#3625;&#3603;&#3640;&#3650;&#3621;&#3585;)', 'appointer' ),
                        'TH-54' => __( 'Phrae (&#3649;&#3614;&#3619;&#3656;)', 'appointer' ),
                        'TH-83' => __( 'Phuket (&#3616;&#3641;&#3648;&#3585;&#3655;&#3605;)', 'appointer' ),
                        'TH-25' => __( 'Prachin Buri (&#3611;&#3619;&#3634;&#3592;&#3637;&#3609;&#3610;&#3640;&#3619;&#3637;)', 'appointer' ),
                        'TH-77' => __( 'Prachuap Khiri Khan (&#3611;&#3619;&#3632;&#3592;&#3623;&#3610;&#3588;&#3637;&#3619;&#3637;&#3586;&#3633;&#3609;&#3608;&#3660;)', 'appointer' ),
                        'TH-85' => __( 'Ranong (&#3619;&#3632;&#3609;&#3629;&#3591;)', 'appointer' ),
                        'TH-70' => __( 'Ratchaburi (&#3619;&#3634;&#3594;&#3610;&#3640;&#3619;&#3637;)', 'appointer' ),
                        'TH-21' => __( 'Rayong (&#3619;&#3632;&#3618;&#3629;&#3591;)', 'appointer' ),
                        'TH-45' => __( 'Roi Et (&#3619;&#3657;&#3629;&#3618;&#3648;&#3629;&#3655;&#3604;)', 'appointer' ),
                        'TH-27' => __( 'Sa Kaeo (&#3626;&#3619;&#3632;&#3649;&#3585;&#3657;&#3623;)', 'appointer' ),
                        'TH-47' => __( 'Sakon Nakhon (&#3626;&#3585;&#3621;&#3609;&#3588;&#3619;)', 'appointer' ),
                        'TH-11' => __( 'Samut Prakan (&#3626;&#3617;&#3640;&#3607;&#3619;&#3611;&#3619;&#3634;&#3585;&#3634;&#3619;)', 'appointer' ),
                        'TH-74' => __( 'Samut Sakhon (&#3626;&#3617;&#3640;&#3607;&#3619;&#3626;&#3634;&#3588;&#3619;)', 'appointer' ),
                        'TH-75' => __( 'Samut Songkhram (&#3626;&#3617;&#3640;&#3607;&#3619;&#3626;&#3591;&#3588;&#3619;&#3634;&#3617;)', 'appointer' ),
                        'TH-19' => __( 'Saraburi (&#3626;&#3619;&#3632;&#3610;&#3640;&#3619;&#3637;)', 'appointer' ),
                        'TH-91' => __( 'Satun (&#3626;&#3605;&#3641;&#3621;)', 'appointer' ),
                        'TH-17' => __( 'Sing Buri (&#3626;&#3636;&#3591;&#3627;&#3660;&#3610;&#3640;&#3619;&#3637;)', 'appointer' ),
                        'TH-33' => __( 'Sisaket (&#3624;&#3619;&#3637;&#3626;&#3632;&#3648;&#3585;&#3625;)', 'appointer' ),
                        'TH-90' => __( 'Songkhla (&#3626;&#3591;&#3586;&#3621;&#3634;)', 'appointer' ),
                        'TH-64' => __( 'Sukhothai (&#3626;&#3640;&#3650;&#3586;&#3607;&#3633;&#3618;)', 'appointer' ),
                        'TH-72' => __( 'Suphan Buri (&#3626;&#3640;&#3614;&#3619;&#3619;&#3603;&#3610;&#3640;&#3619;&#3637;)', 'appointer' ),
                        'TH-84' => __( 'Surat Thani (&#3626;&#3640;&#3619;&#3634;&#3625;&#3598;&#3619;&#3660;&#3608;&#3634;&#3609;&#3637;)', 'appointer' ),
                        'TH-32' => __( 'Surin (&#3626;&#3640;&#3619;&#3636;&#3609;&#3607;&#3619;&#3660;)', 'appointer' ),
                        'TH-63' => __( 'Tak (&#3605;&#3634;&#3585;)', 'appointer' ),
                        'TH-92' => __( 'Trang (&#3605;&#3619;&#3633;&#3591;)', 'appointer' ),
                        'TH-23' => __( 'Trat (&#3605;&#3619;&#3634;&#3604;)', 'appointer' ),
                        'TH-34' => __( 'Ubon Ratchathani (&#3629;&#3640;&#3610;&#3621;&#3619;&#3634;&#3594;&#3608;&#3634;&#3609;&#3637;)', 'appointer' ),
                        'TH-41' => __( 'Udon Thani (&#3629;&#3640;&#3604;&#3619;&#3608;&#3634;&#3609;&#3637;)', 'appointer' ),
                        'TH-61' => __( 'Uthai Thani (&#3629;&#3640;&#3607;&#3633;&#3618;&#3608;&#3634;&#3609;&#3637;)', 'appointer' ),
                        'TH-53' => __( 'Uttaradit (&#3629;&#3640;&#3605;&#3619;&#3604;&#3636;&#3605;&#3606;&#3660;)', 'appointer' ),
                        'TH-95' => __( 'Yala (&#3618;&#3632;&#3621;&#3634;)', 'appointer' ),
                        'TH-35' => __( 'Yasothon (&#3618;&#3650;&#3626;&#3608;&#3619;)', 'appointer' )
                    ),
                    'ZA' => array(
                        'EC'  => __( 'Eastern Cape', 'appointer' ) ,
                        'FS'  => __( 'Free State', 'appointer' ) ,
                        'GP'  => __( 'Gauteng', 'appointer' ) ,
                        'KZN' => __( 'KwaZulu-Natal', 'appointer' ) ,
                        'LP'  => __( 'Limpopo', 'appointer' ) ,
                        'MP'  => __( 'Mpumalanga', 'appointer' ) ,
                        'NC'  => __( 'Northern Cape', 'appointer' ) ,
                        'NW'  => __( 'North West', 'appointer' ) ,
                        'WC'  => __( 'Western Cape', 'appointer' )
                    )
                );
            });

        birch_defn( $ns, 'get_currencies', function() use ( $ns ) {
                return array(
                    'USD' => array('title' => __('U.S. Dollar', 'appointer'), 'code' => 'USD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'EUR' => array('title' => __('Euro', 'appointer'), 'code' => 'EUR', 'symbol_left' => '', 'symbol_right' => '€', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'GBP' => array('title' => __('Pounds Sterling', 'appointer'), 'code' => 'GBP', 'symbol_left' => '£', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'AUD' => array('title' => __('Australian Dollar', 'appointer'), 'code' => 'AUD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'BHD' => array('title' => __('Bahraini dinar', 'appointer'), 'code' => 'BHD', 'symbol_left' => '', 'symbol_right' => 'BD', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '3'),
                    'BRL' => array('title' => __('Brazilian Real', 'appointer'), 'code' => 'BRL', 'symbol_left' => 'R$', 'symbol_right' => '', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                    'CAD' => array('title' => __('Canadian Dollar', 'appointer'), 'code' => 'CAD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'CNY' => array('title' => __('Chinese RMB', 'appointer'), 'code' => 'CNY', 'symbol_left' => '￥', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'CZK' => array('title' => __('Czech Koruna', 'appointer'), 'code' => 'CZK', 'symbol_left' => '', 'symbol_right' => 'Kč', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                    'DKK' => array('title' => __('Danish Krone', 'appointer'), 'code' => 'DKK', 'symbol_left' => '', 'symbol_right' => 'kr', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                    'HKD' => array('title' => __('Hong Kong Dollar', 'appointer'), 'code' => 'HKD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'HUF' => array('title' => __('Hungarian Forint', 'appointer'), 'code' => 'HUF', 'symbol_left' => '', 'symbol_right' => 'Ft', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'INR' => array('title' => __('Indian Rupee', 'appointer'), 'code' => 'INR', 'symbol_left' => 'Rs.', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'ILS' => array('title' => __('Israeli New Shekel', 'appointer'), 'code' => 'ILS', 'symbol_left' => '₪', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'JPY' => array('title' => __('Japanese Yen', 'appointer'), 'code' => 'JPY', 'symbol_left' => '¥', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'MYR' => array('title' => __('Malaysian Ringgit', 'appointer'), 'code' => 'MYR', 'symbol_left' => 'RM', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'MXN' => array('title' => __('Mexican Peso', 'appointer'), 'code' => 'MXN', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'NZD' => array('title' => __('New Zealand Dollar', 'appointer'), 'code' => 'NZD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'NOK' => array('title' => __('Norwegian Krone', 'appointer'), 'code' => 'NOK', 'symbol_left' => 'kr', 'symbol_right' => '', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                    'PHP' => array('title' => __('Philippine Peso', 'appointer'), 'code' => 'PHP', 'symbol_left' => 'Php', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'PLN' => array('title' => __('Polish Zloty', 'appointer'), 'code' => 'PLN', 'symbol_left' => '', 'symbol_right' => 'zł', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                    'RON' => array('title' => __('Romanian leu', 'appointer'), 'code' => 'RON', 'symbol_left' => '', 'symbol_right' => 'ron', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                    'SGD' => array('title' => __('Singapore Dollar', 'appointer'), 'code' => 'SGD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'ZAR' => array('title' => __('South Africa Rand', 'appointer'), 'code' => 'ZAR', 'symbol_left' => 'R', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'SEK' => array('title' => __('Swedish Krona', 'appointer'), 'code' => 'SEK', 'symbol_left' => '', 'symbol_right' => 'kr', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                    'CHF' => array('title' => __('Swiss Franc', 'appointer'), 'code' => 'CHF', 'symbol_left' => '', 'symbol_right' => 'CHF', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                    'TWD' => array('title' => __('Taiwan New Dollar', 'appointer'), 'code' => 'TWD', 'symbol_left' => 'NT$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'THB' => array('title' => __('Thai Baht', 'appointer'), 'code' => 'THB', 'symbol_left' => '', 'symbol_right' => '฿', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                    'TRY' => array('title' => __('Turkish Lira', 'appointer'), 'code' => 'TRY', 'symbol_left' => '', 'symbol_right' => 'TL', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                    'AED' => array('title' => __('United Arab Emirates Dirham', 'appointer'), 'code' => 'AED', 'symbol_left' => '', 'symbol_right' => 'AED', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2')
                );
            });

    } );
