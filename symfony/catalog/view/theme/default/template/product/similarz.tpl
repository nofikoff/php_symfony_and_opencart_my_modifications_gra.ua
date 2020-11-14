<?php if ($similar_products) { ?>
	<div id="tab_similar_products">
		<?php $products = $similar_products; ?>
		<?php $id = 'similar_products'; ?>
		<?php $text = $text_similar ?>
		<?php include("catalog/view/theme/default/template/common/_carusel_list.tpl"); ?>  
	</div>
<?php } ?>
<?php if ($similar_price_products) { ?>
	<div id="tab_similar_price_products">
		<?php $products = $similar_price_products; ?>
		<?php $id = 'similar_price_products'; ?>
		<?php $text = $text_similar_price; ?>
		<?php include("catalog/view/theme/default/template/common/_carusel_list.tpl"); ?>  
	</div>
<?php } ?>