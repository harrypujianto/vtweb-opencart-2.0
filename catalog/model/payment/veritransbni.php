<?php

class ModelPaymentVeritransbni extends Model {
  
  public function getMethod($address, $total) {
		
		$this->load->language('payment/veritransbni');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('veritrans_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('veritransbni_total') > 0 && $this->config->get('veritransbni_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('veritransbni_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
      $method_data = array(
        'code'       => 'veritransbni',
        'title'      => $this->config->get('veritransbni_display_name'),
		'sort_order' => $this->config->get('veritransbni_sort_order'),
		'terms' 	 => ''
      );
    }

    return $method_data;
  }

	public function addToken($data){
		$this->db->query("INSERT INTO `tokens` SET order_id = '" . $data['order_id'] . "', token_merchant = '" .$data['token_merchant']. "', token_browser = '" . $data['token_browser'] . "'");
	}

	public function getTokenMerchant($orderId){
		$merchant_query = $this->db->query("SELECT token_merchant FROM `tokens` WHERE order_id = '" . $orderId . "'");
		$token_merchant='0';

		if ($merchant_query->num_rows) {
			$token_merchant = $merchant_query->row['token_merchant'];
		}

		return $token_merchant;
	}

	public function getTokenBrowser($orderId){
		$browser_query = $this->db->query("SELECT token_browser FROM `tokens` WHERE order_id = '" . $orderId . "'");
		$token_browser='0';

		if ($browser_query->num_rows) {
			$token_browser = $browser_query->row['token_browser'];
		}

		return $token_browser;
	}
}
