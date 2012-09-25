<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/setting.png'); ?>"> Theme Editor</h1>

        <?php if ($file_writable && $file_readable && $files_found): ?>
            <div class="buttons">
                <a class="button" href="#" onClick="$('#form').submit();"><span>Save</span></a>
            </div>
        <?php endif; ?>
    </div>
    <div class="content">
        <?php if ( ! $file_writable &&  ($files_found && $file_readable)): ?>
            <p class="attention">The current file does not have writable permissions.</p>
        <?php endif; ?>
        <div id="file_explorer">
            <ul>
                <li>
                    <div class="title">Stylesheets</div>
                    <?php if ( ! empty($stylesheets)): ?>
                    <ul>
                        <?php foreach($stylesheets as $stylesheet): ?>
                            <li>
                                <a <?php echo ($stylesheet['theme_path'] == $file) ? 'class="selected"' : ''; ?> href="<?php echo site_url(ADMIN_PATH . '/settings/theme-editor/index/' . $stylesheet['hash']); ?>">
                                    <?php echo $stylesheet['title']; ?>
                                    <div class="filepath"><?php echo $stylesheet['relative_path']; ?></div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </li>

                <li>
                    <div class="title">Layouts</div>
                    <?php if ( ! empty($layouts)): ?>
                    <ul>
                        <?php foreach($layouts as $layout): ?>
                            <li>
                                <a <?php echo ($layout['theme_path'] == $file) ? 'class="selected"' : ''; ?> href="<?php echo site_url(ADMIN_PATH . '/settings/theme-editor/index/' . $layout['hash']); ?>">
                                    <?php echo $layout['title']; ?>
                                    <div class="filepath"><?php echo $layout['relative_path']; ?></div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </li>

                <li>
                    <div class="title">Partials</div>
                    <?php if ( ! empty($partials)): ?>
                    <ul>
                        <?php foreach($partials as $partial): ?>
                            <li>
                                <a <?php echo ($partial['theme_path'] == $file) ? 'class="selected"' : ''; ?> href="<?php echo site_url(ADMIN_PATH . '/settings/theme-editor/index/' . $partial['hash']); ?>">
                                    <?php echo $partial['title']; ?>
                                    <div class="filepath"><?php echo $partial['relative_path']; ?></div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </li>

                <li>
                    <div class="title">JavaScripts</div>
                    <?php if ( ! empty($javascripts)): ?>
                    <ul>
                        <li>
                            <?php foreach($javascripts as $javascript): ?>
                                <a <?php echo ($javascript['theme_path'] == $file) ? 'class="selected"' : ''; ?> href="<?php echo site_url(ADMIN_PATH . '/settings/theme-editor/index/' . $javascript['hash']); ?>">
                                    <?php echo $javascript['title']; ?>
                                    <div class="filepath"><?php echo $javascript['relative_path']; ?></div>
                                </a>
                            <?php endforeach; ?>
                        </li>
                    </ul>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
        <div style="margin-right: 230px;">
            <?php if ( ! $files_found): ?>
                <p class="attention">No files were found for this theme.</p>
            <?php elseif ( ! $file_readable): ?>
                <p class="error">The attempted file does not exist or does not have readable permissions.</p>
            <?php else: ?>
                <?php echo form_open(null, 'id="form"'); ?>
                    <?php echo form_textarea(array('name'=>'code', 'id'=>'code', 'value'=>set_value('code', $code))); ?>
                <?php echo form_close(); ?>
                <script type="text/javascript">
                    var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
                        lineNumbers: true,
                        matchBrackets: true,
                        mode: "<?php echo $mode; ?>",
                        indentUnit: 4,
                        indentWithTabs: true,
                        enterMode: "keep",
                        tabMode: "shift"
                    });
                </script>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
    </div>
</div>
