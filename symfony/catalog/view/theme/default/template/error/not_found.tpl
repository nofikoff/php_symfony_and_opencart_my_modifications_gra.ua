<?php echo $header; ?>
<?php echo $column_left; ?>
<div class="content error_page">
               <h1><?php echo $text_error; ?></h1>
        <a onclick="location = '<?php echo str_replace('&', '&amp;', $continue); ?>'" class="button"><span><?php echo $button_continue; ?></span></a>

  </div>
</div>
 </div>
<?php echo $footer; ?> 