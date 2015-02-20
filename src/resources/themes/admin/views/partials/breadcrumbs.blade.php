@if (!empty($breadcrumbs))
    <ul>
        @if (in_array(Route::currentRouteName(), array('admin.index', 'admin.dashboard')))
            <li><span class="first_crumb current">Dashboard</span></li>
        @else
            <li><a class="first_crumb" href="{{ route('admin.index') }}">Dashboard</a></li>
        @endif

        @foreach($breadcrumbs as $uri => $crumb)
            @if (Request::is($uri))
                <li><span class="current">{{ $crumb }}</span></li>
            @else
                <li><a href="{{ Admin::url($uri) }}">{{ $crumb }}</a></li>
            @endif
        @endforeach
    </ul>
    <div class="clear"></div>
@endif