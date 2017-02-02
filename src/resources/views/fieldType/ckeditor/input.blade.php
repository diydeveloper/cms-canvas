<div>
    <textarea name="{{ $fieldType->getKey() }}" class="textarea_content ckeditor_textarea" style="height: {{ $fieldType->getSetting('height', '300') }}px;">{{ $fieldType->getData() }}</textarea>
</div>
