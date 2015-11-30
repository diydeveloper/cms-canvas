<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/category.png') !!}"> Navigations</h1>

        <div class="buttons">
            <a class="button" href="{!! Admin::url('content/navigation/add') !!}"><span>Add Navigation</span></a>
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

                <div class="left filter_buttons">
                    <button type="submit" class="button"><span>Filter</span></button>
                    <button type="submit" class="button" name="clear_filter" value="1"><span>Clear</span></button>
                </div>
            {!! Form::close() !!}
            <div class="clear"></div>
        </div>

        {!! Form::open(array('id' => 'navigations_form')) !!}
            <table class="list">
                <thead>
                    <tr>
                        <th width="1" class="center">
                            <input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" />
                        </th>
                        <th>
                            <a rel="title" class="sortable{!! $orderBy->getElementClass('title') !!}" href="javascript:void(0);">Title</a>
                        </th>
                        <th>
                            <a rel="short_name" class="sortable{!! $orderBy->getElementClass('short_name') !!}" href="javascript:void(0);">Short Name</a>
                        </th>
                        <th class="right"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($navigations) > 0)
                        @foreach ($navigations as $navigation)
                            <tr class="row_link" data-href="{!! Admin::url("content/navigation/{$navigation->id}/tree") !!}">
                                <td class="center no_row_link"><input type="checkbox" value="{!! $navigation->id !!}" name="selected[]" /></td>
                                <td>{!! $navigation->title !!}</td>
                                <td>{!! $navigation->short_name !!}</td>
                                <td class="right">
                                    <ul class="actions_btn">
                                        <li>
                                            <a class="actions_link no_row_link" href="javascript:void(0);">
                                                <span class="actions_arrow">Actions</span>
                                            </a>
                                            <ul class="actions_dropdown no_row_link" style="text-align: left;">
                                                <li class="edit_icon"><a href="{!! Admin::url("content/navigation/{$navigation->id}/tree") !!}">Items</a></li>
                                                <li class="edit_icon"><a href="{!! Admin::url("content/navigation/{$navigation->id}/edit") !!}">Rename</a></li>
                                                <li><a href="javascript:void(0);" data-id="{!! $navigation->id !!}" data-href="{!! Admin::url('content/navigation/delete') !!}" class="delete_item">Delete</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="center">No results found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        {!! Form::close() !!}

        @include('theme::partials.pagination', ['paginator' => $navigations])
    </div>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        $('.delete').click( function() {
            if (confirm('Delete cannot be undone! Are you sure you want to do this?')) {
                $('#navigations_form').attr('action', '{!! Admin::url('content/navigation/delete') !!}').submit()
            } else {
                return false;
            }
        });
    });
</script>
