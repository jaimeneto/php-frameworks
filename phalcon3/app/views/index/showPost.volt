{{ content() }}

<div class="card">
    <h3 class="card-header">{{ post.getTitle()|e }}</h3>
    <blockquote class="card-body lead text-justify">
        <p class="card-text">{{ post.getText()|e }}</p>

        <div class="text-right">
            <strong>{{ post.getUser().getName() }}</strong>
            <small class="text-muted">
                &minus; {{ post.getCreatedAt('d/m/Y H:i') }}
            </small>
        </div>
    </blockquote>
</div>

{{ partial('comments/form', [
   'postId': post.getId(),
   'items': post.getComments()
]) }}