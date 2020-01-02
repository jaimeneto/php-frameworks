@extends('layouts.app')

@section('content')
<div class="container">

   @include('shared.messages')

   <div class="card">
       <h3 class="card-header">{{ $post->title }}</h3>
       <blockquote class="card-body lead text-justify">
           <p class="card-text">{{ $post->text }}</p>
           <div class="text-right">
               <strong>{{ $post->user->name }}</strong>
               <small class="text-muted">&minus;
                   {{ $post->created_at->format('d/m/Y H:i') }}
               </small>
           </div>
        </blockquote>
   </div>

   <br>
    @include('comment.form')

</div>
@endsection