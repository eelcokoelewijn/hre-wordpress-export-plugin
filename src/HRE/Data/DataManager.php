<?php
namespace HRE\Data;

use HRE;
use HRE\Exporter\Admin;

class DataManager {
	protected $product_id;

	/**
	* Data manager constructor
	*/
	public function __construct() {

	}

	private function fetchRegistations()
	{
		if (isset($this->product_id))
		{
			$sql = "SELECT
			orders.order_id,
			orders.order_item_name,
			orderName.Name,
			orderDetails.meta_key,
			orderDetails.meta_value
			FROM wp_woocommerce_order_items AS orders
			INNER JOIN (
			 SELECT meta.order_item_id FROM wp_woocommerce_order_itemmeta AS meta
					WHERE (meta.meta_key = '_product_id' AND meta.meta_value = {$this->product_id})
			) AS orderProduct ON orderProduct.order_item_id = orders.order_item_id
			INNER JOIN (
				SELECT meta.order_item_id,
				meta.meta_value AS Name
				FROM wp_woocommerce_order_itemmeta AS meta
				WHERE (meta.meta_key = 'Naam')
			) AS orderName ON orderName.order_item_id = orders.order_item_id
			INNER JOIN (
					SELECT meta.order_item_id,
					meta.meta_value,
					meta.meta_key
					FROM wp_woocommerce_order_itemmeta AS meta
					WHERE (meta.meta_key NOT LIKE '\_%')
			) AS orderDetails ON orderDetails.order_item_id = orders.order_item_id
			INNER JOIN wp_posts posts ON orders.order_id = posts.id
			WHERE posts.post_type = 'shop_order'
			AND posts.post_status = 'wc-processing'
			ORDER BY orders.order_id ASC";

			return $GLOBALS['wpdb']->get_results($sql, ARRAY_A);
		}
	}

	public function setProductForRegistrations($product_id)
	{
		$this->product_id = intval($product_id);
	}

	private function exportColumnNames()
	{
		$column_names = null;
		if (array_key_exists($this->product_id, $this::$participant)) {
			$column_names['Naam'] = null;
			$column_names['Woonplaats'] = null;
			$column_names['E-mailadres'] = null;
			$column_names['Telefoon'] = null;
			$column_names['Telefoon bij Calamiteiten'] = null;
			$column_names['Geboortedatum'] = null;
			$column_names['Geslacht'] = null;
			$column_names['Flessenpost'] = null;
			$column_names['Ambitie'] = null;
			$column_names['NTB Licentie'] = null;
			$column_names['MyLaps Chip'] = null;
			$column_names['MyLaps Chipnummer'] = null;
			$column_names['Diner Zaterdag'] = null;
			$column_names['Kamperen (gratis voor deelnemers en vrijwilligers)'] = null;
			$column_names['Ontbijt Zondag'] = null;
			$column_names['NTB Licentienummer'] = null;
			$column_names['Ontbijt Zaterdag'] = null;
			$column_names['Bring & Braai (Vrijdag)'] = null;
		} else if (array_key_exists($this->product_id, $this::$visitor)) {
			$column_names['Naam'] = null;
			$column_names['E-mailadres'] = null;
			$column_names['Flessenpost'] = null;
			$column_names['Diner Zaterdag'] = null;
			$column_names['Kamperen "Zaterdag/Zondag"'] = null;
			$column_names['Bring & Braai (Vrijdag)'] = null;
		} else if (array_key_exists($this->product_id, $this::$volunteer)) {
			$column_names['Naam'] = null;
			$column_names['Adres'] = null;
			$column_names['E-mailadres'] = null;
			$column_names['Telefoon'] = null;
			$column_names['Geboortedatum'] = null;
			$column_names['Flessenpost'] = null;
			$column_names['Bij welk onderdeel zou je willen helpen?'] = null;
			$column_names['Zelfstandigheid'] = null;
			$column_names['Ik werk graag samen met:'] = null;
			$column_names['De organisatie mag wat mij betreft met het volgende rekening houden'] = null;
			$column_names['Diner Zaterdag (gratis voor vrijwilligers)'] = null;
			$column_names['Kamperen (gratis voor deelnemers en vrijwilligers)'] = null;
			$column_names['Ontbijt Zondag'] = null;
		}
		return $column_names;
	}

/**
* Create report name for export
*
* @return String Name for export
*/
	public function exportName()
	{
		$admin = new Admin("HRE Exporter DataManager","1.0");
		$products = $admin->hre_get_product_ids();

		if (array_key_exists($this->product_id, $products)) {
			return filter_var('report-'.$products[$this->product_id], FILTER_SANITIZE_URL);
		}
		return 'report';
	}

	public function data()
	{
		$registrations = $this->fetchRegistations();

		$order_id= 0;
		$newRegistration = false;
		$currentName = '';
		$data = array();
		$results = array();
		foreach ($registrations as $registration) {
			if ($registration['order_id'] != $order_id || $currentName != $registration['Name']) {
				if ($data) {
					$data['order_id'] = $order_id;
					$results[] = array_merge($this->exportColumnNames(), $data);
					$data = null;
					$newRegistration = false;
				}
				$order_id = $registration['order_id'];
				$currentName = $registration['Name'];
			}
			$data[$registration['meta_key']] = $registration['meta_value'];
		}
		return $results;

	}
}
