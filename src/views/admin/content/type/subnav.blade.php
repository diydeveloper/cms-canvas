<div class="subnav">
    <ul>
        <li><a {{ (Request::segment(5) == 'edit') ? 'class="selected"' : '' }} href="{{ Admin::url("/content/type/{$contentType->id}/edit/") }}"><span>Edit Content Type</span></a></li>
        <li><a {{ (Request::segment(5) == 'field') ? 'class="selected"' : '' }} href="{{ Admin::url("/content/type/{$contentType->id}/field/") }}"><span>Fields</span></a></li>
    </ul>
    <div class="clear"></div>
</div>
