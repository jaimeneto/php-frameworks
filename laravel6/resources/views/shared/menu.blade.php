@can('manage', 'App\User')
  <li class="nav-item{{ (strpos(Route::currentRouteName(), 'user.') === 0) ? ' active' : '' }}">
    <a class="nav-link" href="{{ route('user.index') }}">{{ __('Users') }}</a>
  </li>
@endcan

@can('manage', 'App\Post')
  <li class="nav-item{{ (strpos(Route::currentRouteName(), 'post.') === 0) ? ' active' : '' }}">
    <a class="nav-link" href="{{ route('post.index') }}">{{ __('Posts') }}</a>
  </li>
@endcan

@can('manage', 'App\Comment')
  <li class="nav-item{{ (strpos(Route::currentRouteName(), 'comment.') === 0) ? ' active' : '' }}">
    <a class="nav-link" href="{{ route('comment.index') }}">{{ __('Comments') }}</a>
  </li>
@endcan