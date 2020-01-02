@extends('layouts.app')

@section('content')
<div class="container">

  <div class="blog-header">
      <h2 class="blog-title">{{ __('Comments') }}</h2>
      <hr>
  </div>

  @include('shared.messages')

  <table class="table table-hover">
    <thead class="thead-dark">
      <tr>
        <th>{{ __('Id') }}</th>
        <th>{{ __('Post title') }}</th>
        <th>{{ __('Comments') }}</th>
        <th>{{ __('Author') }}</th>
        <th>{{ __('Created at') }}</th>
        <th>{{ __('Approved at') }}</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach($comments as $comment)
      <tr>
        <td>{{ $comment->id }}</td>
        <td><a href="{{ route('post.show',[$comment->post->id]) }}" 
          target="_blank">{{ $comment->post->title }}</a></td>
        <td>{{ $comment->text }}</td>
        <td>{{ $comment->user->name }}</td>
        <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
        <td>{{ $comment->isApproved()
          ? $comment->approved_at->format('d/m/Y H:i')
          : '' }}</td>

        {{-- Botões de ação --}}
        <td class="text-right text-nowrap">
          @can('approve', $comment)
          <form method="POST" class="d-inline"                 
            action="{{ route('comment.approve', [$comment->id]) }}"
            onsubmit="return confirm('{{ __('Approve comments?') }}')">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <button class="btn btn-sm btn-primary" type="submit"
              >{{ __('Approve') }}</button>
          </form>
          @endcan

          @can('delete', $comment)
          <form method="POST" class="d-inline"                 
            action="{{ route('comment.destroy', [$comment->id]) }}"
            onsubmit="return confirm('{{ __('Delete comments?') }}')">
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
  <div class="card-footer">{{ $comments }}</div>
</div>
@endsection
