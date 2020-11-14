    </section>
        <div class="page_info">
            <div class="page_i_top">
                <div class="page_i_left">
                </div>
                <div class=" page_i_right">
                    <?php echo $footer_block_right ?>
                </div>
            </div>
        </div>
</section>
<footer class="footer">
    <div class="top_footer"> 
         <div class="top_contact">
          <?php echo $footer_contact ?>
         </div>
       
          <!-- Social links-->
          
            <div class="centre">           
                <div class="top_menu">
                    <a href="http://gra.ua/index.php?route=information/information&amp;information_id=11">Privacy policy</a>
                    <?php echo $information; ?>
                    <?php /*<a href="<?php echo str_replace('&', '&amp;', $sitemap_href); ?>">Карта сайта</a> */ ?>

<script src="https://apis.google.com/js/platform.js?onload=renderBadge" async defer></script>
 
<script>
  window.renderBadge = function() {
    var ratingBadgeContainer = document.createElement("div");
    document.body.appendChild(ratingBadgeContainer);
    window.gapi.load('ratingbadge', function() {
      window.gapi.ratingbadge.render(ratingBadgeContainer, {"merchant_id": 121414289});
    });
  }
</script>
                </div>
                 <div class="copy">
             <div class="support">
                  <div class="social">
                   </div>
             </div>
			Архив:<br/>
			  <a class="" href="/catalog/sbornye-modeli">Сборные модели</a>
			  &nbsp;&nbsp;&nbsp;
			  <a class="" href="/catalog/galantereya">Галантерея</a>
             <p>Copyright &copy; 2009-<?php echo date('Y') ?> <a href="/">gra.ua</a> </p>
         </div>
            </div> 
         </div>

</footer>
<!-- BEGIN JIVOSITE CODE {literal} -->
<script type='text/javascript'>
(function(){ var widget_id = 'UtZv2WbgL5';var d=document;var w=window;function l(){
var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();</script>
<!-- {/literal} END JIVOSITE CODE -->

        <script type="text/javascript">
		   
	 $('.product_item').live('click', function() {
		product_id = $(this).find('.remarketing-id').attr('data-product_id');
		list = $(this).find('.remarketing-id').attr('data-list');
		
		if(product_id) {
		
		  $.ajax({ 
              type: 'post',
              url:  'index.php?route=product/product/shortProduct',
			  data: 'product_id='+product_id,
              dataType: 'json',
              success: function(json) {
			  if (json['success']){
			  price = parseFloat(json['product']['price']).toFixed(2);
			  special = parseFloat(json['product']['special']).toFixed(2);
			  if(!isNaN(special) && special > 0) price = special;
				var heading = $('h1').text();
				
				if(list != undefined) heading = list;
			window.dataLayer = window.dataLayer || [];
			dataLayer.push({
						'ecommerce': {
						'currencyCode': 'UAH',
						'actionField': {'list': heading },
						'click': {                                
							'products': [{                       
							'name': json['product']['name'],
							'id': json['product']['model'],
							'brand': json['product']['manufacturer'],
							'price': price,
							'category': heading
							}]
    }},
		'event': 'gtm-ee-event',
		'gtm-ee-event-category': 'Enhanced Ecommerce',
		'gtm-ee-event-action': 'Product Clicks',
		'gtm-ee-event-non-interaction': 'False'
	});		
}}})}});

	 $('.product_item .button_green_list').live('click', function() {
		product_id = $(this).find('.remarketing-id').attr('data-product_id');
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
						'actionField': {'list': heading },
						'add': {                                
							'products': [{                      
							'name': json['product']['name'],
							'id': json['product']['model'],
							'brand': json['product']['manufacturer'],
							'price': price,
							'category': heading
							}]
    }},
		'event': 'gtm-ee-event',
		'gtm-ee-event-category': 'Enhanced Ecommerce',
		'gtm-ee-event-action': 'Adding a Product to a Shopping Cart',
		'gtm-ee-event-non-interaction': 'False'
	});		 
}}})}});

	 $('.product_info .button_green').live('click', function() {
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
						'actionField': {'list': heading },
						'add': {                                
							'products': [{                      
							'name': json['product']['name'],
							'id': json['product']['model'],
							'brand': json['product']['manufacturer'],
							'price': price,
							'category': heading
							}]
    }},
		'event': 'gtm-ee-event',
		'gtm-ee-event-category': 'Enhanced Ecommerce',
		'gtm-ee-event-action': 'Adding a Product to a Shopping Cart',
		'gtm-ee-event-non-interaction': 'False'
	});		 
}}})}});

		</script>
<?php if(isset($checkout_ecommerce) && $checkout_ecommerce) echo $checkout_ecommerce;?>
</body></html>