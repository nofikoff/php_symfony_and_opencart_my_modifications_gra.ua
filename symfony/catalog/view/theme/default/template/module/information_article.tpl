<div class="text info">
	<h3>
		<?php echo $heading_title; ?></h3>
	<ul class="check">
        <?php foreach ($informations as $information) { ?>
            <li>
                  <a href="<?php echo str_replace('&', '&amp;', $information['href']); ?>"><?php echo $information['title']; ?></a>
            </li>
        <?php } ?>        
	</ul>
</div>
