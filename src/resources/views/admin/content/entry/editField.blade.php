<div>
    {!! HTML::decode(Form::label('field_id_'.$fieldType->getField()->id, '<div class="arrow arrow_expand"></div>'.(($fieldType->getField()->required) ? '<span class="required">*</span> ' : ''). $fieldType->getField()->label)) !!}
    <div>
        @if ($fieldType->getField()->translate)
            <div class="tabs">
                <ul class="htabs">
                    @foreach ($relatedFieldTypes as $relatedFieldType)
                        <?php $language = $languages->getFirstWhere('locale', $relatedFieldType->getLocale()); ?>
                        <li>
                            <a href="#translate-{!! $relatedFieldType->getkey() !!}">{!! $language->language !!}</a>
                        </li>
                    @endforeach
                </ul>
                @foreach ($relatedFieldTypes as $relatedFieldType)
                    <div id="translate-{!! $relatedFieldType->getKey() !!}">
                        {!! $relatedFieldType->inputField() !!}
                    </div>
                @endforeach
            </div>
        @else
            {!! $fieldType->inputField() !!}
        @endif
    </div>
</div>