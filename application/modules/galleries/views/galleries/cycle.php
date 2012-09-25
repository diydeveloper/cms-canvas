<script type="text/javascript" src="<?php echo site_url('/assets/js/jquery.cycle.all.min.js'); ?>"></script>

<?php
    // Set default paramaters if not passed
    $effect = (isset($effect)) ? $effect : 'fade';
    $speed = (isset($speed)) ? $speed : '1000';
    $pause = (isset($pause)) ? $pause : '0';
    $timeout = (isset($timeout)) ? $timeout : '4000';
    $delay = (isset($delay)) ? $delay : '0';
    $sync = (isset($sync)) ? $sync : 'true';
?>

<div id="gallery_<?php echo $Gallery->id ?>">
    <?php foreach($images as $image): ?>
        <img alt="<?php echo $image['title']; ?>" src="<?php echo $image['image']; ?>" />
    <?php endforeach; ?>
</div>

<script type="text/javascript">
$(document).ready( function() {
    $('#gallery_<?php echo $Gallery->id; ?>').cycle({
        fx: '<?php echo $effect; ?>',
        speed: <?php echo $speed; ?>,
        pause: <?php echo $pause; ?>,
        timeout: <?php echo $timeout; ?>,
        sync: <?php echo $sync; ?>,
        delay: <?php echo $delay; ?>
    });
});
</script>
