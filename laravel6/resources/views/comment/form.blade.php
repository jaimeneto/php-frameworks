<div class="card">
 <div class="card-header">{{ __('Comments') }}</div>
 <div class="card-body">
   @if (count($post->comments) > 0)
     @foreach($post->comments as $comment)
     @can('view', $comment)
     <div class="card-text">
       <div>
         <strong>{{ $comment->user->name }}</strong>
         <small class="text-muted"> &minus;
           {{ $comment->created_at->format('d/m/Y H:i') }}
         </small>
 
         @if (!$comment->isApproved())
           <span class="badge badge-danger"
             >{{ __('Pending Approval') }}</span>
         @endif
 
         <div class="float-right">
           @can('approve', $comment)
             <form method="POST" style="margin:-7px 0 2px"
              action="{{ route('comment.approve', [$comment->id]) }}"
              onsubmit="return confirm('{{ __('Approve comments?') }}')">
              @csrf
              <input type="hidden" name="_method" value="PUT">
              <button type="submit" 
              class="btn btn-sm form-control form-control-sm btn-success"
                 >{{ __('Approve') }}</button>
             </form>
           @endcan
 
           @can('delete', $comment)
             <form method="POST"
               action="{{ route('comment.destroy', [$comment->id]) }}"
               onsubmit="return confirm('{{ __('Delete comments?') }}')">
               @csrf
               <input type="hidden" name="_method" value="DELETE">
               <button type="submit" 
               class="btn btn-sm form-control form-control-sm btn-danger"
                 >{{ __('Delete') }}</button>
             </form>
           @endcan
         </div>
       </div>
       <div>{{ $comment->text }}</div>
       <hr>
     </div>
     @endcan
     @endforeach
     @else
       <div class="card-text text-center">
         {{ __('No comments so far. Be the first!') }}
       </div>
     @endif
 </div>
 
 <div class="card-footer">
   @can('create', 'App\Comment')
     <form action="{{ route('comment.store') }}" method="POST">
       @csrf
       <input type="hidden" name="post_id" value="{{ $post->id }}">
       <div class="form-group">
         <textarea name="text" id="text"
           placeholder="{{ __('Enter your comments here') }}"
           class="form-control{{ $errors->has('text')
             ? ' is-invalid' : '' }}"
           >@if ($errors->has('text'))
           {{ old('text') }}
           @endif</textarea>
         @if ($errors->has('text'))
           <div class="invalid-feedback">
               {{ $errors->first('text') }}
           </div>
         @endif
       </div>
       <button class="btn btn-primary" type="submit"
        >{{ __('Save') }}</button>
       <button class="btn btn-light" type="reset"
        >{{ __('Cancel') }}</button>
     </form>
   @else
     <br>
     <p class="text-center">
      {{ __('You need to login to add your comments!') }}
     </p>
   @endcan
 </div>
</div>