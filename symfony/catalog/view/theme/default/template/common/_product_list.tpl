<?php if($products) { ?>
<div class="pagination"><?php echo $pagination; ?></div>
<div class="product_list product_list_grid">
    <?php foreach ($products as $key => $product) {
/*
    if ($_SERVER["HTTP_CF_CONNECTING_IP"] == '46.219.78.155' ) {
    print_r($product);
    exit;
    }
*/

    $category = explode('/',$_SERVER['REQUEST_URI']);
    $category = end($category);
    ?>
    <div class="product_item<?php if (!($key%3)) echo ' first'; ?>">
        <a class="image" id="big_image<?php echo $product['product_id'] ?>"
           href="<?php echo str_replace('&', '&amp;', $product['href']); ?>">
            <img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>"
                 alt="<?php echo $product['name']; ?>"/>
            <div class="status <?php if($product['old_price']){ echo 'promotional ';}else {if($product['is_new']) echo 'new ';} ?>"></div>
        </a>
        <div class="info_block">
            <a class="name" href="<?php echo str_replace('&', '&amp;', $product['href']); ?>">
                <?php echo $product['name']; ?>
            </a>
            <div class="desc">
                <?php echo $product['short_description'] ?>
            </div>
            <div class="price_block">
                <?php if ($product['stock_status']=='Есть в наличии') { ?><a class="button_green_list remarketing-id" data-product_id="<?php echo $product['product_id'];?>"
                                                                             href="<?php echo str_replace('&', '&amp;', $product['add']); ?>"
                                                                             title="<?php echo $button_add_to_cart; ?>"></a> <?php } else { ?>
                <div style=""></div>
                <?php
					// padding: 17px 50px;
					} ?>
                <!--<span class="product_status<?php if ($product['stock_status']=='Есть в наличии') {?> in_stock<?php } else if ($product['stock_status']=='Нет в наличии') { ?> out_of_stock<?php } ?>"><?php echo $product['stock_status']; ?></span>-->
                <span class="price"><?php
					if ($product['stock_status']=='Есть в наличии') { 
					echo $product['price']; 
					} 
					?></span>
                <?php if ($product['old_price'] && ($product['stock_status']=='Есть в наличии')){ ?>
                <span class="old_price"><?php echo $product['old_price']; ?></span>
                <?php } ?>

                <div class="" style="
						position: absolute;
						color: #000000;
						font-size: 10px;
						bottom: 0px;
					">Код: <?php echo $product['model']; ?></div>

            </div>
        </div>
    </div>

    <?php } ?>
</div>
<div class="pagination"><?php echo $pagination; ?></div>
<?php } ?>
