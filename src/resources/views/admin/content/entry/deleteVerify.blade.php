<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/attention.png') !!}"> Are you sure?</h1>
    </div>
    <div class="content">
        The following entries will be deleted.
        <br />
        <br />
        {!! Form::open(['id' => 'form', 'url' => Admin::url('content/entry/delete')]) !!}
            <table class="list">
                <thead>
                    <tr>
                        <th width="220">Title</th>
                        <th>Content Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entries as $entry)
                        <tr>
                            <td>
                                <input type="hidden" name="selected[]" value="{!! $entry->id !!}" />
                                {!! $entry->title !!}
                            </td>
                            <td>{!! $entry->contentType->title !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="buttons">
                <a class="button" href="{!! Admin::url('content/entry') !!}"><span>Cancel</span></a>
                <a class="button" href="javascript:void(0)" onClick="$('#form').submit();"><span>Delete</span></a>
            </div>
        {!! Form::close() !!}
    </div>
</div>