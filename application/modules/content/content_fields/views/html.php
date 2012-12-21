<div class="html_code">
    <?php 
        echo form_textarea(array(
            'id' => 'field_id_' . $Field->id, 
            'name'=>'field_id_' . $Field->id, 
            'value'=>set_value('field_id_' . $Field->id, $content)
        )); 
    ?>
</div>

<script type="text/javascript">
    var editor = CodeMirror.fromTextArea(document.getElementById('<?php echo 'field_id_'  . $Field->id; ?>'), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "application/x-httpd-php",
        indentUnit: 4,
        indentWithTabs: true,
        enterMode: "keep",
        tabMode: "shift"
    });
</script>
