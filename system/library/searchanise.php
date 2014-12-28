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

class Searchanise {
	//
	// Configurable constants
	//
	const SE_SEARCH_TIMEOUT = 3;
	const SE_REQUEST_TIMEOUT = 10;
	const SE_PRODUCTS_PER_PASS = 50;
	const SE_USE_RELEVANCE_AS_DEFAULT_SORTING = 'Y';

	const SE_CODES_SEARCH_WEIGHT = 10;
	const SE_INPUT_SEARCH_WEIGHT = 60;

	//
	// Not configurable constants
	//
	const SE_VERSION = '1.2';
	const SE_MAX_ERROR_COUNT = 25;
	const SE_MAX_PROCESSING_TIME = 720;
	const SE_MAX_SEARCH_REQUEST_LENGTH = 8000;
	const SE_SERVICE_URL = 'http://www.searchanise.com';
	const VERSION = '1.0.2';
	
	private static $db = null;
	private static $stores = array();
	private static $engines = array();
	private static $languages = array();
	private static $contact_email = "";
	private static $notifications = array();

	public static function seGetSearchaniseStatus($object) {
		$se_settings = $object->config->get('searchanise_module');

		return isset($se_settings[0]['status']) ? $se_settings[0]['status'] : 0;
	}

	public static function seCheckSearchaniseStatus($object) {
		return self::seGetSearchaniseStatus($object) == 1;
	}

	public static function seSpecifySettings($db_connector) {
		self::$db = $db_connector;
	}

	public static function seGetNotifications() {
		return self::$notifications;
	}

	public static function seIsRegistered() {
		$query = self::$db->query(
			"SELECT count(*) as total FROM `" . DB_PREFIX . "se_settings` "
			. " WHERE name = 'parent_private_key'"
		);

		return (bool)$query->row['total'];
	}

	public static function seGetParentPrivateKey($store_id) {
		$keys = self::$db->query(
			"SELECT value FROM `" . DB_PREFIX . "se_settings` "
			. " WHERE `name` = 'parent_private_key' AND `store_id` = '" . $store_id . "'"
		);

		return isset($keys->row['value']) ? $keys->row['value'] : null;
	}

	public static function seGetSimpleSetting($name, $store_id, $lang_code) {
		return self::seGetSetting($name, $store_id, $lang_code);
	}

	public static function seGetSetting($name, $store_id, $lang_code) {
		$settings = self::seGetAllSettings();
		return isset($settings[$store_id][$lang_code][$name]) ? $settings[$store_id][$lang_code][$name] : null;
	}

	public static function seGetAllSettings() {
		$settings = array();

		$_settings = self::$db->query("SELECT * FROM `" . DB_PREFIX . "se_settings`");

		$all_settings = $_settings->rows;
		foreach ($all_settings as $s) {
			$settings[$s['store_id']][$s['lang_code']][$s['name']] = $s['value'];
		}

		return $settings;
	}

	public static function seGetEnginesData($store_id = null, $lang_code = null) {
		$engines_data = array();
		
		foreach (self::getStores() as $store_data) {
			$s_id = $store_data['store_id'];
			if ($store_id !== null && $store_id != $s_id) {
				continue;
			}
			foreach (self::getLanguages() as $language_data) {
				$l_code = $language_data['code'];

				if ($lang_code !== null && $lang_code != $l_code) {
					continue;
				}

				$engines_data[] = array(
					'lang_code'             => $l_code,
					'store_id'              => $s_id,
					'language_name'         => $language_data['name'],
					'url'                   => $store_data['url'] . "?lang=" . $l_code,
					'api_key'               => self::seGetApiKey($s_id, $l_code),
					'private_key'           => self::seGetPrivateKey($s_id, $l_code),
					'import_status'         => self::seGetImportStatus($s_id, $l_code),
					'parent_private_key'    => self::seGetParentPrivateKey($s_id),
				);
			}
		}
		
		return $engines_data;
	}

	public static function seGetApiKey($store_id, $lang_code) {
		return self::seGetSetting('api_key', $store_id, $lang_code);
	}

	public static function seGetPrivateKey($store_id, $lang_code) {
		return self::seGetSetting('private_key', $store_id, $lang_code);
	}

	public static function seGetImportStatus($store_id, $lang_code) {
		return self::seGetSetting('import_status', $store_id, $lang_code);
	}

	public static function seSignup($_store_id = null, $_lang_code = null) {
		@ignore_user_abort(1);
		@set_time_limit(3600);

		$connected = false;
		$is_showed = false;

		if (($_store_id !== null || !empty($_lang_code)) && !self::seIsRegistered()) {
			return false;
		}

		$engines_data = self::seGetEnginesData($_store_id, $_lang_code);

		foreach ($engines_data as $engine_data) {
			$lang_code = $engine_data['lang_code'];
			$store_id = $engine_data['store_id'];
			$private_key = $engine_data['private_key'];
			$parent_private_key = self::seGetParentPrivateKey($store_id);

			if (!empty($private_key)) {
				continue;
			}

			$res = Http::request('POST', self::SE_SERVICE_URL . '/api/signup', array(
				'url'  => $engine_data['url'],
				'email'   => self::getContactEmail(),
				'version' => self::SE_VERSION,
				'language' => $lang_code,
				'parent_private_key' => $parent_private_key,
				'platform' => 'opencart',
			));

			if (!empty($res)) {
				$res = self::seParseResponse($res, true);

				if (is_object($res)) {
					$api_key = (string)$res->api;
					$private_key = (string)$res->private;

					if (empty($api_key) || empty($private_key)) {
						return false;
					}

					if (empty($parent_private_key)) {
						self::seSetSetting('parent_private_key', $store_id, $lang_code, $private_key);
					}

					self::seSetSetting('api_key', $store_id, $lang_code, $api_key);
					self::seSetSetting('private_key', $store_id, $lang_code, $private_key);

					$connected = true;
				} else {
					self::setNotification('W', 'text_se_connect_error');
					return false;
				}
			}
		}

		if ($connected == true) {
			self::setNotification('N', 'text_se_just_connected');
		}

		return $connected;
	}

	public static function seSendRequest($url_part, $private_key, $data, $store_id, $lang_code) {
		if (empty($private_key)) {
			return;
		}

		$params = array('private_key' => $private_key) + $data;
		$result = Http::request('POST', self::SE_SERVICE_URL . $url_part, $params, array('timeout' => self::SE_REQUEST_TIMEOUT));
		$res = self::seParseResponse($result);

		self::seSetSetting('last_request', $store_id, $lang_code, time());
		return $res;
	}

	public static function seDeleteKeys($store_id = NULL, $lang_code = NULL) {
		$engines_data = self::seGetEnginesData($store_id, $lang_code, true);

		foreach ($engines_data as $engine_data) {
			$s_id = $engine_data['store_id'];
			$l_code = $engine_data['lang_code'];

			self::seSendAddonStatusRequest('deleted', $s_id, $l_code);

			self::$db->query(
				"DELETE FROM `" . DB_PREFIX . "se_settings` "
				. " WHERE store_id = " . $s_id . " AND lang_code = '" . $l_code . "' "
				. " AND name IN ('api_key', 'private_key', 'import_status', 'parent_private_key', 'use_navigation')"
			);
			self::$db->query(
				"DELETE FROM `" . DB_PREFIX . "se_queue` "
				. " WHERE store_id = " . $s_id . " AND lang_code = '" . $l_code . "' "
			);
		}
	}

	public static function seDeleteEngineSettings($store_id, $lang_code) {
		self::$db->query(
			"DELETE FROM `" . DB_PREFIX . "se_settings` "
			. " WHERE store_id = " . $store_id . " "
			. " AND lang_code = '" . $lang_code . "' "
			. " AND name IN ('api_key', 'private_key', 'import_status', 'parent_private_key')"
		);
	}

	public static function seParseResponse($res, $show_notification = false) {
		$xml = simplexml_load_string($res, null, LIBXML_NOERROR);

		if (empty($res) || $xml === false) {
			return false;
		}
		
		if (!empty($xml->error)) {
			foreach ($xml->error as $e) {
				if ($show_notification == true) {
					self::setNotification('W', 'Searchanise: ' . (string)$e);
				}
			}

			return false;

		} elseif (strtolower($xml->getName()) == 'ok') {
			return true;

		} else {
			return $xml;
		}
	}

	public static function seSetSetting($name, $store_id, $lang_code, $value) {
		if (empty($name) || empty($lang_code)) {
			return;
		}

		self::$db->query(
			"REPLACE INTO `" . DB_PREFIX . "se_settings` "
			. " SET `name` = '" . $name . "', "
			. " `store_id` = '" . $store_id . "', "
			. " `lang_code` = '" . $lang_code . "', "
			. " `value` = '" . $value . "'"
		);

	}

	public static function seQueueImport($store_id = null, $lang_code = null, $show_notification = true) {
		if (self::seIsRegistered() == false) {
			return;
		}

		self::seAddAction('prepare_full_import', null, $store_id, $lang_code);

		$engines_data = self::seGetEnginesData($store_id, $lang_code);
		foreach ($engines_data as $engine_data) {
			self::seSetImportStatus('queued', $engine_data['store_id'], $engine_data['lang_code']);
		}

		if ($show_notification) {
			self::setNotification('N', 'text_se_import_status_queued');
		}
	}

	public static function seAddAction($action, $data = null, $store_id = null, $lang_code = null) {
		if (self::seIsRegistered() == false) {
			return;
		}

		$data = array(serialize((array)$data));

		if ($action == 'prepare_full_import') {
			if ($store_id === null && empty($lang_code)) {
				self::$db->query("TRUNCATE `" . DB_PREFIX . "se_queue`");
			} elseif ($store_id !== null) {
				self::seDeleteQueue(array('store_id' => $store_id));
			}
		}

		$engines_data = self::seGetEnginesData($store_id, $lang_code);
		foreach ($data as $d) {
			foreach ($engines_data as $engine_data) {

				if ($action != 'phrase') {
					$_params = array(
						'status'    => 'pending',
						'action'    => $action,
						'data'      => $d,
						'store_id'  => $engine_data['store_id'],
						'lang_code' => $engine_data['lang_code']
					);
					self::seDeleteQueue($_params);
				}

				$_queue = array(
					'action'     => $action,
					'data'       => $d,
					'store_id'   => $engine_data['store_id'],
					'lang_code'  => $engine_data['lang_code'],
				);
				self::seInsertQueue($_queue);
			}
		}
	}

	public static function seInsertQueue($params) {
		$set_data = "";
		foreach ($params as $field_name => $field_value) {
			if ($set_data) {
				$set_data .= ", ";
			}
			$set_data .= " `" . $field_name . "` = '" . $field_value . "'";
		}

		self::$db->query("INSERT INTO `" . DB_PREFIX . "se_queue` SET " . $set_data);
	}

	public static function seSetImportStatus($status, $store_id, $lang_code) {
		self::seSetSetting('import_status', $store_id, $lang_code, $status);
	}

	public static function seGetQueueByStoreId($store_id = null) {
		$condition = "";
		if ($store_id !== null) {
			$condition = " AND store_id = " . $store_id;
		}
		$queue = self::$db->query(
			"SELECT * FROM `" . DB_PREFIX . "se_queue` " 
			. " WHERE 1 " . $condition
			. " ORDER BY queue_id ASC LIMIT 1"
		);

		return $queue->row;
	}
	
	public static function seGetTotalQueueByStoreId($store_id = null) {
		$condition = "";
		if ($store_id !== null) {
			$condition = " AND store_id = " . $store_id;
		}
		$queue = self::$db->query(
			"SELECT COUNT(*) FROM `" . DB_PREFIX . "se_queue` " 
			. " WHERE 1 " . $condition
		);

		return !empty($queue->row) ? reset($queue->row) : 0;
	}

	public static function seUpdateQueue($queue_id, $params) {
		$update_data = "";
		foreach ($params as $field_name => $field_value) {
			if ($update_data) {
				$update_data .= ", ";
			}
			$update_data .= " `" . $field_name . "` = '" . $field_value . "'";
		}

		self::$db->query(
			"UPDATE `" . DB_PREFIX . "se_queue` SET " . $update_data
			. " WHERE queue_id = " . $queue_id
		);
	}

	public static function seDeleteQueue($params = array(), $exclude_params = array()) {
		$condition = $exclude_condition = "";
		foreach ($params as $field_name => $field_value) {
			$condition .= " AND `" . $field_name . "` = '" . $field_value . "'";
		}
		foreach ($exclude_params as $field_value => $field_value) {
			$condition .= " AND `" . $field_name . "` != '" . $field_value . "'";
		}

		self::$db->query(
			"DELETE FROM `" . DB_PREFIX . "se_queue` WHERE 1 " . $condition . $exclude_condition
		);
	}

	public static function seGetMinMaxProductIds() {
		$query = self::$db->query(
			"SELECT MIN(`product_id`) as min, MAX(`product_id`) as max "
			. " FROM `" . DB_PREFIX . "product`"
		);

		return $query->row;
	}

	public static function seGetProductIds($start, $end, $limit, $store_id = 0) {
		$prod_ids = self::$db->query(
			"SELECT p.product_id FROM `" . DB_PREFIX . "product` as p" 
			. " LEFT JOIN `" . DB_PREFIX . "product_to_store` as p2s ON p.product_id = p2s.product_id"
			. " WHERE p.product_id >= " . $start 
			. " AND p.product_id <= " . $end 
			. " AND p2s.store_id = " . $store_id
			. " LIMIT " . $limit
		);

		$result = array();
		foreach ($prod_ids->rows as $data) {
			$result[] = $data['product_id'];
		}
		
		return $result;
	}

	public static function seMultiInsertData($data) {
		self::$db->query(
			"INSERT INTO `" . DB_PREFIX . "se_queue` "
			. " (`data`, `action`, `store_id`, `lang_code`) "
			. " VALUES " . join(',', $data)
		);
	}

	public static function seCheckImportIsDone($store_id = NULL, $lang_code = NULL) {
		$skip_time_check = false;
		$engines_data = self::seGetEnginesData($store_id, $lang_code);

		foreach ($engines_data as $engine_data) {
			$s_id = $engine_data['store_id'];
			$l_code = $engine_data['lang_code'];

			if (
				$engine_data['import_status'] == 'sent' 
				&& ((time() - self::seGetSimpleSetting('last_request', $s_id, $l_code)) > 10 
					|| $skip_time_check == true)
			) {
				$xml = self::seSendRequest('/api/state/get', self::seGetPrivateKey($s_id, $l_code), array('status' => '', 'full_import' => ''), $s_id, $l_code);

				$variables = self::seParseStateResponse($xml);

				if (!empty($variables) && isset($variables['status'])) {
					if ($variables['status'] == 'normal' && $variables['full_import'] == 'done') {
						$skip_time_check = true;
						self::seSetImportStatus('done', $s_id, $l_code);
					} elseif ($variables['status'] == 'disabled') {
						self::seSetImportStatus('none', $c_id, $l_code);
					}
				}
			}
		}
	}

	private static function seParseStateResponse($xml, $element = false) {
		$res = array();
		if (!is_object($xml)) {
			return false;
		}

		if ($xml->getName() == 'variable') {
			foreach($xml as $v) {
				$res[$v->getName()] = (string)$v;
			}
		} else {
			return false;
		}

		if (!empty($element)) {
			$res = isset($res[$element])? $res[$element] : false;
		}

		return $res;
	}

	public static function seGetSearchParams($params, $store_id, $lang_code) {
		if (
			empty($params['filter_name'])
			|| self::seGetImportStatus($store_id, $lang_code) != 'done'
		) {
			list($params['sort'], $params['sort_order']) = self::seGetSortParams(array('sort' => ''));
			return $params;
		}

		list($restrict_by, $union) = self::sePrepareSearchParams($params, $store_id);

		list($sort_by, $sort_order) = self::seGetSortParams($params);

		$request_params = array(
			'sortBy'        => $sort_by,
			'sortOrder'     => $sort_order,

			'restrictBy'    => $restrict_by,
			'union'         => $union,
			
			'items'         => 'true',
			'facets'        => 'false',

			'maxResults'    => $params['limit'],
			'startIndex'    => $params['start']
		);

		if (!empty($params['filter_name']) && strlen($params['filter_name']) > 0) {
			$request_params['q'] = $params['filter_name'];
			$request_params['suggestions'] = 'true';
			$request_params['query_correction'] = 'false';
			$request_params['suggestionsMaxResults'] = 1;
		} else {
			$request_params['q'] = '';
		}

		$result = self::seSendSearchRequest($request_params, $store_id, $lang_code);

		if ($result == false) {
			list($params['sort'], $params['sort_order']) = self::seGetSortParams(array('sort' => ''));
			return $params;
		}
		
		$_params = array(
			'sort'          => $params['sort'],
			'order'         => $params['order'],
			'start'         => 0,
			'limit'         => $result['currentItemCount'],
			'total'         => $result['totalItems'],
			'suggestions'   => $result['suggestions']
		);

		if (!empty($result['items'])) {
			foreach($result['items'] as $product) {
				$_params['pid'][] = $product['product_id'];
			}
		} else {
			$_params['pid'] = 0;
		}

		return $_params;
	}

	private static function seGetSortParams($params) {
		$sort_matching = array(
			'p.sort_order'  => 'sort_order',
			'relevance'     => 'relevance',
			'pd.name'       => 'title',
			'p.price'       => 'price',
			'rating'        => 'rating',
			'p.model'       => 'model'
		);
		$selected_sort = $params['sort'];

		$sort_by = !empty($sort_matching[$selected_sort]) ? $sort_matching[$selected_sort] : 'sort_order';
		$sort_order = (isset($params['order']) && $params['order'] == 'ASC') ? 'asc' : 'desc';

		return array($sort_by, $sort_order);
	}

	private static function sePrepareSearchParams($params, $store_id) {
		$restrict_by = $union = array();

		$restrict_by['status'] = 1;
		$restrict_by['store_id'] = $store_id;

		if ($params['filter_category_id']) {
			$c_ids = array($params['filter_category_id']);

			if ($params['filter_sub_category']) {
				$c_ids = array_merge($c_ids, self::getCategoryChilds($params['filter_category_id'], $store_id));
			}
			$restrict_by['category_id'] = join('|', $c_ids);
		}
		$restrict_by['date_available'] = "," . time();

		if (isset($params['auth']['customer_group_id'])) {
			$union['price']['min'] = 'price_' . $params['auth']['customer_group_id'];
		}

		return array($restrict_by, $union);
	}

	public static function seSendAddonStatusRequest($status = 'enabled', $store_id = NULL, $lang_code = NULL) {
		$engines_data =  self::seGetEnginesData($store_id, $lang_code);

		foreach ($engines_data as $engine_data) {
			$private_key = self::seGetPrivateKey($engine_data['store_id'], $engine_data['lang_code']);
			self::seSendRequest('/api/state/update', $private_key, array('addon_status' => $status), $engine_data['store_id'], $engine_data['lang_code']);
		}
	}

	private static function seSendSearchRequest($params, $store_id, $lang_code) {
		$api_key = self::seGetApiKey($store_id, $lang_code);

		if (empty($api_key)) {
			return;
		}

		$params['output'] = 'json';
		$query = http_build_query($params);

		if (strlen($query) > self::SE_MAX_SEARCH_REQUEST_LENGTH) {
			$received = Http::request('POST', self::SE_SERVICE_URL . '/search?api_key=' . $api_key, $params, array('timeout' => SE_SEARCH_TIMEOUT));
		} else {
			$params['api_key'] = $api_key;
			$received = Http::request('GET', self::SE_SERVICE_URL . '/search', $params, array('timeout' => self::SE_REQUEST_TIMEOUT));

		}

		if (empty($received)) {
			return false;
		}

		$result = json_decode(trim($received), true);

		if (isset($result['error'])) {
			if ($result['error'] == 'NEED_RESYNC_YOUR_CATALOG') {
				self::seQueueImport($company_id, $lang_code, false);

				return false;

			} elseif ($result['error'] == 'NAVIGATION_DISABLED') {
				self::seSetSetting('use_navigation', $store_id, $lang_code, 'N');
			}
		}

		if (empty($result) || !is_array($result) || !isset($result['totalItems'])) {
			return false;
		}

		return $result;
	}

	/*******************/

	private static function getStores() {
		if (empty(self::$stores)) {
			$_stores = array();
			$main_store_name = "";
			$main_store_url = defined('HTTP_CATALOG') ? HTTP_CATALOG : HTTP_SERVER;

			$main_store_configs = self::$db->query(
				"SELECT s.value, s.key "
				. " FROM `" . DB_PREFIX . "setting` as s "
				. " WHERE store_id = '0' AND s.key = 'config_name'"
			);
			
			foreach ($main_store_configs->rows as $config) {
				if ($config['key'] == 'config_name') {
					$main_store_name = $config['value'];
				}
			}

			if ($main_store_name && $main_store_url) {
				$_stores[0] = array(
					'store_id' => 0,
					'url' => $main_store_url,
					'name' => $main_store_name,
				);
			}

			$seconadary_stores = self::$db->query(
				"SELECT store_id, url, name FROM `" . DB_PREFIX . "store`"
			);

			foreach ($seconadary_stores->rows as $_store) {
				$_stores[$_store['store_id']] = array(
					'store_id' => $_store['store_id'],
					'url' => $_store['url'],
					'name' => $_store['name'],
				);                
			}

			self::$stores = $_stores;
		}

		return self::$stores;
	}

	private static function getLanguages() {
		if (empty(self::$languages)) {
			$_languages = self::$db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE status = 1");
			
			self::$languages = $_languages->rows;
		}
		
		return self::$languages;
	}

	private static function getContactEmail() {
		if (empty(self::$contact_email)) {
			$query = self::$db->query(
				"SELECT s.value as email FROM `" . DB_PREFIX . "setting` as s "
				. " WHERE s.store_id = 0 AND s.key = 'config_email'"
			);

			self::$contact_email = $query->row['email'];
		}

		return self::$contact_email;
	}

	private static function setNotification($type, $text = "") {
		self::$notifications[] = array(
			'type' => $type, 
			'text' => $text
		);
	}

	private static function getCategoryChilds($parent_id = 0, $store_id) {
		$category_ids = array();

		$query = self::$db->query(
			"SELECT * FROM `" . DB_PREFIX . "category` as c "
			. " LEFT JOIN `" . DB_PREFIX . "category_to_store` as c2s ON (c.category_id = c2s.category_id) "
			. " WHERE c.parent_id = " . (int)$parent_id . " AND c2s.store_id = " . (int)$store_id 
			. " AND c.status = '1'"
		);

		foreach ($query->rows as $category_data) {
			$category_ids[] = $category_data['category_id'];
			$category_ids = array_merge(
				$category_ids, 
				self::getCategoryChilds($category_data['category_id'], $store_id)
			);
		}

		return $category_ids;
	}

	public static function getStoreUrl($store_id) {
		if (!$store_id) {
			return HTTP_SERVER;
		} else {
			return self::getStoreConfig($store_id, 'config_url');
		}
	}

	public static function getStoreSecureUrl($store_id) {
		if (!$store_id) {
			return HTTPS_SERVER;
		} else {
			return self::getStoreConfig($store_id, 'config_ssl');
		}
	}	

	public static function isStoreUsedSsl($store_id) {
		return (bool)self::getStoreConfig($store_id, 'config_secure');
	}

	public static function isStoreUsedSeo() {
		return (bool)self::getStoreConfig(0, 'config_seo_url');
	}

	private static function getStoreConfig($store_id, $config_name) {
		$query = self::$db->query(
			"SELECT value FROM `" . DB_PREFIX . "setting` as s "
			. " WHERE s.key = '" . $config_name . "' AND s.store_id = " . (int)$store_id
		);

		return $query->row['value'];
	}
}

?>