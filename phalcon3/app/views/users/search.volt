<div class="row">
    {% if page.total_items > 0 %}
    <table class="table table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Id</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Tipo</th>
                <th>Criação</th>
                <th>Último acesso</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            {% if page.items is defined %}
            {% for user in page.items %}
            <tr {% if user.getDeletedAt() !== NULL %} class="text-muted" {% endif %}>
                <td>{{ user.getId() }}</td>
                <td>{{ user.getName() }}</td>
                <td>{{ user.getEmail() }}</td>
                <td>{{ user.getType() }}</td>
                <td>{{ user.getCreatedAt('d/m/Y H:i') }}</td>
                <td>{{ user.getAccessedAt('d/m/Y H:i') }}</td>
                <td class="text-right">
                    {% if user.getDeletedAt() === NULL %}

                    {% if session.get('auth').type === 'admin'
        and user.getType() !== 'admin' %}
                    {{ link_to("users/turnIntoAdmin/"~user.getId(), _('Turn Into Admin'),
                "class":"btn btn-sm btn-dark",
                "onclick":"return confirm('Deseja tornar Administrador?')") }}
                    {% endif %}

                    {% if session.get('auth').id !== user.getId()
            and user.getType() !== 'admin' %}
                    {{ link_to("users/delete/"~user.getId(), _('Delete'),
                "class":"btn btn-sm btn-danger",
                "onclick":"return confirm('Deseja enviar para a lixeira?')") }}
                    {% endif %}

                    {% else %}
                    {{ link_to("users/restore/"~user.getId(), _('Restaure'),
            "class":"btn btn-sm btn-warning",
            "onclick":"return confirm('Deseja Restaurar?')") }}

                    {{ link_to("users/delete/"~user.getId()~"/1", _('Destroy'),
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
    <p class="w-100 text-center">Nenhum usuário encontrado.</p>
    {% endif %}
</div>

{{ partial('partials/pagination', [
    'paginator': page, 
    'paginatorPath': 'users/index', 
    'paginatorLimit': 10
]) }}