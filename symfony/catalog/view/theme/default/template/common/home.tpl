<?php echo $header; ?>
<div class="home_page">
   <?php echo $column_left; ?>
   <div class="content">
       <?php if($images) { ?>
       <script src="catalog/view/javascript/jquery/jquery.featureList.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript">
            $(function(){
                $.featureList(
                $("#slideshow .names li"),
                $("#slideshow .output li"), {
                    start_item  :  0,
                    transition_interval: 5000
                }
            );
            });
        </script>
       <div class="slideshow" id="slideshow">
           <ul class="output">
               <?php foreach ($images as $image) { ?>
                   <li>
                       <a href="<?php echo $image['link_href']; ?>" title="<?php echo $image['name']; ?>">
                           <img src="<?php echo $image['image']; ?>" alt="<?php echo $image['name']; ?>" />
                       </a>
                   </li>
               <?php } ?>
           </ul>
           <ul class="names">
               <?php foreach ($images as $image) { ?>
                   <li>

                   </li>
               <?php } ?>
           </ul>
           <a id="slideshow_next" class="next" href="javascript:;" title="next"></a>
           <a id="slideshow_prev" class="prev" href="javascript:;" title="prev"></a>
        </div>
        <?php } ?>
        <div class="category_list">
           <?php $num_cat = 0 ?>
           <?php 

//print_r($parametr_categories);

foreach ($parametr_categories as $value) {?>
             <a class="item<?php if(!($num_cat%3)) echo ' first';?>" href="<?php echo $value['href'];?>">
                       <span class="name"><?php echo $value['value'];?></span>
                       <img src="<?php echo $value['big_image']; ?>"/>
                   </a>  
            <?php $num_cat++ ?>
           <?php } ?>
        </div>
        <?php foreach ($modules as $module) { ?>
            <?php echo ${$module['code']}; ?>
        <?php } ?> 
        <?php echo $top;?>
   </div>
    <?php #if ($seo_text) { 
	?>
  
        <div class="text 102okWC021kmxEQWFkv0Cwo032p0CWlx10wxloEF020Cp3p231qPBmxsxQEWd seo_text">
<!-- 102okWC021kmxEQWFkv0Cwo032p0CWlx10wxloEF020Cp3p231qPBmxsxQEWd -->

<!-- /102okWC021kmxEQWFkv0Cwo032p0CWlx10wxloEF020Cp3p231qPBmxsxQEWd -->
		<?php 
		//echo '<div class="seoshield_content"></div>';
		echo $seo_text;
		?></div>
        <a class="link_preview" rel=".102okWC021kmxEQWFkv0Cwo032p0CWlx10wxloEF020Cp3p231qPBmxsxQEWd" alt="Скрыть"><span>Подробнее</span></a>
    <?php #} 
	?>
</div>  
<?php echo $footer; ?>
