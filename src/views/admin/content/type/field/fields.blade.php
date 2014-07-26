@include('cmscanvas::admin.content.type.subnav')

<div class="box">
    <div class="heading">
        <h1><img alt="" src="{{ Theme::asset('images/layout.png') }}">Content Fields - {{ $contentType->title }} ({{ $contentType->short_name }})</h1>

        <div class="buttons">
            <a class="button" href="{{ Admin::url('content/type/'.$contentType->id.'/field/add') }}"><span>Add Field</span></a>
            <a class="button delete" href="javascript:void(0);"><span>Delete</span></a>
        </div>
    </div>
    <div class="content">

        {{ Form::open(array('id' => 'form')) }}
            <table id="fields_table" class="list">
                <thead>
                    <tr class="nodrag nodrop">
                        <th style="width: 10px;"></th>
                        <th width="1" class="center"><input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></th>
                        <th>Title</th>
                        <th>Short Tag</th>
                        <th>Type</th>
                        <th class="right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($fields) > 0)
                        @foreach ($fields as $field)
                            <tr id="<?php echo $field->id ?>">
                                <td class="drag_handle"></td>
                                <td class="center"><input type="checkbox" value="{{ $field->id }}" name="selected[]" /></td>
                                <td>{{ $field->label }}</td>
                                <td>@{{ <?php echo $field->short_tag; ?> }}</td>
                                <td>{{ $field->type->name }}</td>
                                <td class="right">[ <a href="{{ Admin::url('/content/type/'.$contentType->id.'/field/'.$field->id.'/edit/') }}">Edit</a> ]</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="center" colspan="6">No content fields have been added.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        {{ Form::close() }}

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        $('.delete').click( function() {
            if (confirm('Delete cannot be undone! Are you sure you want to do this?')) {
                $('#form').attr('action', '{{ Admin::url('content/type/'.$contentType->id.'/field/delete/') }}').submit()
            } else {
                return false;
            }
        });

        // Sort fields (table sort)
        $('#fields_table').tableDnD({
            onDrop: function(table, row) {
                show_status('Saving...', false, true);
                order = $('#fields_table').tableDnDSerialize()
                $.post('{{ Admin::url('content/type/'.$contentType->id.'/field/order') }}', order, function() {
                    show_status('Saved', true, false);
                });
            },
            dragHandle: ".drag_handle"
        });

    });
</script>