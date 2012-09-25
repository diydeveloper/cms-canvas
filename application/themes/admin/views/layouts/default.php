<?php echo theme_partial('header'); ?>

<div id="content">
    <div class="breadcrumb"><?php echo isset($breadcrumb) ? $breadcrumb : ''; ?></div>
    <?php echo $this->session->flashdata('message'); ?>
    <?php echo validation_errors(); ?>
    

    <?php echo  $content; ?>
</div>

<?php echo theme_partial('footer'); ?>
