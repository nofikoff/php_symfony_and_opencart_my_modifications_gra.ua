<div class="product_panel">  
        <label><?php echo $text_sort; ?></label>
        <?php foreach ($sorts as $sort) { ?>
            <a class="read_more" href="<?php echo str_replace('&', '&amp;', $sort['href']); ?>" value="<?php echo str_replace('&', '&amp;', $sort['href']); ?>" <?php if($sort['active']) echo 'id="active"' ?>>
                <?php echo $sort['text']; ?></a>
        <?php } ?>
</div>


