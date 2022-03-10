<?php
/**
 * Example of wp_ajax function
 * @package WordPress
 * @author DHL
 * @subpackage new-clean-template-3
 */
add_action( 'wp_ajax_nopriv_get_offer_list_from_crm', 'get_offers_list_from_crm' );
add_action( 'wp_ajax_get_offers_list_from_crm', 'get_offers_list_from_crm' );
function get_offers_list_from_crm() {
	global $wpdb;

	// Get Client ID
	if ( !empty($_COOKIE['client_id']) ) {
		$clientID = sanitize_text_field($_COOKIE['client_id']);
	} else {
		$clientID = 0;
	}

	$args = array(
		'site_id'    => get_field('site_id', 'option'),
		'client_id'  => $clientID,
		'utm_source' => sanitize_text_field($_GET['utm_source'] ?? ''),
		'is_mobile_device' => wp_is_mobile(),
	);
	$url = 'http://admin.densure.ru/offers/get_by_client_id';

	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => http_build_query($args),
		CURLOPT_REFERER => get_home_url()
	));

	$response = curl_exec($ch);
	curl_close($ch);

	// Get Offers ID's
	$result = array();
	$decodedOffersList = json_decode($response, true);

	if ( !empty($decodedOffersList) ) {
		$rows = $wpdb->get_results("SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key = 'admin_panel_offer_id' AND meta_value IN (" . implode(',', $decodedOffersList) . ") ORDER BY FIELD(`meta_value`, " . implode(',', $decodedOffersList) . ")");
		foreach($rows as $row) {
			if ( is_object($row) ) {
				$result[] = $row->post_id;
			}
		}
	}

	return $result;
}
