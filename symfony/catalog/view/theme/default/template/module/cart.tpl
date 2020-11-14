<div class="cart">
    <?php if ($item > 0 ) { ?>
        <div class="cart_info">
            <span class="total">Корзина:</span>
                <a href="<?php echo str_replace('&', '&amp;', $action_cart); ?>"><span><?php echo $item ?></span><?php echo $text_item ?></a>
                <!--<?php// echo $text_cart_price ?> <span><?php //echo $total ?></span>-->
         </div>
        <div class="drop">
            <?php $index = 1; foreach ($products as $product) { if($index<6){ ?>
                <div class="item" id="product<?php echo $product['key'] ?>">
                    <a class="image" href="<?php echo str_replace('&', '&amp;', $product['href']); ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>
                    <a class="name" href="<?php echo str_replace('&', '&amp;', $product['href']); ?>"><?php echo $product['name']; ?><?php if($product['quantity'] > 1) echo '(' . $product['quantity'] . 'шт.)' ?></a>
                    <span class="price"><?php echo $product['price']; ?></span>
                    <a class="delete cart_remove" onclick="cartRemove(<?php echo $product['key'] ?>);"></a>
                </div>
            <?php } $index++; }?>
            <a class="read_more" href="<?php echo str_replace('&', '&amp;',$action_cart); ?>"><span><?php echo $text_checkout; ?></span></a>
        </div>
     <?php  } else { ?>
         <div class="cart_info">
             <div class="empty"><?php echo $text_empty ?></div>
         </div>
    <?php  }  ?>
</div>

<script type="text/javascript">
function hasUrl(name) {
  var str = window.location.href;
  return str.indexOf(name) !== -1;
}
function cartRemove(id) {
		$.ajax({
			type: 'post',
			url: 'index.php?route=module/cart/callback',
			dataType: 'html',
			data: 'remove=' + id,
			success: function (html) {
				$('.cart').html(html);
				if (hasUrl('cart') || hasUrl('order')) {
					window.location.reload();
				}
			}
		});
  return false;
};
</script>