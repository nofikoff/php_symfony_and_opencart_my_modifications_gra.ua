<?php echo $header; ?>

 <div class="column_left">
     <div id="filter"></div>
     <?php if($filter_params){?>
        <div class="filter_box">
             <?php foreach($filter_params as $key => $param) { ?>             
                 <div class="param_name"><?php echo $param['name'] ?>:</div>
                 <div class="filter">
                 <ul>
                   <?php foreach ($param['values'] as $item) { ?>
                     <li><a <?php if ($item['active']) echo 'class="active"' ?> href="<?php echo str_replace('&', '&amp;', $item['href']); ?>"><?php echo $item['value']; ?> </a></li>
                   <?php }?>
                 </ul>
                 </div>
             <?php } ?>
             <?php if(isset($clear_filters_href)){?>
                <a class="filter_link button_f" href="<?php echo str_replace('&', '&amp;', $clear_filters_href); ?>"><?php echo $text_clear_filters; ?></a>
             <?php }?>
       </div>
     <?php }?>
     <?php if($parametr_categories) { ?>
     <h2>Коллекции</h2>
       <div class="nav_list">
            <?php foreach($parametr_categories as $key => $param) { ?> 
              <div class="item">
                <a class="name " href="<?php echo $param['href'] ?>">
                    <img class="image" src="<?php echo $param['image']; ?>">
                    <span><?php echo $param['value'] ?></span>
                </a>
                <?php if($param['active']){?>
                    <div class="filter group_param">
                        <ul>
                            <?php foreach ($param['categoty'] as $categoty) { ?>
                            <li><i>-</i>&nbsp;<a <?php if ($categoty['active']) echo 'class="active"' ?> href="<?php echo str_replace('&', '&amp;', $categoty['href']); ?>"><?php echo $categoty['name']; ?></a></li>
                            <?php }?>
                        </ul>
                    </div>
                <?php } ?>
              </div>
            <?php } ?>
       </div>
     <?php } ?>
     <?php if ($informations) { ?>
<!--    <div class="info_list">
        <h2>Информация</h2>
        <ul>
        <?php// foreach ($informations as $information_i) { ?>
            <li>
                  <a href="<?php// echo str_replace('&', '&amp;', $information_i['href']); ?>"><?php// echo $information_i['title']; ?></a>
            </li>
        <?php// } ?>        
        </ul>
    </div>-->
    <?php } ?>
    </div>
    <div class="content category_page" >
         <h1 class="heading"><?php echo $heading_title; ?></h1>
            <?php if (!$categories && !$products) { ?>
            <div class="category_description"><?php echo $text_error; ?></div>
            <?php } ?>
            <?php include("catalog/view/theme/default/template/common/_category_list.tpl"); ?>
            <?php if ($products) { ?>
                <?php include("catalog/view/theme/default/template/common/_product_panel.tpl"); ?>
                <?php include("catalog/view/theme/default/template/common/_product_list.tpl"); ?>
            <?php } ?>
            <?php if($categories||$products) { ?>
			<?php if(!$_SESSION['widget_switcher']): ?>
                <?php echo $special; ?>
                <?php echo $latest; ?>
                <?php echo $hit; ?>
            <?php else: ?>
                
            <?php endif; ?>
				<noindex>
                <?php if($description) { ?>
                    <div class="text category_seo"><?php echo $description;?></div>
                    <a id="cat_seo_desc" class="link_preview" rel=".category_seo" alt="Скрыть"><span>Подробнее</span></a>
                <?php } ?>
				</noindex>
            <?php }?>
        </div>
<?php if(isset($keywords)){?>
<!-- Google Code for &#1058;&#1077;&#1075; &#1088;&#1077;&#1084;&#1072;&#1088;&#1082;&#1077;&#1090;&#1080;&#1085;&#1075;&#1072; -->
<!-- Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. For instructions on adding this tag and more information on the above requirements, read the setup guide: google.com/ads/remarketingsetup -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 999045519;
var google_conversion_label = "UZB-COG2kAQQj_Ow3AM";
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/999045519/?value=0&amp;label=UZB-COG2kAQQj_Ow3AM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
<?php }?>
    <?php #if ($seo_text) { 
	?>
        <div class="text">
<!-- 102okWC021kmxEQWFkv0Cwo032p0CWlx10wxloEF020Cp3p231qPBmxsxQEWd -->

<!-- /102okWC021kmxEQWFkv0Cwo032p0CWlx10wxloEF020Cp3p231qPBmxsxQEWd -->
		<?php 
		//echo '<div class="seoshield_content"></div>';
		echo $seo_text;
		?></div>
    <?php #} ?>
	<?php if(isset($ecommerce) && $ecommerce) echo $ecommerce;?>

<?php echo $footer; ?> 
