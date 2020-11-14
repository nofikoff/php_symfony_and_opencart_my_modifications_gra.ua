<div class="box">
    <div class="corner"></div>
    <div class="inner">
        <h3><?php echo $heading_title; ?></h3>
        <div class="menu">
          <?php foreach ($information_categories as $information_category) { ?>
          <a href="<?php echo str_replace('&', '&amp;', $information_category['href']); ?>"><?php echo $information_category['name']; ?></a>
          <?php } ?>
        </div>
    </div>
</div>