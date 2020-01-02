@extends('layouts.app')
 
@section('content')
<div class="container">
 
  <div class="blog-header">
      <h2 class="blog-title">{{ __('Users') }}</h2>
      <hr>
  </div>
 
  @include('shared.messages')
 
  <table class="table table-hover">
    <thead class="thead-dark">
      <tr>
        <th>{{ __('Id') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Email') }}</th>
        <th>{{ __('Posts') }}</th>
        <th>{{ __('Comments') }}</th>
        <th>{{ __('Created at') }}</th>
        <th>{{ __('Email verified at') }}</th>
        <th>{{ __('Accessed at') }}</th>
        <th>{{ __('Type') }}</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $user)
      <tr class="{{ $user->trashed() ? 'text-muted': ''}}">
        <td>{{ $user->id }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->posts_count }}</td>
        <td>{{ $user->comments_count }}</td>
        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
        <td>{{ $user->hasVerifiedEmail()
            ? $user->email_verified_at->format('d/m/Y H:i')
            : '' }}</td>
        <td>{{ $user->accessed_at 
            ? $user->accessed_at->format('d/m/Y H:i') 
            : '' }}</td>
        <td>{{ $user->role }}</td>
 
        {{-- Botões de ação --}}
        <td class="text-right text-nowrap">
          @can('changeRole', $user)
            <form method="POST" class="d-inline" action=
              "{{ route('user.turnIntoAdmin',[$user->id]) }}"
              onsubmit="return confirm(
                '{{ __('Turn user :name into admin?', 
                ['name' => $user->name]) }}')">
              @csrf
              <input type="hidden" name="_method" value="PUT">
              <button class="btn btn-sm btn-dark" type="submit"
                >{{ __('Turn into admin') }}</button>
            </form>
          @endcan
 
          @can('delete', $user)
            <form method="POST" class="d-inline" action=
              "{{ route('user.destroy',[$user->id])}}"
              onsubmit="return confirm(
                '{{ __('Send user :name to trash?', 
                ['name' => $user->name]) }}')">
              @csrf
              <input type="hidden" name="_method" value="DELETE">
              <button class="btn btn-sm btn-danger" type="submit"
                >{{ __('Delete') }}</button>
            </form>
          @endcan
 
          @can('restore', $user)
              <form method="POST" class="d-inline"
                action="{{ route('user.restore',[$user->id])}}"
                onsubmit="return confirm('{{ __('Restore user :name?', 
                  ['name' => $user->name]) }}')">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <button class="btn btn-sm btn-warning" type="submit"
                  >{{ __('Restore') }}</button>
              </form>
          @endcan
 
          @can('forceDelete', $user)
            <form method="POST" class="d-inline"
            action="{{ route('user.destroy', [$user->id, true]) }}"
                onsubmit="return confirm(
                  '{{ __('Delete user :name permanently?', 
                  ['name' => $user->name]) }}')">
                @csrf
                <input type="hidden" name="_method" value="DELETE">
                <button class="btn btn-sm btn-danger" type="submit"
                  >{{ __('Destroy') }}</button>
            </form>
            @endcan
        </td>
 
      </tr>
      @endforeach
    </tbody>
  </table>
 
  {{-- Paginação --}}
  <div class="card-footer">{{ $users }}</div>
</div>
@endsection
