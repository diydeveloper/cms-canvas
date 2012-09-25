<?php echo validation_errors(); ?>
<form<?php echo (($anchor) ? ' action="' . current_url() . $anchor . '"' : '') ?> method="post"<?php echo ($id) ? ' id="' . $id . '"' : '' ; ?><?php echo ($class) ? ' class="' . $class . '"' : '' ; ?>>
    <?php echo $Form->fields(); ?>
    <div style="display: none;">
        <input type="text" name="spam_check" value="" />
        <?php if ($id): ?>
            <input type="hidden" name="form_id" value="<?php echo $id; ?>" />
        <?php endif; ?>
    </div>
</form>
