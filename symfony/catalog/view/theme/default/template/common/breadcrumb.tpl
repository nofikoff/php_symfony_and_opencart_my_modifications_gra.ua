<?php if($breadcrumbs) { ?>
    <div class="breadcrumb">
        <?php $arr = [];?>
        <?php foreach ($breadcrumbs as $key => $breadcrumb) { ?>
            <?php if($key > 0) echo '<i></i>' ?>
            <div class="breadcrumb2">
                <?php if(($key + 1) == count($breadcrumbs)):?>
                    <span><?php echo $breadcrumb['text']; ?></span>
                <?php
                        $arr[] = [
                            '@type' => 'ListItem',
                            'position' => ($key + 1),
                            'name' => $breadcrumb['text'],
                        ];
                ?>
                <?php else: ?>
                    <?php
                        $arr[] = [
                            '@type' => 'ListItem',
                            'position' => ($key + 1),
                            'name' => $breadcrumb['text'],
                            'item' => str_replace(['&','/home'], ['&amp;','/'], $breadcrumb['href'])
                        ];
                    ?>
                    <a href="<?php echo str_replace(['&','/home'], ['&amp;','/'], $breadcrumb['href']); ?>">
                        <span><?php echo $breadcrumb['text']; ?></span>
                    </a>
                <?php endif; ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [<?php echo json_encode($arr); ?>]
    }
</script>

