@if (!empty($breadcrumbs))
    <ul>
        @if (in_array(Route::currentRouteName(), array('admin.index', 'admin.dashboard')))
            <li class="breadcrumb-item"><span class="active">Dashboard</span></li>
        @else
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
        @endif

        @foreach($breadcrumbs as $uri => $crumb)
            @if (Request::is($uri))
                <li class="breadcrumb-item"><span class="current">{{ $crumb }}</span></li>
            @else
                <li class="breadcrumb-item"><a href="{{ Admin::url($uri) }}">{{ $crumb }}</a></li>
            @endif
        @endforeach
    </ul>
    <div class="clear"></div>
@endif
