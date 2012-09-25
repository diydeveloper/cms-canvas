<?php $i = 1; ?>

<ul<?php echo (($tag_id) ? ' id="' . $tag_id . '"' : '') . (($class) ? ' class="' . $class . '"' : ''); ?>>
<?php foreach($crumbs as $crumb): ?>
    <li <?php echo ($crumb['class']) ? 'class="' . $crumb['class'] . '"' : ''; ?>>
        <a <?php echo (($crumb['target']) ? 'target="' . $crumb['target'] . '"': ''); ?> href="<?php echo $crumb['url']; ?>"><?php echo $crumb['title']; ?></a>
        <?php if ($breadcrumb_seperator != null && count($crumbs) != $i): ?>
            <span><?php echo $breadcrumb_seperator; ?></span>
        <?php endif; ?>
    </li>
    <?php $i++; ?>
<?php endforeach; ?>
</ul>
