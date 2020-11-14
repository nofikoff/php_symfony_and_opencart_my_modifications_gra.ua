<?php if ($products) { ?>
<div class="box">
    <div class="corner"></div>
    <div class="inner">
    <h3><?php echo $heading_title; ?></h3>
        <div class="products">
        <?php foreach ($products as $product) { ?>
           <a class="item" href="<?php echo str_replace('&', '&amp;', $product['href']); ?>">
               <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" />
             <span class="info">
                <span class="name"><?php echo $product['name']; ?></span>
                
                <?php if (!$product['special']) { ?>
                    <span class="price"><?php echo $product['price']; ?></span>
                <?php } else { ?>
                    <span class="old_price"><?php echo $product['price']; ?></span>
                    <span class="price"><?php echo $product['special']; ?></span>
                <?php } ?>
             </span>
          </a>
        <?php } ?>
        </div>
    </div>
</div>
<?php } ?>


