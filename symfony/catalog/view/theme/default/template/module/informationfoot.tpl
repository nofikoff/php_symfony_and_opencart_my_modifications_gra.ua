  <?php foreach ($informations as $information) { ?>
  <a href="<?php echo str_replace('&', '&amp;', $information['href']); ?>" <?php if($information['title'] == 'Адрес офиса') echo 'class="notranslate"'; ?> ><?php echo $information['title']; ?></a>
  <?php } ?>

