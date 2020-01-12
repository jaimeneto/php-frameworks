<br>
<div class="card">
    <div class="card-header">{{ _('Comments') }}</div>

    {# Exibe a lista de comentários cadastrados #}
    <div class="card-body">
        {{ partial('comments/list', ['items': items]) }}
    </div>

    <div class="card-footer">
        {% if session.get('auth') != null %}
        {{ form("comments/create", "method":"post", "autocomplete":"off",
                   "class":"form-horizontal") }}

        <div class="form-group col-sm-12">
            {{ text_area("text", "class":"form-control", "id":"fieldText",
                       "placeholder": _('Write your comments here') ) }}
        </div>

        <div class="form-group col-sm-12">
            {{ submit_button(_('Save'), 'class': 'btn btn-primary') }}
            <button type="reset" class="btn btn-light">{{ _('Cancel') }}</button>
        </div>

        <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getToken() }}" />
        <input type="hidden" name="post_id" value="{{ postId }}" />

        {{ end_form() }}

        {% else %}
        <br>
        <p class="text-center">
            Você precisa efetuar {{ link_to('login', 'login') }}
            para poder inserir seus comentários!
        </p>
        {% endif %}
    </div>
</div>
<br>