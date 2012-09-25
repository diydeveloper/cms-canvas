<script type="text/javascript" src="<?php echo site_url('/assets/js/fancybox/jquery.fancybox-1.3.4.pack.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('/assets/js/fancybox/jquery.easing-1.3.pack.js'); ?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo site_url('/assets/js/fancybox/jquery.fancybox-1.3.4.css'); ?>" />

<ul id="gallery_<?php echo $Gallery->id ?>">
    <?php foreach($images as $image): ?>
            <li>
                <a title="<?php echo $image['title'] ?>" rel="gallery_<?php echo $Gallery->id ?>" href="<?php echo $image['image']; ?>">
                    <img alt="<?php echo $image['title']; ?>" src="<?php echo $image['thumb']; ?>" />
                </a>
            </li>
    <?php endforeach; ?>
</ul>
<div class="clear"></div>

<script>
    $("#gallery_<?php echo $Gallery->id; ?> a").fancybox({titlePosition: 'over'});
</script>
