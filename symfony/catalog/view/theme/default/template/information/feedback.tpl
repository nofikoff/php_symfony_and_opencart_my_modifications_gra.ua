
<div class="order_page">
    <div class="order_contact">
      <p class="info"><?php echo $heading_title; ?></p>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="feedback_form">
            <div class="order_info">
                    <label><?php echo $entry_name; ?></label>
                    <input type="text" name="name" value="<?php echo $name; ?>"/>
                </div>
                <div class="order_info">
                    <label><?php echo $entry_address_contact; ?></label>
                    <input type="text" name="address" value="<?php echo $address; ?>"/>
                </div>
                <div class="order_info">
                    <?php if ($error_enquiry) { ?><span class="error"><?php echo $error_enquiry; ?></span><?php } ?>
                  <label><?php echo $entry_text; ?> <b>*</b></label>
                  <span style="font-size: 0.8em; color: #777">Изложите, пожалуйста, Ваш вопрос. Желательно указать номер заказа и имя менеджера, для более детального понимания ситуации. </span>
                  <textarea name="enquiry" id="enquiry" required="required" cols="50" rows="10"><?php echo $enquiry; ?></textarea>
                </div>
             </form>
            <div class="buttons">
                <a id="feedback_submit" class="button_request"></a>
            </div>
        </div>
    </div> 
<script type="text/javascript">
//    _gaq.push(['_trackPageview','/feedback-open']);  
    
    $(document).ready(function () {
        $('#feedback_submit').click(function () {
            
//            _gaq.push(['_trackPageview','/feedback-submit']); 
            
            $.ajax({
                type: 'POST',
                url: '<?php echo $action ?>',
                dataType: 'html',
                data: $('#feedback_form :input, #feedback_form :textarea'),
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