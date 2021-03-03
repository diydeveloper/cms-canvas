<div>
    <textarea name="{{ $fieldType->getKey() }}" class="textarea_content ckeditor_textarea" style="height: {{ $fieldType->getSetting('height', '300') }}px;">{{ old($fieldType->getKey(), $fieldType->getData()) }}</textarea>
</div>
