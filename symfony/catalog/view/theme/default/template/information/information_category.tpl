<?php echo $header; ?>
    <?php echo $column_left; ?>
    <div class="content info_page">
      <h1 class="heading"><?php echo $heading_title; ?></h1>
      <ul class="infocategory_list">
        <?php foreach ($category_informations as $category_information) { ?>
          <li class="item">
            <!--<div class="date"><?php echo $category_information['date_added']; ?></div>-->
            <h2><a class="name" href="<?php echo str_replace('&', '&amp;', $category_information['href']); ?>"><?php echo $category_information['title']; ?></a></h2>

            <div class="desc">
              <?php echo html_entity_decode($category_information['short_description']); ?>
            </div>
            <a class="read_more" href="<?php echo str_replace('&', '&amp;', $category_information['href']); ?>">Показать полностью...</a>
          </li>
        <?php } ?>
      </ul>
      <div class="pagination"><?php echo $pagination; ?></div>
    </div>

<?php echo $footer; ?> 