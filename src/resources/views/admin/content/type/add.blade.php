<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/layout.png') !!}">Add Content Type</h1>

        <div class="buttons">
            <a class="button" href="javascript:void(0);" onClick="$('#layout_add').submit();"><span>Create</span></a>
        </div>
    </div>
    <div class="content">

        <div class="form">
            {!! Form::open(array('id' => 'layout_add')) !!}
            <div>
                {!! HTML::decode(Form::label('title', '<span class="required">*</span> Title:')) !!}
                {!! Form::text('title') !!}
            </div>
            <div>
                {!! HTML::decode(Form::label('short_name', '<span class="required">*</span> Short Name:<span class="help">Identifier containing no spaces</span>')) !!}
                {!! Form::text('short_name') !!}
            </div>
            <div>
                {!! HTML::decode(Form::label('theme_layout', '<span class="required">*</span> Theme Layout')) !!}
                {!! Form::select('theme_layout', ['' => '-- None --'] + $themeLayouts, $defaultThemeLayout) !!}
            </div>
            <div>
                {!! HTML::decode(Form::label('entry_route_prefix', 'Entry Route Prefix:<span class="help">Optional prefix to prepend to entry routes.</span>')) !!}
                {!! Form::text('entry_route_prefix', null, ['style' => 'width: 250px;']) !!}
            </div>
            <div>
                {!! HTML::decode(Form::label('url_title_flag', 'Entry URL Title: <span class="help">Adds a URL title field to entries of this content type that can be used for SEO friendly routing.</span>')) !!}
                <span>
                    <label>{!! Form::radio('url_title_flag', '1') !!} Enabled</label>
                    <label>{!! Form::radio('url_title_flag', '0', true) !!} Disabled</label>
                </span>
            </div>
            <div>
                {!! HTML::decode(Form::label('route', 'Route:<span class="help">Optional route to render the content type directly without an entry.</span>')) !!}
                {!! Form::text('route') !!}
            </div>
            <div>
                {!! HTML::decode(Form::label('entry_uri_template', 'Entry URI Template:<span class="help">Optional template used to dynamically generate URIs for entries of this content type. The URI template should generally match a content type\'s route pattern.</span>')) !!}
                {!! Form::text('entry_uri_template') !!}
            </div>
            <div>
                {!! HTML::decode(Form::label('entries_allowed', 'Number of Entries Allowed:<span class="help">Number of entries allowed to be created with this content type</span>')) !!}
                {!! Form::text('entries_allowed', null, array('class' => 'short')) !!}
                <span class="ex">Leave blank for unlimited</span>
            </div>
            <div>
                {!! HTML::decode(Form::label('max_revisions', 'Max Revisions:<span class="help">Max number of revisions to store for each entry</span>')) !!}
                {!! Form::text('max_revisions', null, array('class' => 'short')) !!}
                <span class="ex">No revisions will be stored if left empty</span>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        // Auto fill short name based on title
        $('#title').keyup( function(e) {
            $('#short_name').val($(this).val().toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9\-_]/g, ''))
        });
    });
</script>
