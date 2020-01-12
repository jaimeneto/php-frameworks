<div class="page-header">
    <h1>{{ _('Comments') }}</h1>
    <hr>
</div>

{{ content() }}

<div class="row">
    <table class="table table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Id</th>
                <th>Título do Post</th>
                <th>Usuário</th>
                <th>Criação</th>
                <th>Aprovação</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            {% if page.items is defined %}
            {% for comment in page.items %}
            <tr>
                <td>{{ comment.getId() }}</td>
                <td>{{ comment.getPost().getTitle() }}</td>
                <td>{{ comment.getUser().getName() }}</td>
                <td>{{ comment.getCreatedAt('d/m/Y H:i') }}</td>
                <td>{{ comment.getApprovedAt('d/m/Y H:i') }}</td>
                <td class="text-right">
                    {% if comment.getApprovedAt() === null %}
                    {{ link_to("comments/approve/"~comment.getId(), "Aprovar",
                    "class":"btn btn-sm btn-primary",
                    "onclick":"return confirm('Deseja Aprovar?')") }}
                    {% endif %}
                    {{ link_to("comments/delete/"~comment.getId(), "Excluir",
                "class":"btn btn-sm btn-danger",
                "onclick":"return confirm('Deseja Excluir?')") }}
                </td>
            </tr>
            {% endfor %}
            {% endif %}
        </tbody>
    </table>
</div>

{{ partial('partials/pagination', [
    'paginator': page,
    'paginatorPath': 'comments/index',
    'paginatorLimit': 10
]) }}