<?php echo $header; ?><?php echo $column_left; ?>
<div class="content search_page">
    <h1 class="heading"><?php echo $heading_title; ?></h1>
    <?php if (isset($products) && $products) { ?>
          <?php include("catalog/view/theme/default/template/common/_product_panel.tpl"); ?>
          <?php include("catalog/view/theme/default/template/common/_product_list.tpl"); ?>

    <?php } else { ?>
          <div class="block"><?php echo $text_empty; ?></div>
    <?php } ?>


</div>
<?php echo $footer; ?> 