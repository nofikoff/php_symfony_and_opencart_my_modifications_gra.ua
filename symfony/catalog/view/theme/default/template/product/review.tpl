<?php if ($reviews) { ?>
<?php foreach ($reviews as $review) { ?>
<div class="block"><span class="name"><?php echo $review['author']; ?></span><img src="<?php echo HTTP_SERVER ?>catalog/view/theme/default/image/stars_<?php echo $review['rating'] . '.png'; ?>" alt="<?php echo $review['stars']; ?>" />
  <div class="date"><?php echo $review['date_added']; ?></div>
  <div class="comment"><?php echo $review['text']; ?></div>
</div>
<?php } ?>
<div class="pagination"><?php echo $pagination; ?></div>
<?php } else { ?>
<div class="block"><?php echo $text_no_reviews; ?></div>
<?php } ?>

<div class="heading" id="review_title"><?php echo $text_write; ?></div>
<div class="content_review">
	<label>Ваше имя: </label>
	<input type="text" name="name" value="" size="30"/>
	<br />
	<br />
	<label>Ваш отзыв: </label>
	<textarea name="text" rows="7"></textarea>
	<br />
	<br />
	<strong>Ваша оценка: &nbsp;</strong>
	<span><?php echo $entry_bad; ?></span>
	<input type="radio" name="rating" value="1" style="margin: 0;" />
	<input type="radio" name="rating" value="2" style="margin: 0;" />
	<input type="radio" name="rating" value="3" style="margin: 0;" />
	<input type="radio" name="rating" value="4" style="margin: 0;" />
	<input type="radio" name="rating" value="5" style="margin: 0;" />
	<span><?php echo $entry_good; ?></span>
	<br />
	<br />
	<div class="captcha">
		<strong><?php echo $entry_captcha; ?></strong>
		<input class="number_capcha" type="text" name="captcha" value="" autocomplete="off" />
		<img src="<?php echo HTTP_SERVER ?>index.php?route=product/product/captcha" id="captcha" />
		<a onclick="review();" class="button_request_2"></a>
	</div>
</div>