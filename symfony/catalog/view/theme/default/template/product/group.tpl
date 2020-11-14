<?php echo $header; ?>

    <div class="column_left">
        <div id="filter"></div>
        <?php if($parametr_categories) { ?>
        <h2>Коллекции</h2>
        <div class="nav_list">
           <?php foreach($parametr_categories as $key => $param) { ?> 
              <div class="item">
            <a class="name <?php if($param['active'])echo 'active';?>" href="<?php echo $param['href'] ?>">
                        <img class="image" src="<?php echo $param['image']; ?>">
                        <span><?php echo $param['value'] ?></span>
                     </a>
                     <?php if($param['active']){?>
                        <div class="filter group_param">
                            <ul>
                            <?php $index=0; foreach ($param['categoty'] as $key => $categoties) { ?>
                                <li>
                                    <a class="cat_tree" ref="c<?php echo $index?>"><?php echo $key;?></a>
                                    <ul class="cat_tree_list c<?php echo $index?> <?php foreach ($categoties as $categoty) { if ($categoty['active']){ echo 'active '; break;} }?>">
                                        <?php foreach ($categoties as $categoty) { ?>
                                            <li <?php if ($categoty['active']) echo 'class="active"' ?>><i>-</i>&nbsp;<a href="<?php echo str_replace('&', '&amp;', $categoty['href']); ?>"><?php echo $categoty['name']; ?></a></li>
                                        <?php }?>
                                    </ul>
                                </li>
                             <?php $index++; }?>
                            </ul>
                        </div>
                     <?php } ?>
                  </div>
              <?php } ?>
           </div>
        <?php } ?>
        <?php if ($informations) { ?>
<!--        <div class="info_list">
            <h2>Информация</h2>
            <ul>
            <?php
			// foreach ($informations as $information_i) { ?>
                <li>
                      <a href="<?php
					  // echo str_replace('&', '&amp;', $information_i['href']); ?>"><?php
					  // echo $information_i['title']; ?></a>
                </li>
            <?php
			// } ?>        
            </ul>
        </div>-->
    <?php } ?>
    </div>

    <div class="content category_page" >
        <h1 class="heading"><?php echo $heading_title; ?></h1>
            <?php if ( !$products) { ?>
            <div class="category_description"><?php echo $text_error; ?></div>
            <?php }else{ ?>
                <?php include("catalog/view/theme/default/template/common/_product_panel.tpl"); ?>
                <?php include("catalog/view/theme/default/template/common/_product_list.tpl"); ?>
            <?php } ?>        
   	

	</div>
	<script type="text/javascript">
    $('.cat_tree').click(function(){
        $('.'+$(this).attr('ref')).toggleClass('active');
    })
    
</script>
<?php echo $footer; ?> 
