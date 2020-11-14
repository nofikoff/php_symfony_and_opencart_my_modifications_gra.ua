var _____WB$wombat$assign$function_____ = function(name) {return (self._wb_wombat && self._wb_wombat.local_init && self._wb_wombat.local_init(name)) || self[name]; };
if (!self.__WB_pmw) { self.__WB_pmw = function(obj) { this.__WB_source = obj; return this; } }
{
  let window = _____WB$wombat$assign$function_____("window");
  let self = _____WB$wombat$assign$function_____("self");
  let document = _____WB$wombat$assign$function_____("document");
  let location = _____WB$wombat$assign$function_____("location");
  let top = _____WB$wombat$assign$function_____("top");
  let parent = _____WB$wombat$assign$function_____("parent");
  let frames = _____WB$wombat$assign$function_____("frames");
  let opener = _____WB$wombat$assign$function_____("opener");
}
function is_touch_device() {
    return (('ontouchstart' in window) || (navigator.MaxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0));
};

$(document).ready(function () {
    if (is_touch_device()) $('html').addClass('touch');
    else  $('html').addClass('no-touch');

    /*$.ajax({
     url: '/index.php?route=module/search',
     type: 'get',
     dataType: 'html',
     success: function (html) {
     $('#filter').html(html);
     }
     });*/

    $.ajax({
        url: '/index.php?route=module/cart',
        type: 'get',
        dataType: 'html',
        success: function (html) {
            $('#module_cart').html(html);
        }
    });

    $(".colorbox").colorbox();

    view = $.cookie('list_view');
    if (view)
        listView(view);
    else
        listView('list');

    if($('.filter_box').length) {
        $( "<div class='btn__filter'>Показать фильтр</div>" ).insertBefore( ".filter_box" );
    }
    $('.btn__filter').live('click', function () {
        $('.filter_box').slideToggle(80);
        return false;
    });

    $('.no-touch .nav td.level0').hover(
        function () {
            el = this;
            t = setTimeout(function () {
                $(el).find('.drop').fadeIn(80);
            }, 10);
            $(el).data('timeout', t);
        },
        function () {
            el = this;
            clearTimeout($(el).data('timeout'));
            $(this).find('.drop').fadeOut(0);
        }
    );

    $('.touch .nav td.level0 > a.level0').click(function (e) {
        if($(window).width()>810) {
            e.preventDefault();
            $('.touch .nav td.level0 > a').not(this).next().hide();
            $(this).next().slideToggle(80)
            return false;
        }
    });
    
    $('.btn__menu').click(function (e) {
        $('#nav__mob').slideToggle(80);
        return false;
        
    });

});


function listView(view) {
    if (view == 'table') {
        $('.product_list').addClass('product_table');
        $('.list_view .table').addClass('active');
        $('.list_view .list').removeClass('active');
        $.cookie('list_view', 'table');
    } else {
        $('.product_list').removeClass('product_table');
        $('.list_view .list').addClass('active');
        $('.list_view .table').removeClass('active');
        $.cookie('list_view', 'list');
    }
}

//добавление товаров для сравнения
function addToWishList(product_id) {
    $.ajax({
        url: 'index.php?route=product/wish_list/update',
        type: 'post',
        data: 'product_id=' + product_id,
        dataType: 'json',
        success: function (json) {
            $('#wish_list_module_text').html(json['total']);
            $('#but' + product_id).html('<a class="button_wish_minus" onclick="delFromWishList(' + product_id + ')">Удалить из списка</a>');
            ImageFly(product_id);
            $('.wish_count a').removeAttr('onclick');
            $('.wish_count a').click(function () {
                return true;
            })
        }
    });
}
//удаление товаров для сравнения
function delFromWishList(product_id) {
    $.ajax({
        url: 'index.php?route=product/wish_list/update',
        type: 'post',
        data: 'remove=' + product_id,
        dataType: 'json',
        success: function (json) {
            $('#wish_list_module_text').html(json['total']);
            $('#but' + product_id).html('<a class="button_wish" onclick="addToWishList(' + product_id + ')">В список желаний</a>');

            if (json['total'] == '0 товаров') {
                $('.wish_count a').click(function () {
                    return false;
                })
            }
        }
    });
}

//картинка улетает в модуль товаров для сравнения
function ImageFly(rel) {
    var image = $('#big_image' + rel);
    var compare = $('#wish_list_module_text');
    of_top = image.offset().top;
    of_left = image.offset().left;

    of_top_cart = compare.offset().top;
    of_left_cart = compare.offset().left;

    if ($('#big_image' + rel).attr('img') != undefined) {
        $('body').append('<img src="' + $('#big_image' + rel).attr('img') + '" id="temp" style="z-index: 1000; position: absolute; top: ' + of_top + 'px; left: ' + of_left + 'px;" />');

        params = {
            top: of_top_cart,
            left: of_left_cart,
            opacity: 0.0,
            width: 50,
            heigth: 50
        };

        $('#temp').animate(params, 1000, false, function () {
            $('#temp').remove();
        });
    }
}

$(document).ready(function () {
    $('.link_show').click(function () {
        $($(this).attr('rel')).toggle();

        $(this).toggleClass('link_hide');

        alt = $(this).attr('alt');
        $(this).attr('alt', $(this).find('span').html());
        $(this).find('span').html(alt)

        return false;
    });

    $('.link_preview').click(function () {
        $($(this).attr('rel')).toggleClass('text_preview');

        $(this).toggleClass('link_preview_hide');

        alt = $(this).attr('alt');
        $(this).attr('alt', $(this).find('span').html());
        $(this).find('span').html(alt)

        return false;
    });
});


var url = location.protocol + "//" + location.host + '/' + 'index.php?route=product/search';
/* подсказки в поиске */

/*
$("#filter_keyword").keyup(function () {
    var search;
    var price_to;
    var price_from;
    search = $("#filter_keyword").val();


    if (search.length > 3) {
        $.ajax({
            type: "POST",
            url: url + "/suggest",
            data: {keyword: search},
            success: function (data) {
                $("#suggest").empty();
                if (data.length > 0)
                    $("#suggest").append(data);
            }
        });
    }
    else {
        $("#suggest").empty();
    }

})
*/
/* искать по нажатию enter */



//        $(document).ready(function () {
//            $('.placeholder').each(function(){
//                if(this.value == $(this).attr('placeholder'))
//                    $(this).addClass('placeholder_active');
//            })
//            .focus(function(){
//                if (this.value == $(this).attr('placeholder')){
//                    this.value = '';
//                    $(this).removeClass('placeholder_active');
//                }
//            })
//            .blur(function(){
//                setTimeout("$('#suggest').empty();", 300);
//                if (this.value == ''){
//                    this.value = $(this).attr('placeholder');
//                    $(this).addClass('placeholder_active');
//                }
//            });
//        })


/* Искать на нажатию "Найти" */
/*
function moduleSearch() {
    var filter_keyword = $('#filter_keyword').attr('value');

        url += '&keyword=' + encodeURIComponent(filter_keyword);
    location = url;
}
*/




$("#filter_keyword").keydown(function (e) {
    if (e.keyCode === 13) {
        var filter_keyword = $('#filter_keyword').attr('value');
        if (filter_keyword && filter_keyword != 'введите слово для поиска...' && filter_keyword != 'поиск...') {
            url += '&keyword=' + encodeURIComponent(filter_keyword);
        }
        location = url;
    }
});