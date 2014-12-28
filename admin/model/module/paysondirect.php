<?php
class ModelModulePaysondirect extends Model {
  	public function createModuleTables(){
        $query = $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "payson_order` (
								  `id` int(11) NOT NULL AUTO_INCREMENT,
								  `order_id` int(15) NOT NULL,
								  `added` datetime DEFAULT NULL,
								  `updated` datetime DEFAULT NULL,
								  `valid` tinyint(1) NOT NULL,
								  `ipn_status` varchar(65) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
								  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
								  `sender_email` varchar(50) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
								  `currency_code` varchar(5) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
								  `tracking_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
								  `type` varchar(50) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
								  `purchase_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
								  `invoice_status` varchar(50) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
								  `customer` varchar(50) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
								  `shippingAddress_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
								  `shippingAddress_street_ddress` varchar(60) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
								  `shippingAddress_postal_code` varchar(20) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
								  `shippingAddress_city` varchar(60) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
								  `shippingAddress_country` varchar(60) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
								  PRIMARY KEY (`id`)
						) 
        ");	
  	} 	
}
?>