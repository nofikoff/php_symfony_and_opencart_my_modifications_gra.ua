<?php echo $header; ?>

<script>
    jQuery(document).ready(function($){
        $('.symple_order ').parent().prev().find('#nav__dt table').hide();
    });
</script>
<div class="symple_order order_page ">
    <h1 class="heading"><?php echo $heading_title; ?></h1>
    <a class="cart_back read_more" href="<?php echo $cart_back; ?>">Продолжить покупки</a>
    <div class="delivery_link">
        <a onclick="textMsg()" class="read_more">Подробно об оплате и доставке</a>
        <div id="msg">
            <div id="text">
                <?php echo $payment_info; ?>
            </div>
            <div id="close"><a  class="read_more" href="javascript:closeMsg()">Закрыть</a></div>
        </div>
    </div>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="cart">
        <input type="hidden" name="remove" value="" id="remove" />
        <div class="cart_products">
            <div class="cart_product_item">
                <?php $class = 'odd'; ?>
                <?php $i_custom = 0;
						foreach ($products as $key => $product) {
                $i_custom = $i_custom + $product['quantity'];?>
                <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                <div class="<?php echo $class; ?> row" >
                    <a onclick="sendCommerce('<?php echo $product['product_id']; ?>');setTimeout(function(){ $('#remove').val('<?php echo $product['key']; ?>'); $('#cart').submit()},1000);" class="delete read_more"></a>
                    <div class="price" id="product_total_<?php echo $product['key']; ?>"><?php echo $product['total']; ?></div>
                    <div class="qtt">
                        <a class="qtt_change button_minus" onclick="$('#qtt<?php echo $key ?>').val(<?php echo ($product['quantity']-1) ?>); $('#cart').submit()">-</a>
                        <input type="text" id="qtt<?php echo $key ?>" class="quantity-lost-element" name="quantity[<?php echo $product['key']; ?>]" value="<?php echo $product['quantity']; ?>" size="2" />
                        <a class="qtt_change button_plus" onclick="$('#qtt<?php echo $key ?>').val(<?php echo ($product['quantity']+1) ?>); $('#cart').submit()">+</a>
                    </div>
                    <a  class="image" href="<?php echo str_replace('&', '&amp;', $product['href']); ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>
                    <a class="product_name" href="<?php echo str_replace('&', '&amp;', $product['href']); ?>"><?php echo $product['name']; ?></a>
                </div>
                <?php } ?>
            </div>
            <div class="cart_totals">
                <div class="order_info">
                    <?php if ($error_product) { ?>
                    <span class="error"><?php echo $error_product; ?></span>
                    <?php } ?>
                    <?php  foreach ($totals as $total) { ?>
                    <br/>
                    <z style="text-align: left; font-size: 1.6em; float: left;"><?php echo $i_custom; ?> товаров </z>
                    <z style="text-align: right;
    font-size: 1.6em;
    float: right;
    margin-bottom: 20px;"><?php echo $total['title']; ?> &nbsp;<span style="color: #be0b07;"> <?php echo $total['text']; ?></span></z>
                    <?php } ?>
                </div>
            </div>
        </div>
    </form>
	<script>
		function sendCommerce(product_id) {
	
		if(product_id) { 
		  $.ajax({ 
              type: 'post',
              url:  '/index.php?route=product/product/shortProduct',
			  data: 'product_id='+product_id,
              dataType: 'json',
              success: function(json) {
			  if (json['success']){
			  special = parseFloat(json['product']['special']).toFixed(2); 
			  price = parseFloat(json['product']['price']).toFixed(2);
			  if(!isNaN(special) && special > 0) price = special;
			var  heading = $('h1').text();
			window.dataLayer = window.dataLayer || [];
			dataLayer.push({
						'ecommerce': { 
						'currencyCode': 'UAH',
						'remove': {                                 
							'products': [{                       
							'name': json['product']['name'],
							'id': json['product']['model'],
						//	'brand': json['product']['manufacturer'],
							'price': price,
						//	'category': heading
							}]
    }},
		'event': 'gtm-ee-event',
		'gtm-ee-event-category': 'Enhanced Ecommerce',
		'gtm-ee-event-action': 'Removing a Product from a Shopping Cart',
		'gtm-ee-event-non-interaction': 'False'
	});		 
}}})}};
		</script>
    <div class="order_contact">
        <form id="client_form" class="form" action="<?php echo $action ?>" method="post" alt="catalog/view/theme/default/template/checkout/symple_order.tpl">
            <p class="info">Заполните, пожалуйста, все поля, отмеченные <b>*</b>.</p>
            <div class="order_info order-info-top">
                <label>Фамилия Имя Отчество<b>*</b> <br>(укажите в том же порядке для Новой Почты)</label>
                <?php if ($error_name) { ?>
                <span class="error"><?php echo $error_name; ?></span>
                <?php } ?>
                <input type="text" name="name" required="required" value="<?php echo $name; ?>"/>
            </div>
            <div class="order_info order-info-top">
                <label>Номер телефона  <b>*</b> (в формате : 0671234567)</label>
                <?php if ($error_phone) { ?>
                <span class="error"><?php echo $error_phone; ?></span>
                <?php } ?>
                <?php
                        // смотри обработчик в catalog/controller/checkout/symple_order.php
                        //$phone = trim($phone, '+ ');
                        $phone = str_replace('+38','', $phone);

?>


                <input type="number" name="phone" value="<?=$phone?>"  placeholder="Только цифры" required="required"  pattern=".{10,}" id="phonenumber"/>
            </div>
            <div class="item__wrap">            
                <div class="order_info item1 itm order-info-bottom">
                    <?php if ($easy_novaposhta_status) { ?>
                    
                    <label for="easy_nova_poshta_type">Способ доставки: <b>*</b></label>
                    <?php if ($error_easy_nova_poshta_type) { ?>
                    <span class="error"><?php echo $error_easy_nova_poshta_type; ?></span>
                    <?php } ?>
                    <select id="easy-nova-poshta-type" name="easy_nova_poshta_type">
                        <option value="" disabled selected> --- </option>                        
                        <option value="office" <?= ($error_easy_nova_poshta_office)?'selected':'' ?>>- отделение Новой Почты</option>
                        <option value="self" <?= ($error_easy_nova_poshta_address || $error_easy_nova_poshta_buildingappartment)?'selected':'' ?>>- самовывоз из магазина</option>
                        <option value="address" <?= ($error_easy_nova_poshta_address || $error_easy_nova_poshta_buildingappartment)?'selected':'' ?>>- курьером по Киеву</option>
                        <?php /*<option value="address" <?= ($error_easy_nova_poshta_address || $error_easy_nova_poshta_buildingappartment)?'selected':'' ?>>- адресная Новой Почтой</option><?php */ ?>
                    </select><br><br>
                    
                    <div id="easy-nova-poshta-self-block" style="display: none;">
                        <p>
                            г. Киев, ул. Михаила Бойчука <br>
                            (Киквидзе), 26, вход со двора <br>
                            2-й подъезд, домофон 77в
                        </p>
                    </div>
                    
                    <div id="easy-nova-poshta-region-block" style="display: <?= ($error_easy_nova_poshta_region)?'block':'none' ?>;">
                        <label for="easy_nova_poshta_region">Область <b>*</b></label>
                        <?php if ($error_easy_nova_poshta_region) { ?>
                        <span class="error"><?php echo $error_easy_nova_poshta_region; ?></span>
                        <?php } ?>
                        <select id="easy-nova-poshta-region" name="easy_nova_poshta_region">
                            <option value="" disabled selected> --- Выберите --- </option>
                        </select>
                    </div>

                    <div id="easy-nova-poshta-city-block" style="display: <?= ($error_easy_nova_poshta_city)?'block':'none' ?>;">
                        <label for="easy_nova_poshta_city">Город <b>*</b></label>
                        <?php if ($error_easy_nova_poshta_city) { ?>
                        <span class="error"><?php echo $error_easy_nova_poshta_city; ?></span>
                        <?php } ?>
                        <select id="easy-nova-poshta-city" name="easy_nova_poshta_city">
                            <option value="" disabled selected> --- Выберите --- </option>
                        </select>
                    </div>
                    
                    <div id="easy-nova-poshta-address-block" style="display: <?= ($error_easy_nova_poshta_address || $error_easy_nova_poshta_buildingappartment)?'block':'none' ?>;">
                        <label for="easy_nova_poshta_street">Улица <b>*</b></label>
                        <?php if ($error_easy_nova_poshta_address) { ?>
                        <span class="error"><?php echo $error_easy_nova_poshta_address; ?></span>
                        <?php } ?>
                        <input id="easy-nova-poshta-street" type="text" name="easy_nova_poshta_street" value="" placeholder="Введите улицу" required="required"><br><br>
                        
                        <label for="easy_nova_poshta_buildingappartment">Дом, квартира, этаж <b>*</b></label>
                        <?php if ($error_easy_nova_poshta_buildingappartment) { ?>
                        <span class="error"><?php echo $error_easy_nova_poshta_buildingappartment; ?></span>
                        <?php } ?>
                        <input id="easy-nova-poshta-buildingappartmen" type="text" name="easy_nova_poshta_buildingappartment" value="" placeholder="Введите адрес" required="required"><br><br>
                    </div>
                    
                    <div id="easy-nova-poshta-office-block" style="display: <?= ($error_easy_nova_poshta_office)?'block':'none' ?>;">
                        <label for="easy_nova_poshta_office">Отделение Новой почты <b>*</b></label>
                        <?php if ($error_easy_nova_poshta_office) { ?>
                        <span class="error"><?php echo $error_easy_nova_poshta_office; ?></span>
                        <?php } ?>
                        <select id="easy-nova-poshta-office" name="easy_nova_poshta_office">
                            <option value="" disabled selected> --- Выберите --- </option>
                        </select>
                    </div>
                    
                    <input id="easy-nova-poshta-city-hef" type="hidden" name="easy_nova_poshta_city_hef" value="">                    
                    <?php } else { ?>
                    <label><?php echo $text_info_delivery; ?>:
                    </label>
                    <?php if ($error_address) { ?>
                    <span class="error"><?php echo $error_address; ?></span>
                    <?php } ?>
                    <textarea class="addr" name="address" cols="50" rows="3"><?php echo $address; ?></textarea>
                    <?php } ?>                    
                </div>

                <div class="order_info item2 itm comment-order-block">
                    <label><?php echo $text_info_add; ?> </label>
                    <?php if ($error_comments) { ?>
                    <span class="error"><?php echo $error_comments; ?></span>
                    <?php } ?>
                    <textarea name="comments" cols="50" rows="3"><?php echo $comments; ?></textarea>
                </div>
            </div>

            <!--input type="checkbox" name="dontcall" value="1">Не перезванивать для подтверждения заказа</input-->





            <!--
                   <div class="order_info">
                        <label><?php echo $text_city; ?>: <b>*</b></label>
                        <?php if ($error_city) { ?>
                            <span class="error"><?php echo $error_city ?></span>
                        <?php } ?>
                            <input type="hidden" name="city_id" id="city_id" value="<?php if ($city_id){echo $city_id;} else {echo 908;} ?>" />
                        <input type="text" name="city_name" id="city_name" autocomplete="off" required="required" value="<?php echo $city_name; ?>"/>
                        <div id="city_suggest"></div>
                    </div>

<link rel="stylesheet" href="/np/js/colorbox.css"/>
<link rel="stylesheet" href="/np/js/button.css"/>
<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css"/>
<script type="text/javascript" src="/np/js/jquery.colorbox-min.js"></script>

<div class="order_info">
                   <label>Для доставки по Киеву, это поле не заполняйте<br>Выбор склада Новой почты (если Вы не из Киева): <b>*</b></label>
<input type="hidden" name="city_id" id="city_id" value="<?php if ($city_id){echo $city_id;} else {echo 908;} ?>" />
<input class="npochta"  name="city_name" id="city_name" type="text" size="50" value=""/>

<input type='button' class='button_np' title='Нажмите на логотип НОВОЙ ПОЧТЫ - можно выбрать склад прямо на карте' value='' href='/np/index.php' />
<script type="text/javascript" src="/np/script.js"></script>
</div>
-->
<div class="order_info">
<h4>Виды оплаты:</h4>
- Оплата при получении<br>
- pos-терминал в магазине<br>
- Предоплата на карту ПриватБанка (реквизиты придут в смс)<br>
- Безналичный расчет ФОП Федянін О.О., ІПН 2968104617<br>
  UA023007110000026005060105183 МФО 300711
</div>

            <!--<div class="more_info">
                <a href="javascript:;" alt="Скрыть дополнительную информацию">Указать дополнительную информацию</a>
            </div>
            <div id="more_info">


            </div>
            -->

            <input type="hidden" name="city_id" id="city_id" value="<?php if ($city_id){echo $city_id;} else {echo 908;} ?>" />
        </form>
        
        
        
        
        <div class="buttons">
            <a class="button_red" onclick="$('#client_form').submit(); $(this).hide();" ><span><?php echo $text_confirm; ?></span></a>
        </div>
    </div>

</div>
<style type="text/css">
    form#cart {
        float: right;
    }
    form#cart .cart_products {
        width: 100% !important;
    }
    .order_page > form {
        float: right;
    }
    .symple_order .order_contact {
      margin-left: 0%;
      width: 50%;
    }
    .order-info-top input{
        width: 96% !important;
    }
    .order-info-bottom input {
      width: 90% !important;
    }    
    .comment-order-block {
        width: 50%;
        float: left;
    }
</style>
<script type="text/javascript">
    <?php
        $items = array();
    foreach($products as $pr){
        $price = floatval(strip_tags(preg_replace('/\s+/', '', $pr["price"])));
        $items[] = array(
            "Id" => $pr["key"],
            "Count" => $pr["quantity"],
            "Price" => $price
    );
    }
        ?>
    var items = <?php echo json_encode($items); ?>;
    $(".order_contact .button_red").click(function(event){

        /*$phone = $('#phonenumber'); if ($phone.val().length && $phone.search('+38') === -1) { $phone.val() = '+38' + $phone.val(); }*/

        $('#client_form').submit();
        $(".order_contact .button_red").hide();
    });


    /*
    $('#phonenumber').focus(function() {
        if (this.value != '')
            this.select();
        else
            this.value = '+380';
    }).blur(function() {
         (this.value == '+380')
            this.value = '';
        if (this.value.length)
            this.value = this.value.replace(/[^\d\+]/g, '');
    });
    */



    function increaseQuantity(qtt, id) {
        qtt.val(+qtt.val() + 1);
        $('#product_total_'+id).html(($('#product_price_'+id).val() * qtt.val()) + ' грн');

    }
    function decreaseQuantity(qtt, id) {
        qtt.val(+qtt.val() - 1);
        $('#product_total_'+id).html(($('#product_price_'+id).val() * qtt.val()) + ' грн');
    }

</script>

<script type="text/javascript">
    function textMsg(msg) {
        $('#msg').css('display', 'block');
    }
    function closeMsg() {
        $('#msg').css('display', 'none');
    }
    $('.more_info a').click(function moreInfo(){
        var alt=$(this).attr('alt');
        $('#more_info').toggleClass('visible');
        $(this).attr('alt', $(this).html());
        $(this).html(alt);
    })
</script>

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

<!-- автосохранение формы -->
<script type="text/javascript">
    $( function() { $( "#client_form" ).sisyphus(); } );
</script>

<?php echo $footer; ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript">
    
    $( document ).ready(function() {
    
        $('select#easy-nova-poshta-type').change(function() {
            $('select#easy-nova-poshta-city').empty();
            $('select#easy-nova-poshta-city').append('<option value="" disabled selected> --- Не выбрана область --- </option>');
            $('select#easy-nova-poshta-office').empty();
            $('select#easy-nova-poshta-office').append('<option value="" disabled selected> --- Не выбран город --- </option>');
            $('input#easy-nova-poshta-street').val('');
            $('input#easy-nova-poshta-buildingappartmen').val('');
        
            if ($(this).val() == 'address') {
                $('div#easy-nova-poshta-city-block').css('display','block');
                $('div#easy-nova-poshta-address-block').css('display','block');
                $('div#easy-nova-poshta-office-block').css('display','none');
                $('div#easy-nova-poshta-self-block').css('display','none');
				$('div#easy-nova-poshta-region-block').css('display','none');
                
                if ($('select#easy-nova-poshta-type option:selected').text() == '- курьером по Киеву') {
					//$('div#easy-nova-poshta-city-block').css('display','none');
					$('select#easy-nova-poshta-city').empty();
					$('select#easy-nova-poshta-city').append('<option value="e718a680-4b33-11e4-ab6d-005056801329" selected>Киев</option>');
                    $('select#easy-nova-poshta-city').val('e718a680-4b33-11e4-ab6d-005056801329');
                    $('input#easy-nova-poshta-city-hef').val('e718a680_4b33_11e4_ab6d_005056801329');
                }
                
            } else if ($(this).val() == 'office') {
                    $('select#easy-nova-poshta-region').empty();
                    $.ajax({
                        url: "/index.php?route=api/easy_novaposhta/regions",
                        type: "POST",
                        data: {region_ref: 1},
                        success: function (data) {
                            var regions = JSON.parse(data);
                            if (regions.length > 0) {
								$('select#easy-nova-poshta-region').append('<option value="" disabled selected> --- Выберите --- </option>');
                                for (i=0;i<regions.length;i++) {
                                    $('select#easy-nova-poshta-region').append('<option value="'+regions[i].id+'">'+regions[i].value+'</option>');
                                }
                            }else{
								$('select#easy-nova-poshta-region').append('<option value="" disabled selected> --- Нет данных --- </option>');
							}
                        }
                    });
				$('div#easy-nova-poshta-region-block').css('display','block');
				$('div#easy-nova-poshta-city-block').css('display','block');
                $('div#easy-nova-poshta-office-block').css('display','block');
                $('div#easy-nova-poshta-address-block').css('display','none');
                $('div#easy-nova-poshta-self-block').css('display','none');                               
            } else if ($(this).val() == 'self') {
                $('div#easy-nova-poshta-self-block').css('display','block');
                $('div#easy-nova-poshta-office-block').css('display','none');
                $('div#easy-nova-poshta-address-block').css('display','none');
                $('div#easy-nova-poshta-city-block').css('display','none');
				$('div#easy-nova-poshta-region-block').css('display','none');
            }
        });

        $('select#easy-nova-poshta-region').change(function() {
                    $('select#easy-nova-poshta-city').empty();
					$('input#easy-nova-poshta-city-hef').val('');
					$('select#easy-nova-poshta-office').empty();
					$('select#easy-nova-poshta-office').append('<option value="" disabled selected> --- Не выбран город --- </option>');
                    $.ajax({
                        url: "/index.php?route=api/easy_novaposhta/cities2",
                        type: "POST",
                        data: {region_ref: $('select#easy-nova-poshta-region').val().split('-').join('_')},
                        success: function (data) {
                            var cities = JSON.parse(data);
                            if (cities.length > 0) {
								$('select#easy-nova-poshta-city').append('<option value="" disabled selected> --- Выберите --- </option>');
                                for (i=0;i<cities.length;i++) {
                                    $('select#easy-nova-poshta-city').append('<option value="'+cities[i].id+'">'+cities[i].value+'</option>');
                                }
                            }else{
								$('select#easy-nova-poshta-city').append('<option value="" disabled selected> --- Нет данных --- </option>');
							}
                        }
                    });
		});
		

        $('select#easy-nova-poshta-city').change(function() {
                    $('select#easy-nova-poshta-office').empty();
					$('input#easy-nova-poshta-city-hef').val($('input#easy-nova-poshta-city').val());
                    $.ajax({
                        url: "/index.php?route=api/easy_novaposhta/offices",
                        type: "POST",
                        data: {city_ref: $('select#easy-nova-poshta-city').val().split('-').join('_')},
                        success: function (data) {
                            var offices = JSON.parse(data);
                            if (offices.length > 0) {
								$('select#easy-nova-poshta-office').append('<option value="" disabled selected> --- Выберите --- </option>');
                                for (i=0;i<offices.length;i++) {
                                    $('select#easy-nova-poshta-office').append('<option value="'+offices[i].id+'">'+offices[i].value+'</option>');
                                }
                            }else{
								$('select#easy-nova-poshta-office').append('<option value="" disabled selected> --- Нет данных --- </option>');
							}
                        }
                    });
		});

        $("input#easy-nova-poshta-street").autocomplete({
            source: function(request, response) {
                $.getJSON("/index.php?route=api/easy_novaposhta/adresses", { city_ref: $('input#easy-nova-poshta-city-hef').val(), term: $("input#easy-nova-poshta-street").val() }, response);
            },
            minLength: 2,
            select: function( event, ui ) {
                event.preventDefault();
                $("input#easy-nova-poshta-street").val(ui.item.value);
            }
        });
        
/*        $('select#easy-nova-poshta-office').click(function() {
            if (!$('select#easy-nova-poshta-city').val() || !$('input#easy-nova-poshta-city-hef').val()) {
                $('select#easy-nova-poshta-city').val('');
                $('select#easy-nova-poshta-city').css('border','1px solid #da3939');
                $('select#easy-nova-poshta-city').css('box-shadow','2px 0px 4px #da3939');
            }               
        });*/
        
        $(".quantity-lost-element").keyup(function() {        
            if (!isNaN($(this).val()))
                $(this).val(parseInt($(this).val()));
            else
                $(this).val(1);
        });
        
        $(".quantity-lost-element").focusout(function() {
            $('#cart').submit();
        });
    
    });
    
</script>