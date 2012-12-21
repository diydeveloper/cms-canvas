<div id="admin-toolbar">
    <ul class="admin-toolbar-left">
        <?php if ($entry_id): ?>
        <li><a target="ee_admin" class="admin-toolbar-top" href="<?php echo site_url(ADMIN_PATH . '/content/entries/edit/' . $content_type_id . '/' . $entry_id); ?>">Edit Page</a></li>
        <?php else: ?>
        <li><a target="ee_admin" class="admin-toolbar-top" href="<?php echo site_url(ADMIN_PATH . '/content/types/edit/' . $content_type_id); ?>">Edit Content Type</a></li>
        <?php endif; ?>
        <li>
            <a class="admin-toolbar-top" href="<?php echo site_url(ADMIN_PATH . '/content/entries'); ?>">Content &nbsp;<img src="<?php echo site_url('/application/modules/content/assets/images/down-triangle.gif'); ?>" /></a>
            <ul>
                <li><a href="<?php echo site_url(ADMIN_PATH . '/content/entries'); ?>">Entries</a></li>
                <li><a href="<?php echo site_url(ADMIN_PATH . '/content/types'); ?>">Content Types</a></li>
                <li><a href="<?php echo site_url(ADMIN_PATH . '/navigations'); ?>">Navigations</a></li>
            </ul>
        </li>
    </ul>
    <ul class="admin-toolbar-right">
        <?php if (is_inline_editable()): ?>
            <li><span id="admin-save-status" class="admin-toolbar-top"></span></li>
            <li><a id="admin-save-changes" class="admin-toolbar-top" href="javascript:void(0);">Save Changes</a></li>
        <?php endif; ?>
        <li><a id="admin-settings-icon" class="admin-toolbar-top" href="javascript:void(0);"></a>
            <ul>
                <li><a id="admin-toggle-inline-editing" href="javascript:void(0);"><?php echo ($this->settings->enable_inline_editing) ? 'Disable' : 'Enable'; ?> Inline Editing</a></li>
                <li><a href="<?php echo site_url('/users/logout') ?>">Sign Out</a></li>
            </ul>
        </li>
    </ul>
    <div id="admin-toolbar-shadow"></div>
</div>
