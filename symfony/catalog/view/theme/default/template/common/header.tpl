<!DOCTYPE html>
<html lang="ru-RU" prefix="og: http://ogp.me/ns#">
<head>
<!--[if IE]>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Language" content="ru-ru"/>

<meta http-equiv="imagetoolbar" content="no"/>
<meta http-equiv="MSThemeCompatible" content="no"/>
<![endif]-->
<!--[if lt IE 9]>
<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<!--[if lt IE 8]>
<link rel="stylesheet" type="text/css" href="/catalog/view/theme/default/stylesheet/ie6.css"/>
<![endif]-->
<!--[if IE]>
<script type="text/javascript" src="/catalog/view/javascript/ie-hover-ns-pack.js"></script>
<![endif]-->

<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Граю Я | gra.ua"/>
<meta property="og:title" content="<?php echo $product_name; ?>"/>
<meta property="og:description" content=" <?php echo $description; ?>">
<meta property="og:url" content="<?php echo $product_url; ?>"/>
<?php if($datalayer) echo $datalayer;?>
<?php if ($thumb || $images) { ?>
<meta property="og:image" content="<?php echo $thumb; ?>">
<?php } else { ?>
<meta property="og:image" content="<?php echo $logo; ?>">
<?php } ?>
<meta property="product:retailer_item_id" content="<?php echo(isset($model) ? $model : '')?>">


<link type="text/css" href="/catalog/view/theme/default/stylesheet/stylesheet.css?v13" rel="stylesheet"/>
<link type="text/css" href="/catalog/view/theme/default/stylesheet/colorbox.css" rel="stylesheet"/>
<link rel="stylesheet" type="text/css" href="/catalog/view/theme/default/stylesheet/adaptive.css"/>
<link rel="stylesheet" type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css"/>
<style>.102
    okWC021kmxEQWFkv0Cwo032p0CWlx10wxloEF020Cp3p231qPBmxsxQEWd {
        clear: both;
        overflow: hidden;
        height: 122px;
        font-size: 0.9em;
    }</style>

<link rel="author" href="https://plus.google.com/112581242649273915681"/>


<title>🎲<?php echo $title; ?></title>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>"/>
<?php } ?>
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>"/>
<?php } ?>

<link href="/favicon.ico" rel="icon"/>

<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>"
      media="<?php echo $style['media']; ?>"/>
<?php } ?>

    <?php if (isset($_GET["page"])) { ?>
    <meta name="robots" content="noindex, follow" />
    <?php } ?>

    <?php if (isset($_GET["sort"]) || isset($_GET["order"])) { ?>
    <meta name="robots" content="noindex, nofollow" />
    <?php } ?>

    <?php if (isset($_GET["route"]) && $_GET["route"] == "product/search") { ?>
    <meta name="robots" content="noindex, nofollow" />
    <?php } ?>

    <?php
     $noIndex = false;
     foreach($_GET as $params) {
         $tmp = explode(',',$params);
         if(count($tmp) > 1) {
            $noIndex = true;
            break;
         }
     }
     ?>
    <?php if ($noIndex) { ?>
    <meta name="robots" content="noindex, nofollow" />
    <?php } ?>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-ZGWQ');</script>
<!-- End Google Tag Manager -->
<?php /* ?>
<script>
  (function(d) {
    var s = d.createElement('script');
    s.defer = true;
    s.src = 'https://multisearch.io/plugin/10837';
    if (d.head) d.head.appendChild(s);
  })(document);
</script>
<?php */ ?>
</head>

<body>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-ZGWQ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<script type="text/javascript">
    (function (d, w, s) {
        var widgetHash = '2gx7hw6oaii8waf04lps', ctw = d.createElement(s);
        ctw.type = 'text/javascript';
        ctw.async = true;
        ctw.src = '//widgets.binotel.com/calltracking/widgets/' + widgetHash + '.js';
        var sn = d.getElementsByTagName(s)[0];
        sn.parentNode.insertBefore(ctw, sn);
    })(document, window, 'script');
</script>

    
<script>
    (function (i, s, o, g, r, a, m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)}, i[r].l = 1 * new Date();
    a = s.createElement(o),
        m = s.getElementsByTagName(o)[0];
    a.async = 1;
    a.src = g;
    m.parentNode.insertBefore(a, m)
    })
    (window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

    ga('create', 'UA-5285806-2', 'auto');
    ga('send', 'pageview');

</script>


<?php //echo $google_analytics;?>

<?php if(isset($ga_order_infos) && $ga_order_infos) { ?>
<!-- Google Analytics Ecommerce Tracking [BEGIN] -->
<script type="text/javascript">
    ga('require', 'ecommerce', 'ecommerce.js');

    <
        ? php foreach($ga_order_infos as $ga_order_info)
    {
            ?
    >

        ga('ecommerce:addTransaction', {
            id: '<?= $ga_order_info['order_id'] ?>',
            affiliation: 'gra.ua',
            revenue: '<?= $ga_order_info['total'] ?>'
        });

    <
            ? php foreach($ga_order_info['products'] as $product)
        {
                ?
        >

            ga('ecommerce:addItem', {
                id: '<?= $product['order_id'] ?>',
                sku: '<?= $product['product_id'] ?>',
                name: '<?= $product['product_name'] ?>',
                category: '<?= $product['category_name'] ?>',
                price: '<?= $product['price'] ?>',
                quantity: '<?= $product['quantity'] ?>'
            });

        <
                ? php
        }
            ?
    >

        ga('ecommerce:send');

    <
            ? php
    }
        ?
    >
</script>
<?php } ?>
<!-- Google Analytics Ecommerce Tracking [END] -->

<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>
<section class="wrapper">
    <header class="header">

        


        <a class="logo" href="/"></a>

        <div class="btn__menu">
            <div style="width: 30px; float: left; margin-right: 15px" >
            <span></span><span></span><span></span>
            </div>
            <div style="color: #fff;
    font-size: 18px;
    height: 30px;
    display: flex;
    align-items: center;     justify-content: center;">Каталог товаров</div>

        </div>
        <nav class="nav " id="nav__mob">
            <table class="level0 navigation">
                <tr>
                    <?php foreach($categories as $key => $category) { ?>
                    <?php if($category['category_id'] == 5 || $category['category_id'] == 377) continue;?>
                    <td class="level0 <?php if($category['category_id'] == $active_category) echo " active
                    " ?>">
                    <a class="level0 category<?php echo $category['category_id'] ?> name"
                       href="<?php echo str_replace('&', '&amp;', $category['href']); ?>">
                        <?php echo $category['name']; ?>
                        <span class="drop_arrow"></span>
                    </a>
                    <?php if($category['sub']) { ?>
                    <div class="drop <?php echo ($key < 6 ? 'drop_left' : 'drop_right') ?> drop_col<?php echo $category['column_count']  ?>">
                        <div class="col">
                            <?php foreach($category['sub'] as $category1) { ?>
                            <?php if($category1['column_count']>1) echo '
                        </div>
                        <div class="col">' ?>
                            <ul>
                                <li class="title"><a
                                            href="<?php echo str_replace('&', '&amp;', $category1['href']); ?>"><?php echo html_entity_decode($category1['name']); ?></a>
                                </li>
                                <?php foreach($category1['sub'] as $category2) { ?>
                                <li>
                                    <a href="<?php echo str_replace('&', '&amp;', $category2['href']); ?>"><?php echo html_entity_decode($category2['name']); ?></a>
                                </li>
                                <?php } ?>
                            </ul>
                            <?php } ?>
                        </div>
                        <span class="clear"></span>
                    </div>
                    <?php } ?>
                    </td>

                    <?php } ?>
                </tr>
            </table>
        </nav>


        <div class="right_info">
            <div class="info_links"><?php echo $information; ?></div>
            <div class="search_box">
                <div class="search">
                    <div class="search_t">
                        <input type="text" value="Введите текст для поиска..." id="filter_keyword"
                               onfocus="if (this.value=='Введите текст для поиска...') this.value='';"
                               onblur="if (this.value==''){this.value='Введите текст для поиска...'}"/>
                        <a class="search_button"></a>
                    </div>
                </div>
                <div id="suggest"></div>
            </div>
            <div class="top_contact">

                <?php echo $top_contact; ?>
                <?php /* <a class="top_icon callback colorbox"  title="<?php echo $callback_tittle; ?>"
                href="<?php echo str_replace('&', '&amp;', $callback_href); ?>"><span>Перезвоните мне</span></a> */ ?>
            </div>

            <div id="module_cart">
            </div>
            <?php /*
                    <div class="wish_count">
            <a onclick="<?php if(!isset($this->session->data['wish_list'])||!$this->session->data['wish_list']){echo 'return false;';}?>"
               href="<?php echo str_replace('&', '&amp;', $wish_list_href); ?>">Р’ СЃРїРёСЃРєРµ
                Р¶РµР»Р°РЅРёР№: </a><span class="number" id="wish_list_module_text"><?php echo $wish_count ?></span>
        </div>
        */ ?>

        </div>
        <nav class="nav" id="nav__dt">
            <table class="level0 navigation">
                <tr>
                    <?php foreach($categories as $key => $category) { ?>
                    <?php if($category['category_id'] == 5 || $category['category_id'] == 377) continue;?>
                    <td class="level0 <?php if($category['category_id'] == $active_category) echo " active
                    " ?>">
                    <a class="level0 category<?php echo $category['category_id'] ?> name"
                       href="<?php echo str_replace('&', '&amp;', $category['href']); ?>">
                        <?php echo $category['name']; ?>
                        <span class="drop_arrow"></span>
                    </a>
                    <?php if($category['sub']) { ?>
                    <div class="drop <?php echo ($key < 6 ? 'drop_left' : 'drop_right') ?> drop_col<?php echo $category['column_count']  ?>">
                        <div class="col">
                            <?php foreach($category['sub'] as $category1) { ?>
                            <?php if($category1['column_count']>1) echo '
                        </div>
                        <div class="col">' ?>
                            <ul>
                                <li class="title"><a
                                            href="<?php echo str_replace('&', '&amp;', $category1['href']); ?>"><?php echo html_entity_decode($category1['name']); ?></a>
                                </li>
                                <?php foreach($category1['sub'] as $category2) { ?>
                                <li>
                                    <a href="<?php echo str_replace('&', '&amp;', $category2['href']); ?>"><?php echo html_entity_decode($category2['name']); ?></a>
                                </li>
                                <?php } ?>
                            </ul>
                            <?php } ?>
                        </div>
                        <span class="clear"></span>
                    </div>
                    <?php } ?>
                    </td>

                    <?php } ?>
                </tr>
            </table>
        </nav>
    </header>
    <section class="main">
        <?php echo $breadcrumb; ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>        
        <script src="/catalog/view/javascript/jquery/common.js"></script>
		 <script src="/catalog/view/javascript/jquery/sphinxpro/sphinxpro.js"></script>
		<link rel="stylesheet" type="text/css" href="/catalog/view/javascript/jquery/sphinxpro/sphinxpro.css"/>
        <script src="/catalog/view/javascript/all.min.js"></script>
        <!-- SoftCube BEGIN -->
        <script type="text/javascript">
            !function (t, e, c, n){var s=e.createElement(c);s.async=1,s.src="//script.softcube.com/"+n+"/sc.js";var r=e.scripts[0];r.parentNode.insertBefore(s,r)}(window, document, 'script', "61A0C97E00F6464B806B18B4FC7FEDC6");
        </script>
        <!-- SoftCube END -->