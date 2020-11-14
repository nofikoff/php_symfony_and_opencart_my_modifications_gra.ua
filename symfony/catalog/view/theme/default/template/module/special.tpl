<div class="special">  
<?php  $id = 'special_products'; $text = $heading_title?>
        <?php include("catalog/view/theme/default/template/common/_carusel_list.tpl"); ?> 
    <?php if($products){?>
        <a class="read_more special_link" href="<?php echo $special_href?>"><?php echo $text_all_promotional_products?></a>
    <?php }?>
</div>
