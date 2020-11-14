<?php echo $header; ?><?php echo $column_left; ?>
<div class="content cart_page">
    <h1><?php echo $heading_title; ?></h1>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>

    <form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="cart">
      <input type="hidden" name="remove" value="" id="remove" />
      <div class="cart_products">
       
        <div class="cart_product_item">
            <?php $class = 'odd'; ?>
            <?php 
			$i_custom = 0;
			foreach ($products as $key => $product) {
			$i_custom++;
			?>
            <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
            <div class="<?php echo $class; ?> row">
                 <a href="<?php echo str_replace('&', '&amp;', $product['href']); ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>
                 <a class="product_name" href="<?php echo str_replace('&', '&amp;', $product['href']); ?>"><?php echo $product['name']; ?></a>
                 
                 <a class="qtt_change button_minus" onclick="$('#qtt<?php echo $key ?>').val(<?php echo ($product['quantity']-1) ?>); $('#cart').submit()">-</a>
                 <input type="text" readonly="readonly" id="qtt<?php echo $key ?>" name="quantity[<?php echo $product['key']; ?>]" value="<?php echo $product['quantity']; ?>" size="2" />
                 <a class="qtt_change button_plus" onclick="$('#qtt<?php echo $key ?>').val(<?php echo ($product['quantity']+1) ?>); $('#cart').submit()">+</a>
 
             <span class="price"><?php echo $product['price']; ?></span>
              <a onclick="sendCommerce('<?php echo $product['product_id']; ?>');$('#remove').val('<?php echo $product['key']; ?>');" class="button_update delete"></a>
               <!-- $('#cart').submit() -->
            </div>
            <?php } ?>
             
        </div>
		<script>
		function sendCommerce(product_id) {
		product_id = $(this).attr('data-product_id');
		list = $(this).find('.remarketing-id').attr('data-list');

		if(product_id) { 
		  $.ajax({ 
              type: 'post',
              url:  '/index.php?route=product/product/shortProduct',
			  data: 'product_id='+product_id,
              dataType: 'json',
              success: function(json) {
			  if (json['success']){
			  special = parseFloat(json['product']['special']).toFixed(2); 
			  price = parseFloat(json['product']['price']).toFixed(2);
			  if(!isNaN(special) && special > 0) price = special;
			var  heading = $('h1').text();
			window.dataLayer = window.dataLayer || [];
			dataLayer.push({
						'ecommerce': { 
						'currencyCode': 'UAH',
						'remove': {                                
							'products': [{                      
							'name': json['product']['name'],
							'id': json['product']['model'],
						//	'brand': json['product']['manufacturer'],
							'price': price,
						//	'category': heading
							}]
    }},
		'event': 'gtm-ee-event',
		'gtm-ee-event-category': 'Enhanced Ecommerce',
		'gtm-ee-event-action': 'Removing a Product from a Shopping Cart',
		'gtm-ee-event-non-interaction': 'False'
	});		 
}}})}};
		</script>
           <div class="cart_totals">
                <?php  foreach ($totals as $total) { ?>
                    <p><div style="text-align:left !important;"><?php echo $i_custom; ?> товаров </div><?php echo $total['title']; ?> &nbsp;<span> <?php echo $total['text']; ?></span></p>
                <?php } ?>
        </div>
      </div>
      <div class="buttons">
          <table>
          <tr>
            <td align="left"><a onclick="location = '<?php echo str_replace('&amp;', '&', $continue); ?>'" class="button_grey"><span><?php echo $button_shopping; ?></span></a></td>
            <td align="right"><a onclick="location = '<?php echo str_replace('&amp;', '&', $checkout); ?>'" class="button button_checkout "><span><?php echo $button_checkout; ?><b class="cart_bg"></b></span></a></td>
          </tr>
        </table>
      </div>
    </form>
</div>
<?php echo $footer; ?>
