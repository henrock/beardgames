<?php  
class ControllerProductPrisjakt extends Controller {
	public function index() {
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('shipping/weight');
		header('Content-type: text/plain; charset=utf-8');

		$results = $this->model_catalog_product->getProducts();	

		echo "Produktnamn;Art.nr.;Kategori;Pris exkl.moms;Produkt-URL;Tillverkare;Frakt exkl.moms;Bild-URL;Lagerstatus\n";

		foreach ($results as $result) {
			$categories = $this->model_catalog_product->getCategories($result['product_id']);
			foreach($categories as $category) {
				if($category['category_id'] != 64) {
					$product_category = $this->model_catalog_category->getCategory($category['category_id']);
					$product_category_name = $product_category['name'];
				}
			}
			$freight_cost = $this->model_shipping_weight->getQuote(array('geo_zone_id' => 5, 'zone_id' => 0, 'country_id' => 203), $result['weight']);
			$freight_cost = array_shift($freight_cost['quote']);
			//var_dump($freight_cost);
			//var_dump($result);
			echo $result['name'].";";
			echo $result['model'].";";
			echo $product_category_name.";";
			echo $result['price'].";";
			echo $this->url->link('product/product', '&product_id=' . $result['product_id']).";";
			echo $result['manufacturer'].";";
			echo $freight_cost['cost'].";";
			echo 'http://www.beardgames.se/'.$result['image'].";";
			echo (($result['quantity'] > 0) ? 'Ja' : 'Nej')."\n";
		}
	}
}
?>