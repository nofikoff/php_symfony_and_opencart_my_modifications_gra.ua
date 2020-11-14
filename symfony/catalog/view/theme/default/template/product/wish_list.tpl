 <?php echo $header; ?> <?php echo $column_left; ?>
<div class="content wish_page">
    <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a> <i>/</i> 
    <?php } ?>
  </div> 
  <h1><?php echo $heading_title; ?></h1>
  <a href="<?php echo $continue; ?>" class="read_more"><span> Вернуться назад </span></a>
  <?php if ($categories) { ?>
        <?php foreach ($categories as $category) { ?>
    <div class="product_list">
        <?php if(isset($category['products'])&&$category['products']){ ?>
        <?php $key_prod = 0; ?>
        <h3 class="heading"><?php echo $category['name'] ?></h3>
          <?php foreach ($category['products'] as $key => $product) { ?>
           <div class="product_item <?php if(!($key_prod%3)) echo 'first';?>">
           <?php $key_prod++; ?>
             <div class="<?php if(!$product['status']) echo 'out_of_stock'?>">

                
                <a class="item" href="<?php echo $product['href']; ?>">
                <span class="name"><?php echo $product['name']; ?></span>
                <?php if ($product['thumb']) { ?>
                    <span id="big_image<?php echo $product['product_id'] ?>" img="<?php echo $product['thumb']; ?>">
                        <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" />
                    </span>
                <?php } ?>
                <span class="price_block">
                    <?php if ($product['price']) { ?>
                    <?php if (!$product['special']) { ?>
                    <span class="price"><?php echo $product['price']; ?></span>
                    <?php } else { ?>
                    <span class="price"><?php echo $product['special']; ?></span>
                    <span class="old_price"><?php echo $product['price']; ?></span>
                    <?php } ?>
                    <?php } ?>
                </span>
                </a>
                 <div class="button_box">
                    <a href="<?php echo $product['add']?>" class="button_buy"><?php echo $button_cart; ?></a>
                    <form action="<?php echo $action; ?>" class="wish_list" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="remove" value="<?php echo $product['product_id']; ?>" />
                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>"  />
                        <a class="button_wish_minus" onclick="$(this).parent().submit();" title="Удалить из списка желаний">Удалить из списка</a>
                </form>
                 </div>
            </div>
        </div>
        <?php } ?>
       <?php } ?>
          </div>
      <?php } ?>

  <?php } else { ?>
  <div class="error" ><?php echo $text_empty; ?><a href="<?php echo $continue; ?>" class="button_buy"><span>Назад</span></a></div>
 
  <?php } ?>
  </div>

  
<?php echo $footer; ?>