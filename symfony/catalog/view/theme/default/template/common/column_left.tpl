<div class="column_left">
  <?php foreach ($modules as $module) { ?>
  <?php echo ${$module['code']}; ?>
  <?php } ?>



    <div id="filter">

        <?php echo $search;?>
    </div>


    <?php if ($parametr_categories) { ?>
    <h2>Коллекции</h2>
    <div class="nav_list">
        <?php foreach($parametr_categories as $key => $param) { ?>
         <div class="item">
            <a class="name" href="<?php echo $param['href'] ?>">
                <?php /*<img class="image" src="<?php echo $param['image']; ?>"/> */ ?>
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
                                        <li class="catsmall"><i>-</i>&nbsp;<a  <?php if ($categoty['active']) echo 'class="active"' ?> href="<?php echo str_replace('&', '&amp;', $categoty['href']); ?>"><?php echo $categoty['name']; ?></a></li>
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
<!--    <div class="info_list">



        <h2>Информация</h2>
        <ul> 


        <?php
		// foreach ($informations as $information_i) { ?>
            <li>
                  <a href="<?php
				  // echo str_replace('&', '&amp;', $information_i['href']); ?>">
				  <?php
				  // echo $information_i['title']; 
				  ?></a>
            </li> 
        <?php
		// } ?>        
        </ul>
    </div>-->
    <?php } ?>

     
</div>
