<?php echo $header; ?>

<script type="text/javascript">
var google_tag_params = {
dynx_itemid: '<?php echo $heading_title . ', ' . $model; ?>',
dynx_pagetype: 'offerdetail',
dynx_totalvalue: <?php $price_dynx = str_replace('<smal> грн</smal>', '', $price); echo str_replace(' ', '', $price_dynx); ?>,
};
</script>
<?php $showHide = false;?>

<?php /* dynx_itemid2: 'REPLACE_WITH_VALUE',*/
/*dynx_pagetype: 'other', */?>

<div class="product_page" >
    <?php if($showHide): ?>
	<span itemprop="mpn" style="display: none;"><?php echo $model; ?></span>
<?php endif; ?>
    <h1 itemprop="name"><?php echo $heading_title; ?></h1>

    <div class="product_top">
        <div class="info">
            <?php echo $product_page_block; ?>
        </div>
        <div class="product">
            <div class="image_block">
                <?php if ($images) { ?>
                    <div class="main-pr-image">
                        <?php $alt = str_replace('"', '', $heading_title); ?>
                        <img itemprop="image" src="<?php echo $middle; ?>" title="<?php echo $alt; ?>" alt="<?php echo $alt; ?>" id="bigpic" rel="-1" />
                    </div>
                    <div id="thumbs_carousel" class="thumbs">
                        <div class="slide_holder">
                            <ul>
                                <li>
                                    <a value="<?php echo str_replace('&', '&amp;', $middle); ?>" href="<?php echo str_replace('&', '&amp;', $popup); ?>" class="colorbox image" rel="gallery" id="thumb_link-1">
                                        <img src="<?php echo $thumb; ?>" title="<?php echo str_replace('&', '&amp;', $heading_title); ?>" alt="<?php echo $heading_title; ?>" />
                                    </a>
                                </li>
                                <?php foreach ($images as $image) { ?>
                                    <li>
                                        <a value="<?php echo str_replace('&', '&amp;', $image['middle']); ?>" href="<?php echo str_replace('&', '&amp;', $image['popup']); ?>" class="colorbox image" rel="gallery" id="thumb_link<?php echo $image['image_id'] ?>">
                                            <img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" />
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="carousel_link">
                            <a class="thumbs_prev" title="prev"></a>
                            <a class="thumbs_next" title="next"></a>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="main-pr-image">
                        <a value="<?php echo str_replace('&', '&amp;', $middle); ?>" href="<?php echo str_replace('&', '&amp;', $popup); ?>" class="colorbox" rel="gallery" id="thumb_link-1">
                            <?php $alt = str_replace('"', '', $heading_title); ?>
                            <img itemprop="image" src="<?php echo $middle; ?>" title="<?php echo $alt ?>" alt="<?php echo $alt ?>" id="bigpic" rel="-1" />
                        </a>
                    </div>
                <?php } ?>
            </div>

            <div class="product_info">
                <div class="product_status<?php if ($stock_status=='Есть в наличии') {?> in_stock<?php } else if ($stock_status=='Нет в наличии') { ?> out_of_stock<?php } ?>"><?php echo $stock_status; ?></div>
                <div class="price_block">

                    <span class="price"><?php
					//if ($stock_status=='Есть в наличии') {

					echo $price;

                        //}
					?>

                        <?php if ($stock_status=='Есть в наличии') { ?>
                        <?php
                        $brand = '';

                        ?>
                        <?php
                          foreach ($params as $group_name => $group) {
                            foreach ($group as $param) {
                                if ($param['name'] == 'Бренд') {
                                    $brand = str_replace(',', '</span><span>', $param['value']);
                                }
                            }
                          }
                        ?>
                        <script type="application/ld+json">
                        {
                            "@context": "https://schema.org/",
                            "@type": "Product",
                            "name": "<?php echo $heading_title; ?>",
                            "image": [
                                "<?php echo $middle; ?>"
                            ],
                            "description": "<?php echo htmlentities($description); ?>",
                            "brand": {
                                "@type": "Brand",
                                "name": "<?php echo $brand; ?>"
                            },
                            "offers": {
                                "@type": "Offer",
                                "url": "<?php echo $action ?>",
                                "priceCurrency": "UAH",
                                "price": <?php echo str_replace(' ','',$price_dynx);?>,
                                "availability": "https://schema.org/InStock; ?>",
                                "seller": {
                                    "@type": "Organization",
                                    "name": "gra.ua"
                                }
                            }
                        }
                        </script>
                        <?php } else { ?>
                        <script type="application/ld+json">
                        {
                            "@context": "https://schema.org/",
                            "@type": "Product",
                            "name": "<?php echo htmlentities(strip_tags($heading_title),ENT_QUOTES, 'UTF-8'); ?>",
                            "image": [
                                "<?php echo $middle; ?>"
                            ],
                            "description": "<?php echo htmlentities(strip_tags($description),ENT_QUOTES, 'UTF-8'); ?>",
                            "brand": {
                                "@type": "Brand",
                                "name": "<?php echo $brand; ?>"
                            },
                            "offers": {
                                "@type": "Offer",
                                "url": "<?php echo $action ?>",
                                "priceCurrency": "UAH",
                                "price": <?php echo str_replace(' ','',$price_dynx);?>,
                                "availability": "https://schema.org/OutStock; ?>",
                                "seller": {
                                    "@type": "Organization",
                                    "name": "gra.ua"
                                }
                            }
                        }
                        </script>
                        <?php } ?>
                    </span>
                    <?php if ($old_price && ($stock_status=='Есть в наличии')) { ?>
                        <span class="old_price"><?php echo $old_price; ?></span>
                    <?php } ?>
					<script type="text/javascript">
var goal1Params = {order_price: '<?php $price_dynx = str_replace('<smal> грн</smal>', '', $price); echo str_replace(' ', '', $price_dynx); ?>'};

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}

<?php $source = $_GET['utm_content'];
if($source == 'recommendz_productfootertabs') { ?>
if(getCookie('<?php echo $product_id; ?>') != '1')
{
	setCookie('<?php echo $product_id; ?>', '1', 15);
}
<?php }?>

function a8bki392ijbijergiji() {
	try {
		if(getCookie('<?php echo $product_id; ?>') == '1')
		{
			yaCounter15958822.reachGoal('goal1', goal1Params);
		}
	}
	catch(e) { }
	return true;
}
					</script>
                    <?php if ($stock_status=='Есть в наличии') { ?> <a class="button_green remarketing-id" data-product_id="<?php echo $product_id;?>" href="<?php echo $action ?>" id="add_to_cart" onclick="a8bki392ijbijergiji();return true;"><span><b class="cart_bg"></b></span></a> <?php } ?>
                </div>
                <div class="params">
                    <table class="table_box">
                        <?php $key = 1 ?>
                        <?php if ($warranty) { ?>
                            <tr <?php if ($key % 2) echo 'class="odd"' ?> >
                                <td class="first"><?php echo $text_warranty ?>:</td>
                                <td><?php echo $warranty ?></td>
                            </tr>
                            <?php $key++; ?>
                        <?php } ?>
                        <?php if ($model) { ?>
                            <tr class="model <?php if ($key % 2) echo ' odd' ?>">
                                <td class="first"><?php echo $text_model ?>:</td>
                                <td><strong itemprop="sku"><?php echo $model ?></strong></td>
                            </tr>
                            <?php $key++;?>
                        <?php } ?>
                    </table>
                    <?php if ($params) { ?>
                        <?php foreach ($params as $group_name => $group) { ?>
                            <?php if ($group_name) { ?>
                                <div class="group_name"><?php echo $group_name ?></div>
                            <?php } ?>
                            <table class="table_box">
                                <?php $key = 1 ?>
                                <?php foreach ($group as $param) { ?>
                                    <tr <?php if ($key % 2) echo 'class="odd"' ?>>
									<?php //if($param['name'] == 'Брэнд') $brand = str_replace(',', '</span><span>', $param['value']);?>
                                        <?php if ($param['description'] != '') { ?>
                                            <td class="first desc">
                                                <span class="name"><?php echo $param['name'] ?>:</span>
                                                <span class="drop_row">
                                                    <div class="drop"><?php echo html_entity_decode($param['description']) ?></div>
                                                </span>
                                            </td>
                                        <?php } else { ?>
                                            <td class="first"><?php echo $param['name'] ?>:</td>
                                        <?php } ?>
                                        <td class="second">
										<?php if ($param['name'] == 'Бренд') { ?>
										<input type="hidden" class="brand-e" value="<?php echo str_replace(',', '</span><span>', $param['value']) ?></span>" />
										<?php } ?>
										<strong><span><?php echo str_replace(',', '</span><span>', $param['value']) ?></span></strong></td>
                                    </tr>
                                    <?php $key++;?>
                                <?php } ?>
                            </table>
                        <?php } ?>
                    <?php } ?>
                </div>

            </div>
            <div id="tabs" class="htabs">
                <a tab="#tab_description" class="link"><span>Описание</span></a>
                <a tab="#tab_review" class="link" onclick="$('#review').load('<?php echo HTTP_SERVER?>'+'index.php?route=product/product/review&product_id=<?php echo $product_id; ?>');"><span> <?php echo $tab_review; ?></span></a>
                <?php if ($video) { ?>
                    <a tab="#tab_video" class="link"><span>Видео</span></a>
                <?php } ?>

                <!-- Social links-->
                <div class="social">


                </div>
            </div>
            <div class="tab_box">
                <?php if ($description) { ?>
                    <div id="tab_description">
                        <div class="tab_description" itemprop="description">
                            <?php echo $description; ?>
							    <?php #if ($seo_text) {
								?>
									<div class="text">
							<!-- 102okWC021kmxEQWFkv0Cwo032p0CWlx10wxloEF020Cp3p231qPBmxsxQEWd -->

							<!-- /102okWC021kmxEQWFkv0Cwo032p0CWlx10wxloEF020Cp3p231qPBmxsxQEWd -->
									<?php
									//echo '<div class="seoshield_content"></div>';
									echo $seo_text;
									?></div>
								<?php #} ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($review_status) { ?>
                    <div id="tab_review" class="review_box">
                        <div id="review"></div>
                    </div>
                <?php } ?>
                <?php if ($video) { ?>
                    <div id="tab_video">
                        <?php echo $video; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div  class="desc_block">
        <?php if(!$_SESSION['widget_switcher']): ?>

            <?php //if ($quote) { ?>
                <!--<blockquote class="quote"><?php// echo $quote; ?></blockquote>-->
            <?php //} ?>



<div id="tabs_products" class="htabs">

<?php /*


<?php if ($recommendz_products) { ?>
	<a tab="#tab_recommendz_products" class="link"><span>Рекомендуемые для вас товары</span></a>
<?php } ?>
<div class="tab_box tab_products">
	<?php if ($recommendz_products) { ?>
	<!--<div id="tab_recommendz">
		<?php echo $recommendz_products; ?>
	</div>-->
	<div id="tab_recommendz">
		<?php $products = $recommendz_products; ?>
		<?php $id = 'recommendz_products'; ?>
		<?php $text = 'Рекомендуемые для вас товары'; ?>
		<?php include("catalog/view/theme/default/template/common/_carusel_list.tpl"); ?>
	</div>
	<?php } else { ?>
	рекомендации
	<?php } ?>
</div>

*/ ?>

</div>

<?php //echo 'category: ' . $category_id;?>

        <div id="tabs_products" class="htabs">
			<?php if ($recommendz_products) { ?>
				<a tab="#tab_recommendz" class="link"><span>Рекомендуемые для вас товары</span></a>
			<?php } ?>
            <?php

            //gra.ua/catalog/controller/module/viewed.php
            //template/module/viewed.tpl
            //catalog/view/theme/default/template/common/_carusel_list.tpl
            if ($viewed) { ?>
                <a tab="#tab_viewed" class="link"><span>Просмотренные товары</span></a>
            <?php } ?>
            <?php //if ($similar_products) { ?>
                <a tab="#tab_similar_products" class="link" onclick="$('#tab_similarz').load('<?php echo HTTP_SERVER?>'+'index.php?route=product/product/similarz&product_id=<?php echo $product_id; ?>&price=<?php echo $raw_price;?>&category_id=<?php echo $category_id;?>');"><span><?php echo $text_similar ?></span></a>
            <?php //} ?>
            <?php if ($similar_price_products) { ?>
                <a tab="#tab_similar_price_products" class="link"><span><?php echo $text_similar_price ?></span></a>
            <?php } ?>
        </div>
        <div class="tab_box tab_products">
			<?php if ($recommendz_products) { ?>
			<!--<div id="tab_recommendz">
				<?php echo $recommendz_products; ?>
			</div>-->
			<div id="tab_recommendz">
				<?php $products = $recommendz_products; ?>
				<?php $id = 'recommendz_products'; ?>
				<?php $text = 'Рекомендуемые для вас товары'; ?>
				<?php $source = 'recommendz_productfootertabs'; ?>
				<?php include("catalog/view/theme/default/template/common/_carusel_list.tpl"); ?>
			</div>
			<?php } ?>
            <?php if ($viewed) { ?>
                <div id="tab_viewed">
                    <?php echo $viewed; ?>
                </div>
            <?php } ?>
            <div id="tab_similarz">

			</div>
        </div>
        <?php else: ?>

        <?php endif; ?>
    </div>
</div>
<script type="text/javascript"><!--
    $(".image_block a.colorbox").colorbox({related:true});
    //--></script>

<script type="text/javascript"><!--
    $.tabs('#tabs a.link');
    $.tabs('#tabs_products a.link');

    $('.thumbs a.image').mouseover(function(){

        changeBigImage(this);
    });

    function changeBigImage(el) {
        var picture = $('#bigpic');

        picture
        .attr('rel', $(el).attr('id').substr(10))
        .stop()
        .fadeTo(100,0, function(){
            $(this).attr('src', $(el).attr('value')).fadeTo(100,1);
        });
    }

    $('#bigpic').click(function() {
        $('#thumb_link' + this.getAttribute('rel')).click();
    });
    //--></script>
<script type="text/javascript"><!--
    $('#review .pagination a').live('click', function() {
        $('#review').slideUp('slow');

        $('#review').load(this.href);

        $('#review').slideDown('slow');

        return false;
    });

    /*$('#review').load('<?php echo HTTP_SERVER?>'+'index.php?route=product/product/review&product_id=<?php echo $product_id; ?>');*/
	/*$('#review').html('<?php //echo trim(file_get_contents(HTTP_SERVER . 'index.php?route=product/product/review&product_id=' . "$product_id"));
	?>');*/

    function review() {
        $.ajax({
            type: 'POST',
            url: '<?php echo HTTP_SERVER ?>'+'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
            dataType: 'json',
            data: 'name=' + encodeURIComponent($('input[name=\'name\']').val()) + '&text=' + encodeURIComponent($('textarea[name=\'text\']').val()) + '&rating=' + encodeURIComponent($('input[name=\'rating\']:checked').val() ? $('input[name=\'rating\']:checked').val() : '') + '&captcha=' + encodeURIComponent($('input[name=\'captcha\']').val()),
            beforeSend: function() {
                $('.success, .warning').remove();
                $('#review_button').attr('disabled', 'disabled');
                $('#review_title').after('<div class="wait"><img src="<?php echo HTTP_SERVER ?>catalog/view/theme/default/image/loading_1.gif" alt="" /> <?php echo $text_wait; ?></div>');
            },
            complete: function() {
                $('#review_button').attr('disabled', '');
                $('.wait').remove();
            },
            success: function(data) {
                if (data.error) {
                    $('#review_title').after('<div class="warning">' + data.error + '</div>');
                }

                if (data.success) {
                    $('#review_title').after('<div class="success">' + data.success + '</div>');

                    $('input[name=\'name\']').val('');
                    $('textarea[name=\'text\']').val('');
                    $('input[name=\'rating\']:checked').attr('checked', '');
                    $('input[name=\'captcha\']').val('');
                }
            }
        });
    }
    //--></script>
<script type="text/javascript"><!--
    $(function() {
        $("#thumbs_carousel").jCarouselLite({
            btnNext: ".thumbs_next",
            btnPrev: ".thumbs_prev",
            vertical: false,
            circular: false,
            visible: 3,
            scroll: 1
        });

    });
    //--></script>

<?php if(isset($keywords)){?>
<!-- Google Code for &#1058;&#1077;&#1075; &#1088;&#1077;&#1084;&#1072;&#1088;&#1082;&#1077;&#1090;&#1080;&#1085;&#1075;&#1072; -->
<!-- Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. For instructions on adding this tag and more information on the above requirements, read the setup guide: google.com/ads/remarketingsetup -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 999045519;
var google_conversion_label = "UZB-COG2kAQQj_Ow3AM";
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/999045519/?value=0&amp;label=UZB-COG2kAQQj_Ow3AM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
<?php }?>

<!-- Код тега ремаркетинга Google -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 996105238;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/996105238/?value=0&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<!-- TrafMag -->
<script async type='text/javascript' src='http://t.trafmag.com/tracking.js'></script>

<?php if(isset($ecommerce) && $ecommerce) echo $ecommerce;?>


<?php echo $footer; ?>
