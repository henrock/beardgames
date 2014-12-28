var Corel = {
	alignCheckoutBoxes: function(){
		var lh = $('.left.checkout-box');
		var rh = $('.right.checkout-box');
		if (lh.height() > rh.height()){
			rh.height(lh.height());
		} else {
			lh.height(rh.height());
		}
	}
}
