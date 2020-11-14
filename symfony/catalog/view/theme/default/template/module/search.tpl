<link type="text/css" rel="stylesheet" href="/catalog/view/theme/default/stylesheet/jquery.slider.css" />
<div class="filter filter_price" <?php if($min_budget==0 && $max_budget==0) { echo ' style="display:none;" ';}?>>
               <span class="param_name">Ваш бюджет:</span>
               <a class="filter_button" onclick="filterByPrice()" title="фильтровать по цене"></a>
               <div class="slider">
                   <input id="slider_price_range" type="slider" name="price_range" value="<?php echo $budget_from ? : $min_budget ?>;<?php echo $budget_to ? : $max_budget ?>" />
                </div>
               
        </div>

<script type="text/javascript">
  $('#slider_price_range').slider({
    from: <?php echo $min_budget ?>,
    to: <?php echo $max_budget ?>,
    smooth: true,
    round: 0,
    dimension: "&nbsp;грн."
  });
  
  function filterByPrice() {
      
        var url = location.protocol + "//" + location.host + '/';
        
        if(url == location.href){
           url += 'index.php?route=product/search'; 
        } else {
            url = location.href;
        }
                 
      $.ajax({
            type: 'post',
            url: '<?php echo HTTP_SERVER . "index.php?route=module/search/set_price" ?>',
            data: {price_from: $('#price_from').val().replace(' ', ''),
                   price_to: $('#price_to').val().replace(' ', '')},
            success: function () {
                   location = url;
            }
        });
  };
  
  function setPrice(){
      
  }
  
$('#price_from').change(function(){
    val = parseInt($('#price_from').val());
    if(isNaN(val) || val < <?php echo $min_budget?>)
        $('#price_from').val('<?php echo $min_budget?>');
    else
        $('#price_from').val(val);
});
$('#price_to').change(function(){
    val = parseInt($('#price_to').val());
    if(isNaN(val) || val > <?php echo $max_budget?>)
        $('#price_to').val('<?php echo $max_budget?>');
    else
        $('#price_to').val(val);
});


</script>