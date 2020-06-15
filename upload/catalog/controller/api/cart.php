<?php

const FC1 = 'Free Cart 1';
const FC1D = 'Free Cart 1 Description';
const PC1 = 'Paid Cart 1';
const PC1D = 'youtube.com/1';

const FC2 = 'Free Cart 2';
const FC2D = 'Free Cart 2 Description';
const PC2 = 'Paid Cart 2';
const PC2D = 'youtube.com/2';

const FC3 = 'Free Cart 3';
const FC3D = 'Free Cart 3 Description';
const PC3 = 'Paid Cart 3';
const PC3D = 'youtube.com/3';

const FC4 = 'Free Cart 4';
const FC4D = 'Free Cart 4 Description';
const PC4 = 'Paid Cart 4';
const PC4D = 'youtube.com/4';

const FC5 = 'Free Cart 5';
const FC5D = 'Free Cart 5 Description';
const PC5 = 'Paid Cart 5';
const PC5D = 'youtube.com/5';

const FC6 = 'Free Cart 6';
const FC6D = 'Free Cart 6 Description';
const PC6 = 'Paid Cart 6';
const PC6D = 'youtube.com/6';

const FC7 = 'Free Cart 7';
const FC7D = 'Free Cart 7 Description';
const PC7 = 'Paid Cart 7';
const PC7D = 'youtube.com/7';

const FC8 = 'Free Cart 8';
const FC8D = 'Free Cart 8 Description';
const PC8 = 'Paid Cart 8';
const PC8D = 'youtube.com/8';

const FC9 = 'Free Cart 9';
const FC9D = 'Free Cart 9 Description';
const PC9 = 'Paid Cart 9';
const PC9D = 'youtube.com/9';

const FC10 = 'Free Cart 10';
const FC10D = 'Free Cart 10 Description';
const PC10 = 'Paid Cart 10';
const PC10D = 'youtube.com/10';


class ControllerApiCart extends Controller {
	public function add() {
		$this->load->language('api/cart');

		$json = array();

        if (!isset($this->session->data['token']) && strlen($_REQUEST['token']) == 0) {
			$json['error']['warning'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->post['product'])) {
				$this->cart->clear();

				foreach ($this->request->post['product'] as $product) {
					if (isset($product['option'])) {
						$option = $product['option'];
					} else {
						$option = array();
					}

					$this->cart->add($product['product_id'], $product['quantity'], $option);
				}

				$json['success'] = $this->language->get('text_success');

				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
			} elseif (isset($this->request->post['product_id'])) {
				$this->load->model('catalog/product');

				$product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);

				if ($product_info) {
					if (isset($this->request->post['quantity'])) {
						$quantity = $this->request->post['quantity'];
					} else {
						$quantity = 1;
					}

					if (isset($this->request->post['option'])) {
						$option = array_filter($this->request->post['option']);
					} else {
						$option = array();
					}

					$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);

					foreach ($product_options as $product_option) {
						if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
							$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
						}
					}

					if (!isset($json['error']['option'])) {
						$this->cart->add($this->request->post['product_id'], $quantity, $option);

						$json['success'] = $this->language->get('text_success');

						unset($this->session->data['shipping_method']);
						unset($this->session->data['shipping_methods']);
						unset($this->session->data['payment_method']);
						unset($this->session->data['payment_methods']);
					}
				} else {
					$json['error']['store'] = $this->language->get('error_store');
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function edit() {
		$this->load->language('api/cart');

		$json = array();

		if (!isset($this->session->data['token']) && strlen($_REQUEST['token']) == 0) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->cart->update($this->request->post['key'], $this->request->post['quantity']);

			$json['success'] = $this->language->get('text_success');

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['reward']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function remove() {
		$this->load->language('api/cart');

		$json = array();

		if (!isset($this->session->data['token']) && strlen($_REQUEST['token']) == 0) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			// Remove
			if (isset($this->request->post['key'])) {
				$this->cart->remove($this->request->post['key']);

				unset($this->session->data['vouchers'][$this->request->post['key']]);

				$json['success'] = $this->language->get('text_success');

				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
				unset($this->session->data['reward']);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function products() {
		$this->load->language('api/cart');

		$json = array();

		if (!isset($this->session->data['token']) && strlen($_REQUEST['token']) == 0) {
			$json['error']['warning'] = $this->language->get('error_permission');
		} else {
			// Stock
			if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
				$json['error']['stock'] = $this->language->get('error_stock');
			}

			// Products
			$json['products'] = array();

			$products = $this->cart->getProducts();

			foreach ($products as $product) {
				$product_total = 0;

				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}

				if ($product['minimum'] > $product_total) {
					$json['error']['minimum'][] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
				}

				$option_data = array();

				foreach ($product['option'] as $option) {
					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'name'                    => $option['name'],
						'value'                   => $option['value'],
						'type'                    => $option['type']
					);
				}

				$json['products'][] = array(
					'cart_id'    => $product['cart_id'],
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'quantity'   => $product['quantity'],
					'stock'      => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
					'shipping'   => $product['shipping'],
					'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
					'total'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']),
					'reward'     => $product['reward']
				);
			}

			// Voucher
			$json['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $key => $voucher) {
					$json['vouchers'][] = array(
						'code'             => $voucher['code'],
						'description'      => $voucher['description'],
						'from_name'        => $voucher['from_name'],
						'from_email'       => $voucher['from_email'],
						'to_name'          => $voucher['to_name'],
						'to_email'         => $voucher['to_email'],
						'voucher_theme_id' => $voucher['voucher_theme_id'],
						'message'          => $voucher['message'],
						'price'            => $this->currency->format($voucher['amount'], $this->session->data['currency']),			
						'amount'           => $voucher['amount']
					);
				}
			}

			// Totals
			$this->load->model('setting/extension');

			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;

			// Because __call can not keep var references so we put them into an array. 
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);
			
			$sort_order = array();

			$results = $this->model_setting_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get('total_' . $result['code'] . '_status')) {
					$this->load->model('extension/total/' . $result['code']);
					
					// We have to put the totals in an array so that they pass by reference.
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
			}

			$sort_order = array();

			foreach ($totals as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $totals);

			$json['totals'] = array();

			foreach ($totals as $total) {
				$json['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
				);
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

    public function getFreeCard() {
        $this->load->language('api/cart');

        $json = array();

        if(!isset($_REQUEST['token'])){
            $json['error'] = $this->language->get('error_token');
        } else {
            if (!isset($this->session->data['token']) && strlen($_REQUEST['token']) == 0) {
                $json['error'] = $this->language->get('error_permission');
            } else {
                $data = [
                    'FC1' => FC1,
                    'FC2' => FC2,
                    'FC3' => FC3,
                    'FC4' => FC4,
                    'FC5' => FC5,
                    'FC6' => FC6,
                    'FC7' => FC7,
                    'FC8' => FC8,
                    'FC9' => FC9,
                    'FC10' => FC10,
                ];
                $json['card'] = $data;
                $json['success'] = $this->language->get('free_cart_success');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getPaidCard() {
        $this->load->language('api/cart');

        $json = array();

        if(!isset($_REQUEST['token'])){
            $json['error'] = $this->language->get('error_token');
        } else {
            if (!isset($this->session->data['token']) && strlen($_REQUEST['token']) == 0) {
                $json['error'] = $this->language->get('error_permission');
            } else {
                $data = [
                    'PC1' => [
                        'name' => PC1,
                        'image' => 'image'
                    ],
                    'PC2' => PC2,
                    'PC3' => PC3,
                    'PC4' => PC4,
                    'PC5' => PC5,
                    'PC6' => PC6,
                    'PC7' => PC7,
                    'PC8' => PC8,
                    'PC9' => PC9,
                    'PC10' => PC10,
                ];
                $json['card '] = $data;
                $json['success'] = $this->language->get('paid_cart_success');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function cartDetails()
    {
        $this->load->language('api/cart');
        $json = array();

        if(!isset($_REQUEST['token'])){
            $json['error'] = $this->language->get('error_token');
        } else {
            if (!isset($this->session->data['token']) && strlen($_REQUEST['token']) == 0) {
                $json['error'] = $this->language->get('error_permission');
            } else {
                if($_REQUEST['card_id']  == 'FC1') {
                    $json['cart_detail '] = FC1D;
                } elseif ($_REQUEST['card_id']  == 'FC2'){
                    $json['cart_detail '] = FC2D;
                } elseif ($_REQUEST['card_id']  == 'FC3'){
                    $json['cart_detail '] = FC3D;
                } elseif ($_REQUEST['card_id']  == 'FC4'){
                    $json['cart_detail '] = FC4D;
                } elseif ($_REQUEST['card_id']  == 'FC5'){
                    $json['cart_detail '] = FC5D;
                } elseif ($_REQUEST['card_id']  == 'FC6'){
                    $json['cart_detail '] = FC6D;
                } elseif ($_REQUEST['card_id']  == 'FC7'){
                    $json['cart_detail '] = FC7D;
                } elseif ($_REQUEST['card_id']  == 'FC8'){
                    $json['cart_detail '] = FC8D;
                } elseif ($_REQUEST['card_id']  == 'FC9'){
                    $json['cart_detail '] = FC9D;
                } elseif ($_REQUEST['card_id']  == 'FC10'){
                    $json['cart_detail '] = FC10D;
                } elseif ($_REQUEST['card_id']  == 'PC1'){
                    $json['cart_detail '] = PC1D;
                } elseif ($_REQUEST['card_id']  == 'PC2'){
                    $json['cart_detail '] = PC2D;
                } elseif ($_REQUEST['card_id']  == 'PC3'){
                    $json['cart_detail '] = PC3D;
                } elseif ($_REQUEST['card_id']  == 'PC4'){
                    $json['cart_detail '] = PC4D;
                } elseif ($_REQUEST['card_id']  == 'PC5'){
                    $json['cart_detail '] = PC5D;
                } elseif ($_REQUEST['card_id']  == 'PC6'){
                    $json['cart_detail '] = PC6D;
                } elseif ($_REQUEST['card_id']  == 'PC7'){
                    $json['cart_detail '] = PC7D;
                } elseif ($_REQUEST['card_id']  == 'PC8'){
                    $json['cart_detail '] = PC8D;
                } elseif ($_REQUEST['card_id']  == 'PC9'){
                    $json['cart_detail '] = PC9D;
                } elseif ($_REQUEST['card_id']  == 'PC10'){
                    $json['cart_detail '] = PC10D;
                } else {
                    $json['error'] = $this->language->get('error_cart_id');
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
