<div class="row">
    {% if page.total_items > 0 %}
    <table class="table table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Id</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Criação</th>
                <th>Alteração</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            {% if page.items is defined %}
            {% for post in page.items %}
            <tr>
                <td>{{ post.getId() }}</td>
                <td>{{ post.getTitle() }}</td>
                <td>{{ post.getUser().getName() }}</td>
                <td>{{ post.getCreatedAt('d/m/Y H:i') }}</td>
                <td>{{ post.getUpdatedAt('d/m/Y H:i') }}</td>

                <td class="text-right">
                    {% if session.get('auth').id === post.getUserId() %}
                    {{ link_to("posts/edit/"~post.getId(), _("Edit"), 
                      "class":"btn btn-sm btn-dark") }}
                    {{ link_to("posts/delete/"~post.getId(), _("Delete"),
                       "class":"btn btn-sm btn-danger", 
                       "onclick":"return confirm('Deseja Excluir?')") }}
                    {% endif %}
                </td>
            </tr>
            {% endfor %}
            {% endif %}
        </tbody>
    </table>
    {% else %}
    <div class="w-100"><hr></div>
    <p class="w-100 text-center">Nenhum post encontrado.</p>
    {% endif %}
</div>

{{ partial('partials/pagination', [
    'paginator': page,
    'paginatorPath': 'posts/index',
    'paginatorLimit': 10
]) }}