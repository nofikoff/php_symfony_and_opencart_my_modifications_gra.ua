<?php if(!isset($_SESSION['widget_switcher']) or !$_SESSION['widget_switcher']): ?>
        <?php  $id = 'latest_products'; $text = $heading_title?>
        <?php include("catalog/view/theme/default/template/common/_carusel_list.tpl"); ?>  
<?php else: ?>
    <div class="dp-widget-container" data-widget-location-id="86824613-2634-4b66-b404-59bb27fc195c"></div>
<?php endif; ?>