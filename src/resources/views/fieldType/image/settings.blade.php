<div>
    {!! Form::label($fieldType->getSettingsKey('output_type'), 'Output Type:') !!}
    {!! Form::select(
        $fieldType->getSettingsKey('output_type'),
        ['image'  => 'Image', 'image_path' => 'Image Path'],
        $fieldType->getSetting('output_type'),
        ['id' => 'output_type']
    ) !!}
</div>

<div class="image_setting">
    {!! Form::label($fieldType->getSettingsKey('inline_editable'), 'Inline Editable:') !!}
    <span>
        <label>{!! Form::radio($fieldType->getSettingsKey('inline_editable'), '1', (bool) $fieldType->getSetting('inline_editable', true)) !!} Yes</label>
        <label>{!! Form::radio($fieldType->getSettingsKey('inline_editable'), '0', (bool) ! $fieldType->getSetting('inline_editable', true)) !!} No</label>
    </span>
</div>

<div class="image_setting">
    {!! Form::label($fieldType->getSettingsKey('id'), 'Tag ID:') !!}
    {!! Form::text($fieldType->getSettingsKey('id'), $fieldType->getSetting('id')) !!}
</div>

<div class="image_setting">
    {!! Form::label($fieldType->getSettingsKey('class'), 'Class:') !!}
    {!! Form::text($fieldType->getSettingsKey('class'), $fieldType->getSetting('class')) !!}
</div>

<div class="image_setting">
    {!! Form::label($fieldType->getSettingsKey('max_width'), 'Max Width:') !!}
    {!! Form::text(
        $fieldType->getSettingsKey('max_width'), 
        $fieldType->getSetting('max_width'),
        ['style' => 'width: 50px;']
    ) !!} px
</div>

<div class="image_setting">
    {!! Form::label($fieldType->getSettingsKey('max_height'), 'Max Height:') !!}
    {!! Form::text(
        $fieldType->getSettingsKey('max_height'), 
        $fieldType->getSetting('max_height'),
        ['style' => 'width: 50px;']
    ) !!} px
</div>

<div class="image_setting">
    {!! Form::label($fieldType->getSettingsKey('crop'), 'Crop to Dimensions:') !!}
    <span>
        <label>{!! Form::radio($fieldType->getSettingsKey('crop'), '1', (bool) $fieldType->getSetting('crop', false)) !!} Yes</label>
        <label>{!! Form::radio($fieldType->getSettingsKey('crop'), '0',  ! (bool) $fieldType->getSetting('crop', false)) !!} No</label>
    </span>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        $('#output_type').change( function() {
            if ($(this).val() == 'image') {
                $('.image_setting').show();
            } else {
                $('.image_setting').hide();
            }
        });

        $('#output_type').trigger('change');
    });
</script>
