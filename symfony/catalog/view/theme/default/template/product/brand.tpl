<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div class="content brand_page">
    <h1 class="heading"><?php echo $heading_title; ?></h1>

        <?php if ($products) { ?>
            <?php include("catalog/view/theme/default/template/common/_product_panel.tpl"); ?>
            <?php include("catalog/view/theme/default/template/common/_product_list.tpl"); ?>
            <div class="pagination"><?php echo $pagination; ?></div>
        <?php } else { ?>
            <div class="brand_description"><?php echo $text_error; ?></div>
        <?php } ?>
</div>
<?php echo $footer; ?> 