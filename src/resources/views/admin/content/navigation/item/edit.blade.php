<div class="box">
    <div class="heading">
        <?php if ( ! empty($item)): ?>
            <h1><img alt="" src="{!! Theme::asset('images/category.png') !!}">Edit Navigation Item - {!! $item->title !!}</h1>
        <?php else: ?>
            <h1><img alt="" src="{!! Theme::asset('images/category.png') !!}">Add Navigation Item</h1>
        <?php endif; ?>

        <div class="buttons">
            <a class="button" href="javascript:void(0);" onClick="$('#form').submit();"><span>Save</span></a>
            <a class="button" href="{!! Admin::url('content/navigation') !!}"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">
        {!! Form::model($item, array('id' => 'form')) !!}
        <div id="tabs">
            <ul class="htabs">
                <li><a href="#item-tab">Item</a></li>
                <li><a href="#advanced-tab">Advanced</a></li>
            </ul>
            <div id="item-tab">
                <div class="form">
                    <div>
                        <label for="type"><span class="required">*</span> Link Type:</label>
                        {!! Form::select('type', ['page' => 'Page', 'url' => 'URL'], null, ['id' => 'type']) !!}
                    </div>

                    <div class="page_div">
                        <label for="entry_id"><span class="required">*</span> Entry Page:</label>
                        {!! Form::select('entry_id', ['' => ''] + $entries->lists('title', 'id')->all()) !!}
                    </div>

                    <div>
                        <label for="title"><span class="required url_div">*</span> Link Text:<span class="help page_div">Leave blank to use the page title</span></label>
                        {!! Form::text('title') !!}
                    </div>

                    <div class="url_div">
                        <label for="url"><span class="required">*</span> URL:</label>
                        {!! Form::text('url') !!}
                    </div>

                    <div>
                        <label for="subnav_visibility"><span class="required">*</span> Subnavigation:<span class="help">Show/Hide children of this item.</span></label>
                        {!! Form::select('children_visibility_id', ['1' => 'Always Show', '2' => 'Only Show If A Current Item Ancestor', '3' => 'Never Show']) !!}
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

        $( "#tabs" ).tabs();

    });
</script>