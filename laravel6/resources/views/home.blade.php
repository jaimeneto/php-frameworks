@extends('layouts.app')
 
@section('content')
<div class="container">
 <div class="card-deck">
 @php ($i = 0) @endphp
 @foreach($posts as $post)
   @if ($i++ % 2 === 0)
</div><br><div class="card-deck">
   @endif
   <div class="card">
     <div class="card-header">{{ $post->title }}</div>
     <div class="card-body">
       <p class="card-text">
         {{ Str::words($post->text, 50, ' [...]') }}
       </p>
       <a href="{{ route('post.show', [$post->id]) }}"
         class="btn btn-sm btn-primary float-right"
         >{{ __('Read more') }}</a>
       <p class="card-text">
         <small class="text-muted">
           <strong>{{ $post->user->name }}</strong>
           &minus; {{ $post->created_at->format('d/m/Y H:i') }}
           &minus; {{ trans_choice(':count comments', 
                      $post->comments_count) }}
         </small>
       </p>
     </div>
   </div>
 @endforeach
 </div>
 
 <br>
 {{-- Paginação --}}
 <div class="float-right">{{ $posts }}</div>
      
</div>
@endsection
