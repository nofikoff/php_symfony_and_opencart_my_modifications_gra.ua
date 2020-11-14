
<div class=" order_page" >
    <div class="order_contact">
        <p class="info">Оставьте свои контакты и мы Вам перезвоним</p>
        <form action="<?php echo $action; ?>" class="form" method="post" enctype="multipart/form-data" id="callback_form">
            <div class="order_info">
                <label>Контактный телефон: <b>*</b></label>
                <?php if ($error_phone) { ?><span class="error"><?php echo $error_phone; ?></span><?php } ?>
                <input type="text" required="required" class="placeholder" name="phone" alt="<?php echo $phone_standart?>" value="<?php echo $phone; ?>"/>
            </div>
            <div class="order_info">
                <label>Ваше имя: <b>*</b></label>
                <?php if ($error_name) { ?><span class="error"><?php echo $error_name; ?></span><?php } ?>
                <input type="text" required="required" name="name" value="<?php echo $name; ?>"/>
            </div>
            <div class="order_info ">
                <label>Вопрос или комментарий:</label>
                <textarea name="enquiry" id="enquiry" rows="10"><?php echo $enquiry; ?></textarea>
            </div>
        </form>
        <div class="buttons">
            <a id="callback_submit" class="button_red">Заказать звонок</a>
        </div>
    </div>
</div>

<script type="text/javascript">
//    _gaq.push(['_trackPageview','/callback-open']);  
    
    $(document).ready(function () {
        $('#callback_submit').click(function () {
            
//            _gaq.push(['_trackPageview','/callback-submit']); 
            
            $.ajax({
                type: 'POST',
                url: '<?php echo $action ?>',
                dataType: 'html',
                data: $('#callback_form :input, #callback_form :textarea'),
                beforeSend: function (){
                    $('#cboxLoadedContent').html('<div class="loader_image"></div>');
                },
                success: function (html) {
                    $('#cboxLoadedContent').html(html);
                    $.fn.colorbox.resize();
                }
            });
        });
    });
</script>