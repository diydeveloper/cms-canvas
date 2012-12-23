<div>
    <?php 
        echo form_textarea(array(
            'name'  => 'field_id_' . $Field->id, 
            'class' => 'textarea_content ckeditor_textarea', 
            'style' => ( ! empty($Field->settings['height'])) ? 'height: ' . $Field->settings['height'] . 'px;' : 'height: 300px;', 
            'value' => set_value('field_id_' . $Field->id, $content),
        )); 
    ?>
</div>
