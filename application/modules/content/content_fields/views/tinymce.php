<div>
    <?php 
        echo form_textarea(array(
            'name'  => 'field_id_' . $Field->id, 
            'class' => 'textarea_content tinymce', 
            'value' => set_value('field_id_' . $Field->id, $content),
        )); 
    ?>
</div>
