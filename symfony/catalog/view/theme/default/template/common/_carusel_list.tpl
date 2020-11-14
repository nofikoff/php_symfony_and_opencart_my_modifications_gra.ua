<?php if($products) { ?>
<div class="carousel_box" id="n0002">
    <h3><?php echo $text; ?></h3>
    <div id="<?php echo $id;?>" class="carousel<?php if(count($products) < 4) echo ' small';?>">
        <div class="slide_holder">
            <ul>
                <?php foreach($products as $product) { ?>
                <li>
                    <a class="item"
                       href="<?php echo str_replace('&', '&amp;',$product['href']); if($source) echo '?utm_source=internal&utm_content=recommendz_productfootertabs'; ?>"
                       title="<?php echo $product['name'] ?>">
                        <span class="name"><?php echo $product['trim_name'] ?></span>
                        <span class="image"><img src="<?php echo $product['thumb'] ?>" alt=""/></span>
                        <div class="status <?php if($product['old_price']){ echo 'promotional ';}else {if($product['is_new']) echo 'new ';} ?>"></div>

                        <span class="price"><?php echo $product['price'] ?></span>
                        <?php if ($product['old_price']){ ?>
                        <span class="old_price"><?php echo $product['old_price'] ?></span>
                        <?php } ?>
                    </a>


                    <!-- by Novikov 2018 -->
                    <?php if (trim($product['stock_status'])=='Есть в наличии') { ?>
                    <a class="button_green_carusel"
                       href="<?php if($source) echo str_replace('&', '&amp;',$product['href']) . '?utm_source=internal&utm_content='.$source.''; else echo $product['add']; ?>"
                       id="add_to_cart"><span></span></a>
                    <?php } else {
echo $product['stock_status'];
?>

                    <?php }
?>


                </li>
                <?php } ?>
            </ul>
        </div>
        <?php if(count($products) > 3) { ?>
        <div class="carousel_link">
            <a class="<?php echo $id;?>_prev prev" title="next"></a>
            <a class="<?php echo $id;?>_next next" title="prev"></a>
        </div>
        <?php } ?>
    </div>
</div>
<?php if(count($products) > 3) { ?>
<!--<script type="text/javascript" src="/catalog/view/javascript/jquery/jcarousellite_1.0.1.min.js"></script>-->
<script type="text/javascript">
    $(function () {
        $("#<?php echo $id;?>").jCarouselLite({
            btnNext: ".<?php echo $id;?>_next",
            btnPrev: ".<?php echo $id;?>_prev",
            vertical: false,
            circular: true,
            visible: 3,
            scroll: 1
        });
    });
</script>
<?php } ?>
<?php } ?>