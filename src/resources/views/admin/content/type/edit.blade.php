@include('cmscanvas::admin.content.type.subnav')

<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/layout.png') !!}">Content Type Edit - <?php echo $contentType->title; ?> (<?php echo $contentType->short_name; ?>)</h1>

        <div class="buttons">
            <a class="button" href="javascript:void(0);" id="save"><span>Save</span></a>
            <a class="button" href="javascript:void(0);" id="save_exit"><span>Save &amp; Exit</span></a>
            <a class="button" href="{!! Admin::url('content/type') !!}"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">

        {!! Form::model($contentType, array('id' => 'layout_edit')) !!}
        <div>
            <table class="list">
                <thead>
                    <tr>
                        <th width="220">Fields</th>
                        <th>Short Tag</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Entry Title</td>
                        <td>
                            @{{ title }}
                        </td>
                    </tr>
                    @foreach ($contentType->fields as $field)
                        <tr>
                            <td>{!! $field->label !!}</td>
                            <td>{{ <?php echo $field->short_tag; ?> }}</a></div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div id="tabs">
                <ul class="htabs">
                    <li><a href="#html-tab">Markup</a></li>
                    <li><a href="#page-head-tab">Page &lt;head&gt;</a></li>
                    <li><a href="#revisions-tab">Revisions</a></li>
                    <li><a href="#settings-tab">Settings</a></li>
                    <li><a href="#permissions-tab">Permissions</a></li>
                </ul>

                <div id="html-tab">
                    {!! Form::textarea('layout', null, array('id' => 'layout')) !!}
                </div>

                <div id="page-head-tab">
                    <p class="info">Include custom JavaScript, CSS, and/or meta information in the <strong>&lt;head&gt;</strong> block of this content type's pages.</p>

                    {!! Form::textarea('page_head', null, array('id' => 'page_head')) !!}
                </div>

                <div id="revisions-tab">
                    <table class="list">
                        <thead>
                            <tr>
                                <th>Revision</th>
                                <th>Author</th>
                                <th>Date</th>
                                <th class="right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            @if (! empty($contentType) && count($contentType->revisions) > 0)
                                @foreach ($contentType->revisions as $revisionIteration)
                                <tr>
                                    <td>{!! substr(sha1($revisionIteration->id), 0, 7) !!}</td>
                                    <td>
                                        @if ($revisionIteration->author != null)
                                            <img src="{{ $revisionIteration->author->avatar(30, 30) }}" class="avatar_20" />
                                            <a target="_blank" href="{!! Admin::url('user/'.$revisionIteration->author->id.'/profile') !!}">{!! $revisionIteration->author->getFullName() !!}</a>
                                        @else
                                            {!! $revisionIteration->author_name !!}
                                        @endif
                                    </td>
                                    <td>{!! Content::userDate($revisionIteration->created_at) !!}</td>
                                    <td class="right">
                                        @if (($revision == null && $i == 0)
                                            || ($revision != null && $revision->id == $revisionIteration->id))
                                            <strong>Currently Loaded</strong>
                                        @elseif ($i == 0)
                                            [ <a href="{!! Admin::url("content/type/{$revisionIteration->content_type_id}/edit") !!}">Load Revision</a> ]
                                        @else
                                            [ <a href="{!! Admin::url("content/type/{$revisionIteration->content_type_id}/edit/revision/{$revisionIteration->id}") !!}">Load Revision</a> ]
                                        @endif
                                    </td>
                                </tr>
                                <?php $i++; ?>
                                @endforeach
                            @else
                                <tr class="center">
                                    <td colspan="4">No revisions found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div id="settings-tab">
                    <div class="form">
                        <div>
                            <label for="title"><span class="required">*</span> Title:</label>
                            {!! Form::text('title') !!}
                        </div>
                        <div>
                            <label for="short_name"><span class="required">*</span> Short Name:</label>
                            {!! Form::text('short_name') !!}
                        </div>
                        <div>
                            <label for="theme_layout"><span class="required">*</span> Theme Layout:</label>
                            {!! Form::select('theme_layout', ['' => '-- None --'] + $themeLayouts) !!}
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
                            {!! Form::text('route', null, ['style' => 'width: 500px;']) !!}
                        </div>
                        <div>
                            {!! HTML::decode(Form::label('entry_uri_template', 'Entry URI Template:<span class="help">Optional template used to dynamically generate URIs for entries of this content type. The URI template should generally match a content type\'s route pattern.</span>')) !!}
                            {!! Form::text('entry_uri_template', null, ['style' => 'width: 500px;']) !!}
                        </div>
                        <div>
                            {!! HTML::decode(Form::label('entries_allowed', 'Number of Entries Allowed:<span class="help">Number of entries allowed to be created with this content type.</span>')) !!}
                            {!! Form::text('entries_allowed', null, array('class' => 'short')) !!}
                            <span class="ex">Leave empty for unlimited</span>
                        </div>
                        <div>
                            {!! HTML::decode(Form::label('max_revisions', 'Max Revisions:<span class="help">Max number of revisions to store for each entry</span>')) !!}
                            {!! Form::text('max_revisions', null, array('class' => 'short')) !!}
                            <span class="ex">No revisions will be kept if left empty</span>
                        </div>
                        <div>
                            {!! HTML::decode(Form::label('media_type_id', 'Media Type:<span class="help">Specifies output media type by setting the Content-Type header when rendered as a page.</span>')) !!}
                            {!! Form::select(
                                'media_type_id', 
                                ['' => 'Default'] + $mediaTypes->pluck('name', 'id')->all()
                            ) !!}
                        </div>
                    </div>
                </div>

                <div id="permissions-tab">
                    <div class="form">
                        <fieldset>
                            <legend>Admin Entry Permissions</legend>
                            <div>
                                <label for="title">View:</label>
                                {!! Form::select('admin_entry_view_permission_id', ['' => '-- None --'] + $permissions->pluck('name', 'id')->all()) !!}
                            </div>
                            <div>
                                <label for="title">Edit:</label>
                                {!! Form::select('admin_entry_edit_permission_id', ['' => '-- None --'] + $permissions->pluck('name', 'id')->all()) !!}
                            </div>
                            <div>
                                <label for="title">Create:</label>
                                {!! Form::select('admin_entry_create_permission_id', ['' => '-- None --'] + $permissions->pluck('name', 'id')->all()) !!}
                            </div>
                            <div>
                                <label for="title">Delete:</label>
                                {!! Form::select('admin_entry_delete_permission_id', ['' => '-- None --'] + $permissions->pluck('name', 'id')->all()) !!}
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var editor = CodeMirror.fromTextArea(document.getElementById("layout"), {
            lineNumbers: true,
            matchBrackets: true,
            mode: {name: "twig", htmlMode: true},
            indentUnit: 4,
            indentWithTabs: true,
            enterMode: "keep",
            tabMode: "shift",
            theme: "pastel-on-dark"
        });

        var editor = CodeMirror.fromTextArea(document.getElementById("page_head"), {
            lineNumbers: true,
            matchBrackets: true,
            mode: {name: "twig", htmlMode: true},
            indentUnit: 4,
            indentWithTabs: true,
            enterMode: "keep",
            tabMode: "shift",
            theme: "pastel-on-dark"
        });

        $( "#tabs" ).tabs();

        // Save Content
        $("#save, #save_exit").click( function() {

            if ($(this).attr('id') == 'save_exit') {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'save_exit',
                    value: '1'
                }).appendTo('#layout_edit');

                $('#layout_edit').submit();
            } else {
                $('#layout_edit').submit();
            }
        });
    });
</script>
