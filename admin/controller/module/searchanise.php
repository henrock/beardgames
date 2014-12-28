<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

/* [Searchanise] */

class ControllerModuleSearchanise extends Controller {

	/*
	 * Install / Uninstall
	 */

	public function install() {
		$this->load->model('module/searchanise');
		$this->model_module_searchanise->searchaniseCreateModuleTables();

		$this->load->model('setting/setting');

		$settings = $this->createSettings(array('se_status' => 1));

		$this->model_setting_setting->editSetting('searchanise', $settings);
	}

	public function uninstall() {
		$this->load->model('module/searchanise');
		$this->model_module_searchanise->searchaniseRemoveTables();
	}


	/*
	 * Modes
	 */

	public function index() {
		$this->load->model('setting/store');
		$this->load->model('setting/setting');

		$this->language->load('module/searchanise');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$settings = $this->createSettings($this->request->post);
			$this->model_setting_setting->editSetting('searchanise', $settings);

			if (isset($this->request->post['se_status'])) {
				$new_status = $this->request->post['se_status'];
				
				if (!$new_status) {
					if ($this->config->get('searchanise_module') != $new_status) {
						Searchanise::seSendAddonStatusRequest('disabled');
					}
				} elseif ($new_status) {
					$se_re_indexation_notification = str_replace(
						'[href]', 
						$this->url->link('module/searchanise', 'token=' . $this->session->data['token'], 'SSL'), 
						$this->language->get('text_se_check_re_indexation')
					);
					$this->setNotification('N', $se_re_indexation_notification);

					if ($this->config->get('searchanise_module') != $new_status) {
						Searchanise::seSendAddonStatusRequest('enabled');
					}
				}
			}
					
			$this->setNotification('N', $this->language->get('text_success'));
						
			$this->redirect($this->url->link('module/searchanise', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->formationDocument();

		$this->formationAddonData();

		$this->formationLanguageVariables();

		$this->formationBreadcrumbs();

		$this->formationSearchaniseParams();

		$this->formationNotifications();

		$this->template = 'module/searchanise.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);

		$this->response->setOutput($this->render());
	}

	public function signup() {
		$this->load->model('setting/store');
		$this->load->model('localisation/language');

		$this->language->load('module/searchanise');

		$store_id = null;
		if (isset($this->request->get['store_id'])) {
			$store_id = $this->request->get['store_id'];
		}

		if (Searchanise::seSignup($store_id)) {
			Searchanise::seQueueImport($store_id);
		}

		$se_notifications = Searchanise::seGetNotifications();
		if (!empty($se_notifications)) {
			foreach ($se_notifications as $se_notification) {
				$notification_lv = $se_notification['text'];
				$this->setNotification($se_notification['type'], $this->language->get($notification_lv));
			}
		}

		$this->redirect($this->url->link('module/searchanise', 'token=' . $this->session->data['token'], 'SSL'));
	}

	public function export() {
		$this->load->model('setting/store');
		$this->load->model('localisation/language');

		$this->language->load('module/searchanise');

		Searchanise::seSignup($this->getStoreId());
		Searchanise::seQueueImport($this->getStoreId());

		$se_notifications = Searchanise::seGetNotifications();
		if (!empty($se_notifications)) {
			foreach ($se_notifications as $se_notification) {
				$notification_lv = $se_notification['text'];
				$this->setNotification($se_notification['type'], $this->language->get($notification_lv));
			}
		}

		$this->redirect($this->url->link('module/searchanise', 'token=' . $this->session->data['token'], 'SSL'));
	}

	/*
	 * Interface data
	 */

	private function formationDocument() {
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addStyle('view/stylesheet/searchanise_style.css');
	}

	private function formationAddonData() {
		$data = array(
			'cancel_button_link' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'action_link'        => $this->url->link('module/searchanise', 'token=' . $this->session->data['token'], 'SSL'),
			'se_status'          => Searchanise::seGetSearchaniseStatus($this),
			'token'              => $this->session->data['token']
		);

		$stores_count = $this->model_setting_store->getTotalStores();

		if ($stores_count > 0 && Searchanise::seIsRegistered() && $data['se_status']) {
			$data['stores'] = $this->getStores();
			$data['selected_store_id'] = $this->getStoreId();

		} else {
			$data['stores'] = '';
		}

		$this->data = array_merge($this->data, $data);
	}

	private function formationLanguageVariables() {
		$data = array(
			'text_heading_title'  => $this->language->get('heading_title'),
			'text_button_save'    => $this->language->get('button_save'),
			'text_button_cancel'  => $this->language->get('button_cancel'),
			'text_active'         => $this->language->get('text_active'),
			'text_disabled'       => $this->language->get('text_disabled'),
			'text_status'         => $this->language->get('text_status'),
			'text_stores'         => $this->language->get('text_stores'),
		);

		$this->data = array_merge($this->data, $data);
	}

	private function formationNotifications() {
		$notifications = array();

		if (isset($this->session->data['success'])) {
			$this->setNotification('N', $this->session->data['success']);
		
			unset($this->session->data['success']);
		}

		if (isset($this->session->data['warning'])) {
			$this->setNotification('W', $this->session->data['warning']);
		
			unset($this->session->data['warning']);
		}        

		if (isset($this->session->data['notifications'])) {
			$notifications = $this->session->data['notifications'];

			unset($this->session->data['notifications']);
		}

		$this->data = array_merge($this->data, array('notifications' => $notifications));
	}

	private function formationBreadcrumbs() {
		$breadcrumbs = array();

		$breadcrumbs[] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$breadcrumbs[] = array(
			'text'      => $this->language->get('text_modules'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);
		
		$breadcrumbs[] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/searchanise', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data = array_merge($this->data, array('breadcrumbs' => $breadcrumbs));
	}

	private function formationSearchaniseParams() {
		$host = Searchanise::SE_SERVICE_URL;

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$host = str_replace('http://', 'https://', $host);
		}

		$params = array('host' => $host);

		if ($this->data['se_status']) {
			if (!Searchanise::seIsRegistered()) {
				$params['is_registered'] = false;
				$_connect_link = $this->url->link('module/searchanise/signup', 'token=' . $this->session->data['token'], 'SSL');
			} else {
				$params['is_registered'] = true;
				$store_id = $this->getStoreId();

				$_connect_link = $this->url->link('module/searchanise/signup', 'token=' . $this->session->data['token'] . '&store_id=' . $store_id, 'SSL');
				$_re_sync_link  = $this->url->link('module/searchanise/export', 'token=' . $this->session->data['token'] . '&store_id=' . $store_id, 'SSL');

				$params['private_key'] = Searchanise::seGetParentPrivateKey($store_id);
				$params['re_sync_link'] = $_re_sync_link;
				$params['last_request'] = date(
					'Y-m-d H:i:s', 
					$this->parseDate(Searchanise::seGetSimpleSetting('last_request', $this->getStoreId(), $this->config->get('config_language')))
				);
				$params['last_resync'] = date(
					'Y-m-d H:i:s', 
					$this->parseDate(Searchanise::seGetSimpleSetting('last_resync', $this->getStoreId(), $this->config->get('config_language')))
				);

				$params['engines_data'] = Searchanise::seGetEnginesData($store_id);
			}

			$params['connect_link']  = $_connect_link;
		}

		$this->data = array_merge($this->data, array('searchaniseParams' => $params));
	}

	/*
	 * Common
	 */
	private function getStores() {
		$default_store = array(
			0 => array(
				'store_id' => 0,
				'url'      => HTTP_CATALOG,
				'name'     => $this->config->get('config_name') . $this->language->get('text_default')
			)
		);


		$all_stores = array_merge($default_store, $this->model_setting_store->getStores());

		return $all_stores;
	}

	private function getStoreUrl($store_id) {
		$stores = $this->getStores();
		foreach ($stores as $store) {
			if ($store['store_id'] == $store_id) {
				$url = $store['url'];
				if (isset($this->request->server['HTTPS']) 
					&& (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))
				) {
					$url = str_replace(HTTP_CATALOG, 'https://', $url);
				}
				return $url;
			}
		}

		return '';
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/searchanise')) {
			$this->setNotification('W', $this->language->get('text_error_permission'));
			return false;
		}
		
		return true;
	}

	private function setNotification($type, $text = "") {
		$this->session->data['notifications'][] = array(
			'type' => $type, 
			'text' => $text
		);
	}

	private function getStoreId() {
		if (isset($this->session->data['selected_store_id'])) {
			$s_id = $this->session->data['selected_store_id'];
		}
		if (isset($this->request->get['store_id'])) {
			$s_id = $this->request->get['store_id'];
		}
		
		$this->session->data['selected_store_id'] = 0;
		if (isset($s_id)) {
			$isset_store = $this->model_setting_store->getStore($s_id);
			if ($isset_store) {
				$this->session->data['selected_store_id'] = $s_id;
			}
		}

		return $this->session->data['selected_store_id'];
	}

	function parseDate($timestamp) {
		if (!empty($timestamp)) {
			if (is_numeric($timestamp)) {
				return $timestamp;
			}

			$ts = explode('/', $timestamp);
			$ts = array_map('intval', $ts);
			if (empty($ts[2])) {
				$ts[2] = date('Y');
			}
			if (count($ts) == 3) {
				list($h, $m, $s) = array(0, 0, 0);
				$timestamp = gmmktime($h, $m, $s, $ts[0], $ts[1], $ts[2]);
			} else {
				$timestamp = time();
			}
		}

		return !empty($timestamp) ? $timestamp : time();
	}

	private function createSettings($params) {
		$settings = array('searchanise_module' => array());
		$status = 0;

		if (isset($params['se_status'])) {
			$status = $params['se_status'];
		}

		// If set 'status', 'position', 'layout_id' and 'sort_order' that code searchanise will add to customer area.
		// need for each layout_id when will show widget
		$def_set = array(
			'status'     => $status,
			'position'   => 'content_top',
			'layout_id'  => 4, // Default
			'sort_order' => -1,
		);

		$this->load->model('design/layout');
		$layouts = $this->model_design_layout->getLayouts();

		if ($layouts) {
			foreach ($layouts as $layout) {
				$def_set['layout_id'] = $layout['layout_id'];
				$settings['searchanise_module'][] = $def_set;
			}
		} else {
			$settings['searchanise_module'][] = $def_set;
		}

		return $settings;
	}
}

?>