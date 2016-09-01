<div class="box">
    <div class="heading">
        <?php if ( ! empty($item)): ?>
            <h1><img alt="" src="{!! Theme::asset('images/category.png') !!}">Edit Navigation Item - {!! $item->title !!}</h1>
        <?php else: ?>
            <h1><img alt="" src="{!! Theme::asset('images/category.png') !!}">Add Navigation Item</h1>
        <?php endif; ?>

        <div class="buttons">
            <a class="button" href="javascript:void(0);" onClick="$('#form').submit();"><span>Save</span></a>
            <a class="button" href="{!! Admin::url('content/navigation/'.$navigation->id.'/tree') !!}"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">
        {!! Form::model($item, ['id' => 'form']) !!}
        <div class="tabs">
            <ul class="htabs">
                <li><a href="#item-tab">Item</a></li>
                <li><a href="#advanced-tab">Advanced</a></li>
            </ul>
            <div id="item-tab">
                <div class="form">
                    <div>
                        <label for="title"><span class="required">*</span> Title:</label>
                        {!! Form::text('title') !!}
                    </div>

                    <div>
                        <label for="type"><span class="required">*</span> Link Type:</label>
                        {!! Form::select('type', ['page' => 'Page', 'url' => 'URL'], null, ['id' => 'type']) !!}
                    </div>

                    <div class="page_div">
                        <label for="entry_id"><span class="required">*</span> Entry Page:</label>
                        {!! Form::select('entry_id', ['' => ''] + $entries->pluck('title', 'id')->all()) !!}
                    </div>

                    <div>
                        <label for="title">Link Text:<span class="help">Leave blank to use the navigation item title</span></label>
                        <div style="display:inline-block; vertical-align: middle;">
                            <div class="tabs">
                                <ul class="htabs">
                                    @foreach ($languages as $language)
                                        <li>
                                            <a href="#translate-link_text_{!! $language->locale !!}">{!! $language->language !!}</a>
                                        </li>
                                    @endforeach
                                </ul>
                                @foreach ($languages as $language)
                                    <div id="translate-link_text_{!! $language->locale !!}">
                                        <?php $itemData = ((!empty($item)) ? $item->allData->getFirstWhere('language_locale', $language->locale) : null); ?>
                                        {!! Form::text(
                                            'link_text_'.$language->locale, 
                                            ((!empty($itemData)) ? $itemData->link_text : ''), 
                                            ['class' => 'link_text', ((!empty($item->use_entry_title_flag)) ? 'disabled' : '')]) 
                                        !!}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <input type="hidden" name="use_entry_title_flag" value="0">
                        <div class="page_div" style="display: inline-block; margin-left: 10px; border-left: 1px dashed #CCCCCC; padding: 10px;">
                            <span>
                                {!! Form::checkbox('use_entry_title_flag', 1, null, ['id' => 'use_entry_title_flag']) !!}
                                <label for="use_entry_title_flag">Use Entry Title</label>
                            </span>
                        </div>
                    </div>

                    <div class="url_div">
                        <label for="url"><span class="required">*</span> URL:</label>
                        {!! Form::text('url') !!}
                    </div>

                    <div>
                        <label for="subnav_visibility"><span class="required">*</span> Subnavigation:<span class="help">Show/Hide children of this item.</span></label>
                        {!! Form::select(
                            'children_visibility_id', 
                            [
                                $childrenVisibilityShow => 'Always Show', 
                                $childrenVisibilityCurrentBranch => 'Only Show If In Current Branch', 
                                $childrenVisibilityHide => 'Never Show'
                            ]
                        ) !!}
                    </div>

                    <div>
                        <label for="target_attribute"><span class="required">*</span> Target:</label>
                        {!! Form::select('target_attribute', ['' => 'Current Window', '_blank' => 'New Tab / Window (_blank)']) !!}
                    </div>

                    <div>
                        <label for="hidden_flag">Hide:<span class="help">Don't show this item in the navigation.</span></label>
                        <input type="hidden" name="hidden_flag" value="0">
                        {!! Form::checkbox('hidden_flag') !!}
                    </div>
                </div>
            </div>
            <div id="advanced-tab">
                <div class="form">
                    <div>
                        <label for="id_attribute">HTML ID Attribute:</label>
                        {!! Form::text('id_attribute') !!}
                    </div>

                    <div>
                        <label for="class_attribute">HTML Class Attribute:</label>
                        {!! Form::text('class_attribute') !!}
                    </div>

                    <div>
                        <label for="url">Current URI Regex Pattern:<span class="help">Optional regular expression pattern used to match URI requests to indicate the navigation item as a current item.</span></label>
                        {!! Form::text('current_uri_pattern') !!}
                    </div>

                     <div>
                        <label for="disable_current_flag">Disable Current:<span class="help">Don't allow this item to be marked as current.</span></label>
                        <input type="hidden" name="disable_current_flag" value="0">
                        {!! Form::checkbox('disable_current_flag') !!}
                     </div>

                     <div>
                        <label for="disable_current_ancestor_flag">Disable Current Trail:<span class="help">Don't allow this item to be marked with current ancestor.</span></label>
                        <input type="hidden" name="disable_current_ancestor_flag" value="0">
                        {!! Form::checkbox('disable_current_ancestor_flag') !!}
                     </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        $('#type').change(function() {
            if ($(this).val() == 'page')
            {
                $('.url_div').hide();    
                $('.page_div').show();    
            }
            else
            {
                $('.page_div').hide();    
                $('.url_div').show();    
            }
        });

        $('#type').trigger('change');

        $(".tabs").tabs();

        $("#use_entry_title_flag").change(function() {
            if ($("#use_entry_title_flag").prop("checked")) {
                $(".link_text").val('');
                $(".link_text").prop('disabled', true);
            } else {
                $(".link_text").prop('disabled', false);
            }
        })

    });
</script>