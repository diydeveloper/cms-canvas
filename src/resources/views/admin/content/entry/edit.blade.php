<!--[if lte IE 7]> <style type="text/css"> #entry_fields > div > label .arrow { display: inline; } </style> <![endif]-->

<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/review.png') !!}"> 
            @if ( ! empty($entry))
                Edit Entry - {!! strip_tags($entry->title) !!} (#{!! $entry->id !!})
            @else
                Add Entry
            @endif
        </h1>

        <div class="buttons">
            <a class="button" href="javascript:void(0);" id="save"><span>Save</span></a>
            <a class="button" href="javascript:void(0);" id="save_exit"><span>Save &amp; Exit</span></a>
            <a class="button" href="{!! Admin::url('content/entry') !!}"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">

        @if ( ! empty($entry) && $entry->getUri() != '')
            <a style="float: right; z-index: 1; position: relative;" target="_blank" href="{!! url($entry->getUri()) !!}"><img src="{!! Theme::asset('images/preview-icon-medium.png') !!}" /></a>
        @endif

        <div class="fright" style="margin-top: 4px; margin-right: 10px;">
            <a id="collapse_all" class="no_underline" href="javascript:void(0);">Collapse All</a> &nbsp;|&nbsp; <a id="expand_all" class="no_underline" href="javascript:void(0);">Expand All</a>
        </div>

        @if ( ! empty($entry))
            {!! Form::model($entry, array('id' => 'entry_edit')) !!}
        @else
            {!! Form::open(array('id' => 'entry_edit')) !!}
        @endif
        <div class="tabs">
            <ul class="htabs">
                <li><a href="#content-tab">Content</a></li>
                <li><a href="#page-tab">Page</a></li>
                @if ($contentType->max_revisions > 0)
                <li><a href="#revisions-tab">Revisions</a></li>
                @endif
                <li><a href="#settings-tab">Settings</a></li>
            </ul>
            <!-- Content Tab -->
            <div id="content-tab">
                <div id="entry_fields">
                    <div>
                        {!! HTML::decode(Form::label('title', '<div class="arrow arrow_expand"></div><span class="required">*</span> Title')) !!}
                        <div>
                            {!! Form::text('title') !!}
                        </div>
                    </div>
                    @if ($contentType->url_title_flag)
                    <div>
                        {!! HTML::decode(Form::label('url_title', '<div class="arrow arrow_expand"></div><span class="required">*</span> URL Title')) !!}
                        <div>
                            {!! Form::text('url_title') !!}
                        </div>
                    </div>
                    @endif

                    @if ( ! empty($fieldViews))
                        @foreach ($fieldViews as $fieldView)
                            {!! $fieldView !!}
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Page Tab -->
            <div id="page-tab">
                <div class="form">
                    <div>
                        {!! Form::label('route', 'Route:') !!}
                        @if ($contentType->entry_route_prefix !== null && $contentType->entry_route_prefix !== '')
                            <span style="line-height: 24px; ">{!! url('/').'/'.$contentType->entry_route_prefix !!}/</span>
                        @else
                            <span style="line-height: 24px; ">{!! url('/') !!}/</span>
                        @endif
                        {!! Form::text('route') !!}
                    </div>
                    <div>
                        {!! HTML::decode(Form::label('meta_title', 'Meta Title:<br /><span class="help">65 Characters Max</span>')) !!}
                        {!! Form::text('meta_title') !!}
                        &nbsp;<span id="meta_title_count" class="help" style="display: inline;">(0 Chars)</span>
                    </div>
                    <div>
                        {!! HTML::decode(Form::label('meta_keywords', 'Keywords:<span class="help">250 Characters Max</span>')) !!}
                        {!! Form::textarea('meta_keywords', null, array('style' => 'height: 50px;')) !!}
                        &nbsp;<span id="meta_keywords_count" class="help" style="display: inline;">(0 Chars)</span>
                    </div>
                    <div>
                        {!! HTML::decode(Form::label('meta_description', 'Description:<br /><span class="help">150 Characters Max</span>')) !!}
                        {!! Form::textarea('meta_description', null) !!}
                        &nbsp;<span id="meta_description_count" class="help" style="display: inline;">(0 Chars)</span>
                    </div>
                </div>
            </div>

            <!-- Revisions Tab -->
            @if ($contentType->max_revisions > 0)
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
                        @if (! empty($entry) && count($entry->revisions) > 0)
                            @foreach ($entry->revisions as $revisionIteration)
                            <tr>
                                <td>{!! substr(sha1($revisionIteration->id), 0, 7) !!}</td>
                                <td>
                                    @if ($revisionIteration->author != null)
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
                                        [ <a href="{!! Admin::url("content/type/{$revisionIteration->content_type_id}/entry/{$entry->id}/edit") !!}">Load Revision</a> ]
                                    @else
                                        [ <a href="{!! Admin::url("content/type/{$revisionIteration->content_type_id}/entry/{$entry->id}/edit/revision/{$revisionIteration->id}") !!}">Load Revision</a> ]
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
            @endif

            <!-- Settings Tab -->
            <div id="settings-tab">
                <div class="form">
                    <div>
                        {!! HTML::decode(Form::label('entry_status_id', '<span class="required">*</span> Status:')) !!}
                        {!! Form::select('entry_status_id', $entryStatuses->pluck('name', 'id')->all()) !!}
                    </div>
                    <div>
                        {!! HTML::decode(Form::label('created_at', '<span class="required">*</span> Date Created:')) !!}
                        <div style="display: inline-block; vertical-align: middle;">
                            {!! Form::text(
                                'created_at', 
                                ( ! empty($entry->created_at)) ? 
                                    Content::userDate($entry->created_at)
                                :
                                    Content::userDate(\Carbon\Carbon::now()),
                                array('class' => 'datetime')) 
                            !!}<br />
                            @if (! empty($entry->created_at) && Auth::user()->getTimezoneIdentifier() != Config::get('cmscanvas::config.default_timezone'))
                                <span class="ex">Site Default Timezone: {{ Content::userDate($entry->created_at_local, null, false) }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        {!! Form::label('author_id', 'Author:') !!}
                        {!! Form::select('author_id', $authorOptions, ( ! empty($entry->author_id) ? $entry->author_id : Auth::user()->id)) !!}
                    </div>
                    <div>
                        {!! HTML::decode(Form::label('template_flag', 'Render as Template:<span class="help">Parse entry content for template tags</span>')) !!}
                        <input type="hidden" name="template_flag" value="0">
                        {!! Form::checkbox('template_flag') !!}
                    </div>
                    <div>
                        {!! Form::label('change_content_type_id', 'Content Type:') !!}
                        <span id="content_type_text">
                            {!! $contentType->title !!}
                            &nbsp;&nbsp;<a id="change_content_type" style="font-size: 10px;" href="javascript:void(0);">Change</a>
                        </span>

                        <span id="content_type_select" style="display: none;">
                            {!! Form::select('change_content_type_id', $availableContentTypes->pluck('title', 'id')->all(), $contentType->id) !!}
                            &nbsp;<a id="load_content_type" style="font-size: 10px;" href="#">Load</a> 
                            / 
                            <a id="cancel_change_content_type" style="font-size: 10px;" href="javascript:void(0);">Cancel</a
                        </span>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $( ".tabs" ).tabs();

        $( ".datetime" ).datetimepicker({
            showSecond: true,
            timeFormat: 'hh:mm:ss tt',
            ampm: true,
            dateFormat: 'd/M/yy'
        });

        // Wrap datepicker popup with a class smoothness for styleing
        $('body').find('#ui-datepicker-div').wrap('<div class="smoothness"></div>');

        $("#save, #save_exit").click( function() {
            if ($(this).attr('id') == 'save_exit') {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'save_exit',
                    value: '1'
                }).appendTo('#entry_edit');

                $('#entry_edit').submit();
            } else {
                $('#entry_edit').submit();
            }
        });

        // Count meta title characters
        $('#meta_title').keyup( function() {
            $('#meta_title_count').html('(' + $(this).val().length + ' Chars)');
        });
        $('#meta_title').trigger("keyup");

        // Count keyword characters
        $('#meta_keywords').keyup( function() {
            $('#meta_keywords_count').html('(' + $(this).val().length + ' Chars)');
        });
        $('#meta_keywords').trigger("keyup");

        // Count description characters
        $('#description_textarea').keyup( function() {
            $('#meta_description_count').html('(' + $(this).val().length + ' Chars)');
        });
        $('#description_textarea').trigger("keyup");

        // Expand / Collapse entry fields
        $('#entry_fields > div > label').click( function() {
            if($(this).next('div').is(":visible")) {
                $(this).next('div').slideUp();
                $('div', this).removeClass('arrow_expand').addClass('arrow_collapse');
            } else {
                $(this).next('div').slideDown();
                $('div', this).removeClass('arrow_collapse').addClass('arrow_expand');
            }
        });

        @if ($entry == null)
            // Auto Generate Url Title
            $('#title').keyup( function(e) {
                $('#url_title').val($(this).val().toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9\-_]/g, ''))
            });
        @endif

        heading_pos = $('.heading').offset().top;
        position_top = false;

        $(window).scroll(function () {
            if (heading_pos - $(window).scrollTop() <= 0) {
                if (!position_top) {
                    $('.heading').addClass('position_top');
                    $('.content').addClass('position_top');
                    position_top = true;
                }
            } else {
                if (position_top) {
                    $('.heading').removeClass('position_top');
                    $('.content').removeClass('position_top');
                    position_top = false;
                }
            }
        });

        $('#collapse_all').click( function() {
            $('.arrow_expand').trigger('click');
        });

        $('#expand_all').click( function() {
            $('.arrow_collapse').trigger('click');
        });

        $("#change_content_type").click(function() {
            $('#content_type_text').hide();
            $('#content_type_select').show();
        });

        $("#cancel_change_content_type").click(function() {
            $('#content_type_select').hide();
            $('#content_type_text').show();
        });

        $("#load_content_type").click(function() {
            if ($('#change_content_type_id').val() == '') {
                alert('No content type was selected.');
            } else {
                response = confirm('Changing the content type will only carry over content from fields with matching short tags in both content types.\nAny current unsaved data will be lost.\n\n Are you sure you want to continue?');

                if (response) {
                    window.location = ADMIN_URL + "/content/type/" + $('#change_content_type_id').val() + "/entry/{!! (! empty($entry)) ? $entry->id.'/edit' : 'add' !!}";
                }
            }
        });
    });
</script>