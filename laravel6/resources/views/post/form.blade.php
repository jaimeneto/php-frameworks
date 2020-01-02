@extends('layouts.app')

@section('content')
<div class="container">

  <div class="blog-header">
      <h2 class="blog-title">
        {{ $post->id ? __('Edit Post') : __('Create Post') }}
      </h2>
      <hr>
  </div>

  @include('shared.messages')

  <form method="POST" action="{{ $post->id
    ? route('post.update',[$post->id])
    : route('post.store') }}">
  @csrf
  @if ($post->id)
    <input type="hidden" name="_method" value="PUT">
  @endif

  <div class="form-group">
    <label for="title">{{ __('Title') }}: </label>
    <input type="text" name="title" id="title"
      value="{{ old('title', $post->title) }}"
      class="form-control{{ $errors->has('title')
        ? ' is-invalid' : '' }}">
    @if ($errors->has('title'))
      <div class="invalid-feedback">
        {{ $errors->first('title') }}
      </div>
    @endif
  </div>

  <div class="form-group">
    <label for="text">{{ __('Text') }}: </label>
    <textarea name="text" rows="10" id="text"
      class="form-control{{ $errors->has('text')
        ? ' is-invalid' : '' }}"
      >{{ old('text', $post->text) }}</textarea>
    @if ($errors->has('text'))
      <div class="invalid-feedback">
        {{ $errors->first('text') }}
      </div>
    @endif
  </div>
      
  <div class="form-group row">
    <div class="col-md-6">
      <button class="form-control btn btn-primary" 
        type="submit">{{ __('Save') }}</button>
      </div>
      <div class="col-md-6">
        <a href="{{ route('post.index') }}" 
          class="form-control btn btn-outline-secondary"
          >{{ __('Cancel') }}</a>
      </div>
</form>
</div>
@endsection