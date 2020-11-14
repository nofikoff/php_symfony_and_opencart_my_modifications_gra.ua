    <?php if($related_products) { ?>
    <div class="carousel_box" id="n0001">
        <h3><?php echo $text_related; ?></h3>
        <div id="related_carousel" class="carousel" >
            <div class="slide_holder">
              <ul>
                 <?php foreach($related_products as $related_product) { ?>
                    <li>
                         <a href="<?php echo str_replace('&', '&amp;',$related_product['href']) ?>" title="<?php echo $related_product['name'] ?>">
                            <img src="<?php echo $related_product['thumb'] ?>" alt="" />
                            <div class="status <?php if($related_product['old_price']){ echo 'promotional ';}else {if($related_product['is_new']) echo 'new ';} ?>"></div>
                            <span class="name"><?php echo $related_product['trim_name'] ?></span>
                            <span class="price"><?php echo $related_product['price'] ?></span>
                         </a>
<!--                         <div class="button_box">
                           <a class="button_buy" href="" id="add_to_cart"><span><?php // echo $button_add_to_cart; ?></span></a>
                           <div id="but<?php// echo $product_id ?>" class="wish_list">    
                               <?php // if(isset($this->session->data['wish_list'])){?>
                                <?php //if(!in_array($product_id, $this->session->data['wish_list'])){?>
                                    <a class="button_wish"  onclick="addToWishList(<?php //echo $product_id; ?>, '<?php// echo $category_id; ?>')" >В список желаний</a>
                                <?php //}  else {?>
                                    <a class="button_wish_minus" onclick="delFromWishList(<?php //echo $product_id; ?>, '<?php// echo $category_id; ?>')">Удалить из списка</a> 
                                <?php // }?>
                                <?php //}else{?>
                                    <a class="button_wish" onclick="addToWishList(<?php //echo $product_id; ?>, '<?php// echo $category_id; ?>')">В список желаний</a>
                               <?php //}?> 
                            </div>
                       </div>-->
                    </li>
                  <?php } ?>
              </ul>
            </div>
            <div class="carousel_link">
                <a  class="related_prev prev"  title="next"></a>
                <a  class="related_next next"  title="prev"></a>
            </div>
       </div>
  </div>

<script type="text/javascript">
$(function() {
$("#related_carousel").jCarouselLite({
    btnNext: ".related_next",
    btnPrev: ".related_prev",
    vertical: false,
    circular: false,
    visible: 3,
    scroll: 1
});
});
</script>
<?php } ?>