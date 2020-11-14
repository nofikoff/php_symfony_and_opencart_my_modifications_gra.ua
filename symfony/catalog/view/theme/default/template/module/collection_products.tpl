<?php if ($products) { ?>
    <?php $id = 'collection_products';
    $text = 'Другие товары коллекции ' . $name . ':';?>
    <?php include("./catalog/view/theme/default/template/common/_carusel_list.tpl"); ?>
<?php } ?>