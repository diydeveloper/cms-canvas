<div>
    {!! Form::label($fieldType->getSettingsKey('content_type'), 'Content Type:') !!}
    {!! Form::text($fieldType->getSettingsKey('content_type'), $fieldType->getSetting('content_type')) !!}
</div>

<div>
    {!! Form::label($fieldType->getSettingsKey('entry_id'), 'Entry ID:') !!}
    {!! Form::text($fieldType->getSettingsKey('entry_id'), $fieldType->getSetting('entry_id')) !!}
</div>

<div>
    {!! Form::label($fieldType->getSettingsKey('url_title'), 'URL Title:') !!}
    {!! Form::text($fieldType->getSettingsKey('url_title'), $fieldType->getSetting('url_title')) !!}
</div>

<div>
    {!! Form::label($fieldType->getSettingsKey('year'), 'Year:') !!}
    {!! Form::text($fieldType->getSettingsKey('year'), $fieldType->getSetting('year')) !!}
</div>

<div>
    {!! Form::label($fieldType->getSettingsKey('month'), 'Month:') !!}
    {!! Form::text($fieldType->getSettingsKey('month'), $fieldType->getSetting('month')) !!}
</div>

<div>
    {!! Form::label($fieldType->getSettingsKey('day'), 'Day:') !!}
    {!! Form::text($fieldType->getSettingsKey('day'), $fieldType->getSetting('day')) !!}
</div>

<div>
    {!! Form::label($fieldType->getSettingsKey('day'), 'Day:') !!}
    {!! Form::text($fieldType->getSettingsKey('day'), $fieldType->getSetting('day')) !!}
</div>

<div>
    {!! Form::label($fieldType->getSettingsKey('limit'), 'Limit:') !!}
    {!! Form::text($fieldType->getSettingsKey('limit'), $fieldType->getSetting('limit')) !!}
</div>

<div>
    {!! Form::label($fieldType->getSettingsKey('order_by'), 'Order By:') !!}
    {!! Form::text($fieldType->getSettingsKey('limit'), $fieldType->getSetting('limit')) !!}
</div>

<div>
    {!! Form::label($fieldType->getSettingsKey('sort'), 'Sort:') !!}
    {!! Form::select(
        $fieldType->getSettingsKey('sort'),
        ['' => '', 'ASC'  => 'Ascending', 'DESC' => 'Descending'],
        $fieldType->getSetting('sort'),
        ['id' => 'sort']
    ) !!}
</div>

<div>
    {!! Form::label($fieldType->getSettingsKey('pagination'), 'Pagination:') !!}
    {!! Form::select(
        $fieldType->getSettingsKey('pagination'),
        ['' => '', 'paginate'  => 'Paginate', 'simple_paginate' => 'Simple Paginate'],
        $fieldType->getSetting('pagination'),
        ['id' => 'pagination']
    ) !!}
</div>

<div class="pagination_settings">
    {!! Form::label($fieldType->getSettingsKey('per_page'), 'Entries Per Page:') !!}
    {!! Form::text($fieldType->getSettingsKey('per_page'), $fieldType->getSetting('per_page')) !!}
</div>

<script type="text/javascript">
    $(document).ready( function() {
        $('#pagination').change( function() {
            if ($(this).val() == 'paginate' || $(this).val() == 'simple_paginate') {
                $('.pagination_settings').show();
            } else {
                $('.pagination_settings').hide();
            }
        });

        $('#pagination').trigger('change');
    });
</script>