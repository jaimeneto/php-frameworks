@extends('layouts.app')

@section('content')
<div class="container">

  <div class="blog-header">
    <a class="btn btn-primary btn-lg float-right"
          href="{{ route('post.create') }}">{{ __('Create') }}</a>
    <h2 class="blog-title">{{ __('Posts') }}</h2>
    <hr>
  </div>

  @include('shared.messages')

  <table class="table table-hover">
    <thead class="thead-dark">
      <tr>
        <th>{{ __('Id') }}</th>
        <th>{{ __('Title') }}</th>
        <th>{{ __('Author') }}</th>
        <th>{{ __('Comments') }}</th>
        <th>{{ __('Created at') }}</th>
        <th>{{ __('Updated at') }}</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach($posts as $post)
      <tr>
        <td>{{ $post->id }}</td>
        <td>{{ $post->title }}</td>
        <td>{{ $post->user->name }}</td>
        <td>{{ $post->comments_count }}</td>
        <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
        <td>{{ $post->updated_at->format('d/m/Y H:i') }}</td>

        {{-- Botões de ação --}}
        <td class="text-right text-nowrap">
          @can('view', $post)
            <a href="{{ route('post.show',[$post->id]) }}"
              class="btn btn-sm btn-success">{{ __('Show') }}</a>
          @endcan

          @can('update', $post)
            <a href="{{ route('post.edit',[$post->id]) }}"
              class="btn btn-sm btn-dark">{{ __('Edit') }}</a>
          @endcan

          @can('delete', $post)
            <form method="POST" class="d-inline" action=
              "{{ route('post.destroy',[$post->id]) }}"
              onsubmit="return confirm('{{ __('Delete post?') }}?')">
              @csrf
              <input type="hidden" name="_method" value="DELETE">
              <button class="btn btn-sm btn-danger" type="submit"
                >{{ __('Delete') }}</button>
            </form>
          @endcan
        </td>

      </tr>
      @endforeach
    </tbody>
  </table>

  {{-- Paginação --}}
  <div class="card-footer">{{ $posts }}</div>

</div>
@endsection
