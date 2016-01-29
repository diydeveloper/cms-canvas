<div style="width: 150px; text-align: center; float: left;">
    <a class="choose_image" href="javascript:void(0);" style="display: block; margin-bottom: 5px;">
        <img class="image_thumbnail" src="{!! Content::thumbnail(old($fieldType->getKey(), $fieldType->data), ['width' => 150, 'height' => 150, 'no_image' => Theme::asset('images/no_image.jpg')]) !!}" />
    </a>

    <a class="remove_image" href="javascript:void(0);"><span>Remove Image</span></a><br />
    <a class="choose_image" href="javascript:void(0);"><span>Add Image</span></a>
    {!! Form::hidden($fieldType->getKey(), $fieldType->data, array('class' => 'hidden_file')) !!}
</div>

<div style="float: left; margin-left: 15px; width: 220px;">
    <label for="alt"><strong>Alternative Text:</strong></label>
    {!! Form::text($fieldType->getMetadataKey('alt'), $fieldType->getMetadata('alt')) !!}
</div>

<div class="clear"></div>