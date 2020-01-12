{% if page.items is defined %}
<div class="card-deck">
    {% for post in page.items %}
    <div class="card">
        <div class="card-header">{{ post.getTitle()|e }}</div>
        <div class="card-body">
            <p class="card-text text-justify">
                {{ truncate(post.getText(), 300, '...') }}
            </p>

            {{ link_to("post/"~post.getId(), _('Read More'),
              "class":"btn btn-sm btn-primary float-right") }}

            <p>
                <small class="text-muted">
                    <strong>{{ post.getUser().getName()|e }}</strong>
                    &minus; {{ post.getCreatedAt('d/m/Y H:i') }}
                </small>
            </p>
        </div>
    </div>
    {% if (loop.index % 2 === 0) %}
    </div><br>
    <div class="card-deck">
    {% endif %}
    {% endfor %}
</div>
{% endif %}

<div class="float-right">
{{ partial('partials/simplePagination', [
   'paginator': page,
   'paginatorPath': ''
]) }}
</div>