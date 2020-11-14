<?php echo $header; ?> <?php echo $column_left; ?> 
<div class="content special_page">
  <h1 class="heading"><?php echo $heading_title; ?></h1>
  <?php include("catalog/view/theme/default/template/common/_product_panel.tpl"); ?>
  <?php include("catalog/view/theme/default/template/common/_product_list.tpl"); ?>
  <div class="pagination"><?php echo $pagination; ?></div>
</div>
</div>
<?php echo $footer; ?> 