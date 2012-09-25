<div class="subnav">
    <ul>
        <li><a <?php echo ($this->uri->segment(3) == 'types') ? 'class="selected"' : ''; ?> href="<?php echo site_url(ADMIN_PATH . '/content/types/edit/' . $this->uri->segment(5)); ?>"><span>Edit Content Type</span></a></li>
        <li><a <?php echo ($this->uri->segment(3) == 'fields') ? 'class="selected"' : ''; ?> href="<?php echo site_url(ADMIN_PATH . '/content/fields/index/' . $this->uri->segment(5)); ?>"><span>Fields</span></a></li>
    </ul>
    <div class="clear"></div>
</div>
