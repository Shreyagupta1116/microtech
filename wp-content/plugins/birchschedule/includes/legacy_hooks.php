<?php

add_filter('appointer_view_bookingform_validate_booking_info', 'appointer_validate_booking_form_info', 100);

function appointer_validate_booking_form_info($errors) {
	return apply_filters('appointer_validate_booking_form_info', $errors);
}

add_filter('appointer_model_get_future_time', 'appointer_booking_preferences_future_time',100);

function appointer_booking_preferences_future_time($future_time) {
	return apply_filters('appointer_booking_preferences_future_time', $future_time);
}

add_filter('appointer_enotification_has_attachment', 'appointer_notification_has_attachment', 100, 3);

function appointer_notification_has_attachment($attach, $to, $template_name) {
	return apply_filters('appointer_notification_has_attachment', $attach, $to, $template_name);
}

add_filter('appointer_enotification_if_appointment_changed', 
	'appointer_notification_appointment_changed', 100, 4);

function appointer_notification_appointment_changed($changed, $new_appointment, $old_appointment, $to) {
	$new_appointment = json_decode(json_encode($new_appointment), false);
	$old_appointment = json_decode(json_encode($old_appointment), false);
	return apply_filters('appointer_notification_appointment_changed', 
		$changed, $new_appointment, $old_appointment, $to);
}

add_filter('birchpress_util_get_datetime_separator', 'appointer_datetime_separator', 100);

function appointer_datetime_separator($separator) {
	return apply_filters('appointer_datetime_separator', $separator);
}

add_filter('appointer_model_schedule_get_staff_busy_time', 'appointer_staff_busy_time', 100, 4);

function appointer_staff_busy_time($busy_time, $staff_id, $location_id, $date) {
	return apply_filters('appointer_staff_busy_time', $busy_time, $staff_id, $location_id, $date);
}

add_filter('appointer_model_schedule_get_staff_avaliable_time', 
	'appointer_booking_time_options', 100, 5);

function appointer_booking_time_options($avaliable_times, $staff_id, 
	$location_id, $service_id, $date) {
	return apply_filters('appointer_booking_time_options', $avaliable_times, $service_id, $date);
}

add_filter('appointer_view_bookingform_get_booking_response', 
	'appointer_ajax_booking_response', 100, 3);

function appointer_ajax_booking_response($response, $appointment_id, $errors) {
	return apply_filters('appointer_ajax_booking_response', $response, $appointment_id, $errors);
}

add_filter('appointer_model_mergefields_get_appointment_merge_values', 
	'appointer_get_appointment_merge_fields_values', 100, 2);

function appointer_get_appointment_merge_fields_values($appointment, $appointment_id) {
	return apply_filters('appointer_get_appointment_merge_fields_values', $appointment, $appointment_id);
}

add_filter('appointer_icalendar_get_appointment_export_template',
	'appointer_appointment_export_template', 100);

function appointer_appointment_export_template($template) {
	return apply_filters('appointer_appointment_export_template', $template);
}

add_filter('appointer_model_get_services_listing_order', 
	'appointer_service_listing_order', 100);

function appointer_service_listing_order($order) {
	return apply_filters('appointer_service_listing_order', $order);
}

add_filter('appointer_view_bookingform_get_fields_html',
	'appointer_booking_form_fields', 100);

function appointer_booking_form_fields($html) {
	return apply_filters('appointer_booking_form_fields', $html);
}

