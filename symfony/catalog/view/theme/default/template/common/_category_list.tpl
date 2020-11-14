<?php if($categories) { ?>
<div class="category_list">
<?php foreach ($categories as $key => $category) { ?>
    <a class="item <?php if(!($key%3)) echo 'first';?>" href="<?php echo str_replace('&', '&amp;', $category['href']); ?>">
       <span class="name"><?php echo $category['name']; ?></span>
       <span class="image"><img src="<?php echo $category['thumb']; ?>" title="<?php echo $category['name']; ?>" alt="<?php echo $category['name']; ?>" /></span>
       
    </a>
<?php } ?>
</div>
<?php } ?>