<?php echo $header; ?>
<div class="content sitemap_page">
  <h1><?php echo $heading_title; ?></h1>
  <div class="alphabet">
          <div class="rus">
              <?php foreach ($rus as $let) { ?>
              <a href="<?php echo $letter_href . '&letter=' . $let?>" class="letter <?php if($let==$letter) echo 'active'?>"><?php echo $let?></a>
              <?php } ?>
          </div>
          <div class="eng">
              <?php foreach ($eng as $let) { ?>
              <a href="<?php echo $letter_href . '&letter=' . $let?> " class="letter <?php if($let==$letter) echo 'active'?>"><?php echo $let?></a>
              <?php } ?>
              <a href="<?php echo $letter_href ?>" class="letter"><b>Все</b></a>
          </div>
      </div>
  <div class="text">
      
        <?php if($category) echo $category; ?>
        <ul>
            <li>
                <ul>
                    <?php foreach ($infos as $info) { ?>
                        <?php echo $info['label']; ?>
                        <li><a href="<?php echo str_replace('&', '&amp;', $info['href']); ?>"><?php echo $info['title']; ?></a></li>
                    <?php } ?>
                </ul>
            </li>
          </ul>
      
  </div>
  <div class="product_panel">
    <div class="pagination">
        <?php echo $pagination; ?> 
    </div>
  </div>
  </div>
<?php echo $footer; ?> 