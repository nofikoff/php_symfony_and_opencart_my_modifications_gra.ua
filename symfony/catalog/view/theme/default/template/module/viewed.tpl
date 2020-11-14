<?php if($viewed_products) { ?>
      <?php $products = $viewed_products; $id = 'viewed_products'; $text = $text_viewed?>
      <?php include("catalog/view/theme/default/template/common/_carusel_list.tpl"); ?>  
<?php } ?>
