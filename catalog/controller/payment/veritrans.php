<?php

require_once(DIR_SYSTEM . 'library/veritrans-php/Veritrans.php');

class ControllerPaymentVeritrans extends Controller {

  public function index() {

    $data['errors'] = array();
    $data['button_confirm'] = $this->language->get('button_confirm');
	
  	$data['pay_type'] = $this->config->get('veritrans_payment_type');
  	
  	$data['process_order'] = $this->url->link('payment/veritrans/process_order');
  	
    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/veritrans.tpl')) {
        return $this->load->view($this->config->get('config_template') . '/template/payment/veritrans.tpl',$data);
  	} else {
  	  return $this->load->view('default/template/payment/veritrans.tpl', $data);
  	}

  }

  /**
   * Called when a customer checkouts.
   * If it runs successfully, it will redirect to VT-Web payment page.
   */
  public function process_order() {
    $this->load->model('payment/veritrans');
    $this->load->model('checkout/order');
    $this->load->model('total/shipping');
    $this->load->language('payment/veritrans');

    $data['errors'] = array();

    $data['button_confirm'] = $this->language->get('button_confirm');

    $order_info = $this->model_checkout_order->getOrder(
        $this->session->data['order_id']);

    $this->model_checkout_order->addOrderHistory($this->session->data['order_id'],
        $this->config->get('veritrans_vtweb_challenge_mapping'));

    $transaction_details                 = array();
    $transaction_details['order_id']     = $this->session->data['order_id'];
    $transaction_details['gross_amount'] = $order_info['total'];

    $billing_address                 = array();
    $billing_address['first_name']   = $order_info['payment_firstname'];
    $billing_address['last_name']    = $order_info['payment_lastname'];
    $billing_address['address']      = $order_info['payment_address_1'];
    $billing_address['city']         = $order_info['payment_city'];
    $billing_address['postal_code']  = $order_info['payment_postcode'];
    $billing_address['country_code'] = $order_info['payment_iso_code_3'];
    $billing_address['phone']        = $order_info['telephone'];

    if ($this->cart->hasShipping()) {
      $shipping_address = array();
      $shipping_address['first_name']   = $order_info['shipping_firstname'];
      $shipping_address['last_name']    = $order_info['shipping_lastname'];
      $shipping_address['address']      = $order_info['shipping_address_1'];
      $shipping_address['city']         = $order_info['shipping_city'];
      $shipping_address['postal_code']  = $order_info['shipping_postcode'];
      $shipping_address['phone']        = $order_info['telephone'];
      $shipping_address['country_code'] = $order_info['payment_iso_code_3'];
    } else {
      $shipping_address = $billing_address;
    }

    $customer_details                     = array();
    $customer_details['billing_address']  = $billing_address;
    $customer_details['shipping_address'] = $shipping_address;
    $customer_details['first_name']       = $order_info['payment_firstname'];
    $customer_details['last_name']        = $order_info['payment_lastname'];
    $customer_details['email']            = $order_info['email'];
    $customer_details['phone']            = $order_info['telephone'];

    $products = $this->cart->getProducts();
    
    $item_details = array();

    foreach ($products as $product) {
      if (($this->config->get('config_customer_price')
            && $this->customer->isLogged())
          || !$this->config->get('config_customer_price')) {
        $product['price'] = $this->tax->calculate(
            $product['price'],
            $product['tax_class_id'],
            $this->config->get('config_tax'));
      }

      $item = array(
          'id'       => $product['product_id'],
          'price'    => $product['price'],
          'quantity' => $product['quantity'],
          'name'     => $product['name']
        );
      $item_details[] = $item;
    }

    unset($product);

    $num_products = count($item_details);

    if ($this->cart->hasShipping()) {
      $shipping_info = $this->session->data['shipping_method'];
      if (($this->config->get('config_customer_price')
            && $this->customer->isLogged())
          || !$this->config->get('config_customer_price')) {
        $shipping_info['cost'] = $this->tax->calculate(
            $shipping_info['cost'],
            $shipping_info['tax_class_id'],
            $this->config->get('config_tax'));
      }

      $shipping_item = array(
          'id'       => 'SHIPPING',
          'price'    => $shipping_info['cost'],
          'quantity' => 1,
          'name'     => 'SHIPPING'
        );
      $item_details[] = $shipping_item;
    }

    // convert all item prices to IDR
    if ($this->config->get('config_currency') != 'IDR') {
      if ($this->currency->has('IDR')) {
        foreach ($item_details as &$item) {
          $item['price'] = intval($this->currency->convert(
              $item['price'],
              $this->config->get('config_currency'),
              'IDR'
            ));
        }
        unset($item);

        $transaction_details['gross_amount'] = intval($this->currency->convert(
            $transaction_details['gross_amount'],
            $this->config->get('config_currency'),
            'IDR'
          ));
      }
      else if ($this->config->get('veritrans_currency_conversion') > 0) {
        foreach ($item_details as &$item) {
          $item['price'] = intval($item['price']
              * $this->config->get('veritrans_currency_conversion'));
        }
        unset($item);

        $transaction_details['gross_amount'] = intval(
            $transaction_details['gross_amount']
            * $this->config->get('veritrans_currency_conversion'));
      }
      else {
        $data['errors'][] = "Either the IDR currency is not installed or "
            . "the Veritrans currency conversion rate is valid. "
            . "Please review your currency setting.";
      }
    }

    $total_price = 0;
    foreach ($item_details as $item) {
      $total_price += $item['price'] * $item['quantity'];
    }

    if ($total_price != $transaction_details['gross_amount']) {
      $coupon_item = array(
          'id'       => 'COUPON',
          'price'    => $transaction_details['gross_amount'] - $total_price,
          'quantity' => 1,
          'name'     => 'COUPON'
        );
      $item_details[] = $coupon_item;
    }

    Veritrans_Config::$serverKey = $this->config->
        get('veritrans_server_key_v2');

    Veritrans_Config::$isProduction =
        $this->config->get('veritrans_environment') == 'production'
        ? true : false;

    Veritrans_Config::$is3ds = $this->config->get('veritrans_3d_secure') == 'on'
        ? true : false;
	
    Veritrans_Config::$isSanitized =
        $this->config->get('veritrans_sanitization') == 'on'
        ? true : false;

    $payloads = array();
    $payloads['transaction_details'] = $transaction_details;
    $payloads['item_details']        = $item_details;
    $payloads['customer_details']    = $customer_details;

    try {
      $enabled_payments = array();
      if ($this->config->has('veritrans_enabled_payments')) {
        foreach ($this->config->get('veritrans_enabled_payments')
            as $key => $value) {
          $enabled_payments[] = $key;
        }
      }
      if (empty($enabled_payments)) {
        $enabled_payments[] = 'credit_card';
      }

      $payloads['vtweb']['enabled_payments'] = $enabled_payments;
      $is_installment = false;
      error_log($this->config->get('veritrans_installment_option'));
      if ($this->config->get('veritrans_installment_option') == 'all_product') 
      {
        $payment_options = array(
          'installment' => array(
            'required' => false
          )
        );

        if ($this->config->has('veritrans_installment_banks')) {
          $installment_terms = array();

          foreach ($this->config->get('veritrans_installment_banks')
              as $key => $value) {
			    $a = $this->config->get('veritrans_installment_' . $key . '_term');
			    $term_array = explode(',', $a);
            $installment_terms[$key] = $term_array;
          }

          $payment_options['installment']['installment_terms'] = $installment_terms;
        }

        if ($transaction_details['gross_amount'] >= 500000) {
          $payloads['vtweb']['payment_options'] = $payment_options;
        }
      }
      else if ($this->config->get('veritrans_installment_option') == 'certain_product')
      {

        $payment_options = array(
          'installment' => array(
            'required' => true
          )
        );
		
        $installment_terms = array();

        foreach ($products as $product)
        {
          //$options = $product['option'];
		  
  		  	foreach ($product['option'] as $option) 
          {
        			if ($option['name'] == 'Payment') 
              {   
    		    
             	 $installment_value = explode(' ', $option['value']);
               error_log($installment_value[0]);
               error_log($installment_value[1]);
    			     error_log($installment_value[2]);
                  if (strtolower($installment_value[0]) == 'installment') 
                  {
                    $is_installment = true;
                    $installment_terms[strtolower($installment_value[1])]
                      = array($installment_value[2]);
                  }
              }

  			   }		  
		  
        }

        if ($is_installment && ($num_products == 1)
            && ($transaction_details['gross_amount'] >= 500000)) {
          $payment_options['installment']['installment_terms'] = $installment_terms;
          $payloads['vtweb']['payment_options'] = $payment_options;
        }
      }

      $redirUrl = Veritrans_VtWeb::getRedirectionUrl($payloads);
      
      if ($is_installment) {
        $warningUrl = 'index.php?route=information/warning&redirLink=';

        if ($num_products > 1) {
          $redirUrl = $warningUrl . $redirUrl . '&message=1';
        }
        else if ($transaction_details['gross_amount'] < 500000) {
          $redirUrl = $warningUrl . $redirUrl . '&message=2';
        }
      }
      else if ($this->config->get('veritrans_installment_option') == 'all_product' &&
          ($transaction_details['gross_amount'] < 500000)) {
        $warningUrl = 'index.php?route=information/warning&redirLink=';
        $redirUrl = $warningUrl . $redirUrl . '&message=2';
      }

      // $this->cart->clear();
      $this->response->redirect($redirUrl);
    }
    catch (Exception $e) {
      $data['errors'][] = $e->getMessage();
      error_log($e->getMessage());
      echo $e->getMessage();
    }
  }

  /**
   * Landing page when payment is finished or failure or customer pressed "back" button
   * The Cart is cleared here, so make sure customer reach this page to ensure the cart is emptied when payment succeed
   * payment finish/unfinish/error url : 
   * http://[your shop’s homepage]/index.php?route=payment/veritrans/payment_notification
   */
  public function landing_redir() {
    
    $redirUrl = $this->config->get('config_ssl');
    error_log('redir url: '.$redirUrl); //debugan

    if( isset($_GET['order_id']) && isset($_GET['transaction_status']) && ($_GET['transaction_status'] == 'capture' || $_GET['transaction_status'] == 'pending' || $_GET['transaction_status'] == 'settlement')) {
      //if capture or pending or challenge or settlement, redirect to order received page
      $this->cart->clear();
      $redirUrl = $this->url->link('checkout/success&');
      $this->response->redirect($redirUrl);

    }else if( isset($_GET['order_id']) && isset($_GET['transaction_status']) && $_GET['transaction_status'] == 'deny') {
      //if deny, redirect to order checkout page again
      // $redirUrl = $this->url->link('checkout/cart');
      $redirUrl = $this->url->link('payment/veritrans/failure','','SSL');
      $this->response->redirect($redirUrl);

    }else if( isset($_GET['order_id']) && !isset($_GET['transaction_status'])){ 
      // if customer click "back" button, redirect to checkout page again
      $redirUrl = $this->url->link('checkout/cart');
      $this->response->redirect($redirUrl);
    }
    $this->response->redirect($redirUrl);
  }
  
  /*
  * redirect to payment failure using template & language (text template)
  */
  public function failure() {
    $this->load->language('payment/veritrans');

    $this->document->setTitle($this->language->get('heading_title'));

    $data['heading_title'] = $this->language->get('heading_title');
    $data['text_failure'] = $this->language->get('text_failure');

    $data['column_left'] = $this->load->controller('common/column_left');
    $data['column_right'] = $this->load->controller('common/column_right');
    $data['content_top'] = $this->load->controller('common/content_top');
    $data['content_bottom'] = $this->load->controller('common/content_bottom');
    $data['footer'] = $this->load->controller('common/footer');
    $data['header'] = $this->load->controller('common/header');
    $data['checkout_url'] = $this->url->link('checkout/cart');

    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/veritrans_checkout_failure.tpl')) {
      $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/payment/veritrans_checkout_failure.tpl', $data));
    } else {
      $this->response->setOutput($this->load->view('default/template/payment/veritrans_checkout_failure.tpl', $data));
    }
  }

  /**
   * Called when Veritrans server sends notification to this server.
   * It will change order status according to transaction status and fraud
   * status sent by Veritrans server.
   */
  public function payment_notification() {
    header("HTTP/1.1 200 OK");

    Veritrans_Config::$isProduction = $this->config->get('veritrans_environment') == 'production' ? true : false;
    Veritrans_Config::$serverKey = $this->config->get('veritrans_server_key_v2');

    $this->load->model('checkout/order');
    $this->load->model('payment/veritrans');
    $notif = new Veritrans_Notification();
    $transaction = $notif->transaction_status;
    $fraud = $notif->fraud_status;
    $payment_type = $notif->payment_type;

    $logs = '';
    // error_log(print_r($notif,true)); // debugan
    if ($transaction == 'capture') {
      $logs .= 'capture ';
      if ($fraud == 'challenge') {
        $logs .= 'challenge ';
        $this->model_checkout_order->addOrderHistory(
            $notif->order_id,
            $this->config->get('veritrans_vtweb_challenge_mapping'),
            'VT-Web payment challenged. Please take action on '
              . 'your Merchant Administration Portal.');
      }
      else if ($fraud == 'accept') {
        $logs .= 'accept ';
        $this->model_checkout_order->addOrderHistory(
            $notif->order_id,
            $this->config->get('veritrans_vtweb_success_mapping'),
            'VT-Web payment successful.');
      }
    }
    else if ($transaction == 'cancel') {
        $logs .= 'cancel ';
        $this->model_checkout_order->addOrderHistory(
            $notif->order_id,
            $this->config->get('veritrans_vtweb_failure_mapping'),
            'VT-Web payment canceled.');
    }
    else if ($transaction == 'deny') {
      $logs .= 'deny ';
      $this->model_checkout_order->addOrderHistory(
          $notif->order_id,
          $this->config->get('veritrans_vtweb_failure_mapping'),
          'VT-Web payment failed.');
    }    
	  else if ($transaction == 'pending') {
      $logs .= 'pending ';
      $this->model_checkout_order->addOrderHistory(
          $notif->order_id,
          $this->config->get('veritrans_vtweb_challenge_mapping'),
          'VT-Web payment pending.');
    }
    else if ($transaction == 'settlement') {
          if($payment_type != 'credit_card'){
              $logs .= 'complete ';
              $this->model_checkout_order->addOrderHistory(
              $notif->order_id,
              $this->config->get('veritrans_vtweb_success_mapping'),
              'VT-Web payment successful.');
          }
    }
    else {
      $logs .= "*$transaction:$fraud ";
      $this->model_checkout_order->addOrderHistory(
          $notif->order_id,
          $this->config->get('veritrans_vtweb_challenge_mapping'),
          'VT-Web payment challenged. Please take action on '
            . 'your Merchant Administration Portal.');
    }
    error_log($logs); //debugan to be commented
  }
}