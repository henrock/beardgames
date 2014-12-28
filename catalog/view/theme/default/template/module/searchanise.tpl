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
?>

<?php if ($searchanise['api_key'] && $searchanise['import_status'] == "done") { ?>
<script type="text/javascript">
    Searchanise = {};
    Searchanise.host = "<?php echo $searchanise['host'] ?>";
    Searchanise.api_key = "<?php echo $searchanise['api_key']; ?>";

    Searchanise.SearchInput = '#search input[name=search]';

    Searchanise.AutoCmpParams = {};
    Searchanise.AutoCmpParams.restrictBy = {};
    Searchanise.AutoCmpParams.restrictBy.stores = "<?php echo $searchanise['store_id']; ?>";
    Searchanise.AutoCmpParams.restrictBy.date_available = ",<?php echo $searchanise['date']; ?>";
    Searchanise.AutoCmpParams.restrictBy.status = "1";

    Searchanise.AutoCmpParams.union = {};
    Searchanise.AutoCmpParams.union.price = {};

    <?php if ($searchanise['min_price']) { ?>
        Searchanise.AutoCmpParams.union.price.min = "price_<?php echo $searchanise['min_price']; ?>";
    <?php } ?>

    Searchanise.options = {};
    
    Searchanise.options.onSuggestionClick = function(search) {
        location = $('base').attr('href') + 'index.php?route=product/search&search=' + encodeURIComponent(search);
    };

    <?php if ($searchanise['price_format']) { ?>
        Searchanise.options.PriceFormat = {
            'rate': "<?php echo $searchanise['price_format']['rate']; ?>",
            'symbol': "<?php echo $searchanise['price_format']['symbol']; ?>",
            'after': <?php echo $searchanise['price_format']['after']; ?>,
            'decimals': "<?php echo $searchanise['price_format']['decimals']; ?>",
            'decimals_separator': "<?php echo $searchanise['price_format']['decimals_separator']; ?>",
            'thousands_separator': "<?php echo $searchanise['price_format']['thousands_separator']; ?>",
        };
    <?php } ?>

    (function() {
        var __se = document.createElement('script');
        __se.src = "<?php echo $searchanise['host'] . '/widgets/v1.0/init.js'; ?>";
        __se.setAttribute('async', 'true');
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(__se, s);
    })();

</script>

<style type="text/css">
input[name="description"], label[for="description"] {
    display: none;
}
</style>

<?php } ?>

<style type="text/css">

<?php if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) { ?>
    .snize-price {
        display:none !important;
    }
<?php } ?>

</style>

<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery.get("<?php echo $searchanise['async_link']; ?>")
});
</script>