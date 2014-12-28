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

class ModelModuleSearchanise extends Model {

	public function seGetProducts($p_id, $lang_code) {
		$lang_id_query = $this->db->query(
			"SELECT language_id FROM `" . DB_PREFIX . "language`"
			. " WHERE code = '" . $lang_code . "'"
		);
		
		$lang_id = $lang_id_query->row['language_id'];

		$product_query = $this->db->query(
			"SELECT p.*, pd.* FROM `" . DB_PREFIX . "product` as p "
			. " LEFT JOIN `" . DB_PREFIX . "product_description` as pd ON p.product_id = pd.product_id "
			. " WHERE p.product_id = " . $p_id . " AND pd.language_id = " . $lang_id
		);

		$product = $product_query->row;

		//
		// Remove the code below if it is not necessary to send additional product images to Searchanise when the main image is missing.
		if (empty($product['image'])) {
			$additional_product_image = $this->db->query(
				"SELECT image FROM `" . DB_PREFIX . "product_image` "
				. " WHERE product_id = " . $p_id
				. " ORDER BY sort_order ASC LIMIT 1"
			);
			if (isset($additional_product_image->row['image'])) {
				$product['image'] = $additional_product_image->row['image'];
			}
		}
		//

		$product_to_stores_query = $this->db->query(
			"SELECT store_id FROM `" . DB_PREFIX . "product_to_store` "
			. " WHERE product_id = " . $p_id
		);

		$product['stores'] = array();
		foreach ($product_to_stores_query->rows as $store_data) {
			$product['stores'][] = $store_data['store_id'];
		}

		$product_special_prices_query = $this->db->query(
			"SELECT price, customer_group_id FROM `" . DB_PREFIX . "product_special` "
			. " WHERE product_id = " . $p_id 
			. " AND ((date_start = '0000-00-00' OR date_start < NOW()) "
				. " AND (date_end = '0000-00-00' OR date_end > NOW())) "
			. " ORDER BY priority ASC, price ASC"
		);

		$product_special = array();
		foreach ($product_special_prices_query->rows as $price_data) {
			$product_special[$price_data['customer_group_id']] = $price_data['price'];
		}

		$product['special'] = $product_special;

		$product_discount_prices_query = $this->db->query(
			"SELECT * FROM `" . DB_PREFIX . "product_discount` "
			. " WHERE product_id = '" . (int)$p_id . "' "
			. " AND quantity = 1 "
			. " AND ((date_start = '0000-00-00' OR date_start < NOW()) "
				. " AND (date_end = '0000-00-00' OR date_end > NOW())) "
			. " ORDER BY quantity, priority, price"
		);
		
		$product_discount = array();
		foreach ($product_discount_prices_query->rows as $price_data) {
			if (isset($product_discount[$price_data['customer_group_id']])) {
				$gr_price = $product_discount[$price_data['customer_group_id']];
				if ($price_data['price'] >= $gr_price) {
					continue;
				}
			}
			$product_discount[$price_data['customer_group_id']] = $price_data['price'];
		}
		
		$product['discount'] = $product_discount;

		$manufacturer = $this->db->query(
			"SELECT name FROM `" . DB_PREFIX . "manufacturer` "
			. " WHERE manufacturer_id = " . $product['manufacturer_id']
		);
		
		$product['manufacturer'] = isset($manufacturer->row['name']) ? $manufacturer->row['name'] : '';

		$category_ids_query = $this->db->query(
			"SELECT category_id FROM `" . DB_PREFIX . "product_to_category` "
			. " WHERE product_id = " . $p_id
		);

		$product['categories'] = array();
		foreach ($category_ids_query->rows as $category_data) {
			$product['categories'][] = $category_data['category_id'];
		}

		$rating_query = $this->db->query(
			"SELECT AVG(rating) AS total FROM `" . DB_PREFIX . "review` as r "
			. " WHERE r.product_id = " . $p_id . " AND r.status = '1' GROUP BY r.product_id"
		);

		$product['rating'] = isset($rating_query->row['total']) ? $rating_query->row['total'] : 0;

		$product['attributes'] = array();
		$product_attributes_query = $this->db->query(
			"SELECT `attribute_id`, `text` FROM `" . DB_PREFIX . "product_attribute` "
			. " WHERE product_id = " . $p_id . " AND language_id = " . $lang_id
		);
		foreach ($product_attributes_query->rows as $product_attribute) {
			$product['attributes'][$product_attribute['attribute_id']] = $product_attribute['text'];
		}

		return $product;
	}

}

?>