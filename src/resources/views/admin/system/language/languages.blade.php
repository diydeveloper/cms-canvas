<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/language.png') !!}"> Languages</h1>

        <div class="buttons">
            <a class="button" href="{!! Admin::url('system/language/add') !!}"><span>Add Language</span></a>
            <a class="button delete" href="javascript:void(0);"><span>Delete</span></a>
        </div>
    </div>
    <div class="content">
        <div class="filter">
            {!! Form::model($filter, array('id' => 'filter_form')) !!}
                <div class="left">
                    <div><label>Search:</label></div>
                    {!! Form::text('filter[search]') !!}
                </div>

                <div class="left">
                    <div><label>Status:</label></div> 
                    {!! Form::select('filter[active]', ['' => '', '1' => 'Active', '0' => 'Inactive']) !!}
                </div>

                <div class="left filter_buttons">
                    <button type="submit" class="button"><span>Filter</span></button>
                    <button type="submit" class="button" name="clear_filter" value="1"><span>Clear</span></button>
                </div>
            {!! Form::close() !!}
            <div class="clear"></div>
        </div>

        {!! Form::open(array('id' => 'language_form')) !!}
            <table class="list">
                <thead>
                    <tr>
                        <th width="1" class="center">
                            <input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" />
                        </th>
                        <th>
                            <a rel="title" class="sortable{!! $orderBy->getElementClass('language') !!}" href="javascript:void(0);">Language</a>
                        </th>
                        <th>
                            <a rel="short_name" class="sortable{!! $orderBy->getElementClass('locale') !!}" href="javascript:void(0);">Locale</a>
                        </th>
                        <th>Status</th>
                        <th class="right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($languages) > 0)
                        @foreach ($languages as $language)
                            <tr>
                                <td class="center"><input type="checkbox" value="{!! $language->id !!}" name="selected[]" /></td>
                                <td>
                                    {!! $language->language !!}
                                    @if ($language->default)
                                        <span class="hint">Default</span>
                                    @endif
                                </td>
                                <td>{!! $language->locale !!}</td>
                                <td>
                                    @if ($language->active)
                                        Active
                                    @else
                                        Inactive
                                    @endif
                                </td>
                                <td class="right">
                                    @if ( ! $language->default)
                                        [ <a href="{!! Admin::url("system/language/{$language->id}/set-default") !!}">Set As Default</a> ] 
                                    @endif
                                    [ <a href="{!! Admin::url("system/language/{$language->id}/edit") !!}">Edit</a> ]
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="center">No results found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        {!! Form::close() !!}

        @include('theme::partials.pagination', ['paginator' => $languages])
    </div>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        $('.delete').click( function() {
            if (confirm('Delete cannot be undone! Are you sure you want to do this?')) {
                $('#language_form').attr('action', '{!! Admin::url('system/language/delete') !!}').submit()
            } else {
                return false;
            }
        });
    });
</script>
