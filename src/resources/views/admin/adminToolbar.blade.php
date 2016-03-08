<div id="admin-toolbar" style="display: none;">
    <ul class="admin-toolbar-left">
        @if ($resource instanceof \CmsCanvas\Models\Content\Entry)
            <li><a target="ee_admin" class="admin-toolbar-top" href="{{ Admin::url('/content/type/'.$resource->contentType->id.'/entry/'.$resource->id.'/edit') }}">Edit Page</a></li>
        @else
            <li><a target="ee_admin" class="admin-toolbar-top" href="{{ Admin::url('/content/type/'.$resource->id.'/edit') }}">Edit Content Type</a></li>
        @endif
        <li>
            <a class="admin-toolbar-top" href="{{ Admin::url('/content/entries') }}">
                Content 
                &nbsp;<img src="{{ Theme::asset('js/admin_toolbar/images/down-triangle.gif', 'admin') }}" />
            </a>
            <ul>
                <li><a href="{{ Admin::url('/content/entry') }}">Entries</a></li>
                <li><a href="{{ Admin::url('/content/type') }}">Content Types</a></li>
                <li><a href="{{ Admin::url('/content/navigation') }}">Navigations</a></li>
            </ul>
        </li>
    </ul>
    <ul class="admin-toolbar-right">
        <?php if (Admin::isInlineEditingEnabled()): ?>
            <li><span id="admin-save-status" class="admin-toolbar-top"></span></li>
            <li><a id="admin-save-changes" class="admin-toolbar-top" href="javascript:void(0);">Save Changes</a></li>
        <?php endif; ?>
        <li><a id="admin-settings-icon" class="admin-toolbar-top" href="javascript:void(0);"></a>
            <ul>
                <li>
                    <a id="admin-toggle-inline-editing" href="javascript:void(0);">
                        {{ (auth()->user()->enable_inline_editing) ? 'Disable' : 'Enable' }} Inline Editing
                    </a>
                </li>
                <li><a href="{{ Admin::url('/user/logout') }}">Sign Out</a></li>
            </ul>
        </li>
    </ul>
    <div id="admin-toolbar-shadow"></div>
</div>