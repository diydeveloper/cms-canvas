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

        @if ( ! empty($entry) && $entry->route != '')
            <a style="float: right; z-index: 1; position: relative;" target="_blank" href="{!! url($entry->route) !!}"><img src="{!! Theme::asset('images/preview-icon-medium.png') !!}" /></a>
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
                        <span style="line-height: 24px; "> {!! url() !!}{!! $contentType->getRoutePrefix() !!}/ </span>
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

            <!-- Settings Tab -->
            <div id="settings-tab">
                <div class="form">
                    <div>
                        {!! HTML::decode(Form::label('entry_status_id', '<span class="required">*</span> Status:')) !!}
                        {!! Form::select('entry_status_id', $entryStatuses->lists('name', 'id')) !!}
                    </div>
                    <div>
                        {!! HTML::decode(Form::label('created_at', '<span class="required">*</span> Date Created:')) !!}
                        {!! Form::text(
                            'created_at', 
                            ( ! empty($entry->created_at)) ? 
                                $entry->created_at->setTimezone(Auth::user()->getTimezoneIdentifier())->format('m/d/Y h:i:s a') 
                            : 
                                Carbon::now()->setTimezone(Auth::user()->getTimezoneIdentifier())->format('m/d/Y h:i:s a'), 
                            array('class' => 'datetime')) 
                        !!}
                    </div>
                    <div>
                        {!! Form::label('author_id', 'Author:') !!}
                        {!! Form::select('author_id', $authorOptions, ( ! empty($entry->author_id) ? $entry->author_id : Auth::user()->id)) !!}
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
            ampm: true
        });

        // Wrap datepicker popup with a class smoothness for styleing
        $('body').find('#ui-datepicker-div').wrap('<div class="smoothness"></div>');

        $("#save, #save_exit").click( function() {
            if ($(this).attr('id') == 'save_exit')
            {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'save_exit',
                    value: '1'
                }).appendTo('#entry_edit');

                $('#entry_edit').submit();
            }
            else
            {
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
            if($(this).next('div').is(":visible"))
            {
                $(this).next('div').slideUp();
                $('div', this).removeClass('arrow_expand').addClass('arrow_collapse');
            }
            else
            {
                $(this).next('div').slideDown();
                $('div', this).removeClass('arrow_collapse').addClass('arrow_expand');
            }
        });

        @if ( ! empty($entry))
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
    });
</script>