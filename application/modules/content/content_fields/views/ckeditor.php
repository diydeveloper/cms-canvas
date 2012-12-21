<div>
    <?php 
        echo form_textarea(array(
            'name'  => 'field_id_' . $Field->id, 
            'class' => 'textarea_content ckeditor_textarea', 
            'value' => set_value('field_id_' . $Field->id, $content),
        )); 
    ?>
</div>
