{% if paginator.total_items > 0 %}

{# Usa format para converter start para um número inteiro #}
{% set start = '%d'|format(paginator.current - paginatorLimit/2) %}
{% if start < 1 %}{% set start = 1 %}{% endif %}
{% set end = start + paginatorLimit %}
{% if end > paginator.total_pages %}
   {% set end = paginator.total_pages %}
{% endif %}
{% if (end == paginator.last) and (end - start < paginatorLimit) %}
   {% set start = end - paginatorLimit %}
   {% if start < 1 %}{% set start = 1 %}{% endif %}
{% endif %}

<nav>
  <ul class="pagination">

     {# Insere os links para a primeira página e a anterior #}
     {% if paginator.current != 1 %}
     <li class="page-item">{{ link_to(paginatorPath~"/?page=1", "&laquo;",
        "title": _('First Page'), "class": "page-link") }}</li>
     <li class="page-item">
        {{ link_to(paginatorPath~"/?page="~paginator.before,
        "&lsaquo;", "title": _('Previous Page'), 
        "class": "page-link") }}</li>
     {% else %}
     <li class="page-item disabled"
        >{{ link_to(paginatorPath~"#", "&laquo;",
        "title": _('First Page'), "class": "page-link") }}</li>
     <li class="page-item disabled">{{ link_to(paginatorPath~"#", 
        "&lsaquo;", "title": _('Previous Page'), 
        "class": "page-link") }}</li>
     {% endif %}

     {# Insere os links das páginas #}
     {% for i in start..end %}
     <li class="page-item {%if paginator.current === i %}
        active{% endif %}">{{ link_to(paginatorPath~"/?page="~i, i, 
        "class": "page-link") }}</li>
     {% endfor %}

     {# Insere os links para a próxima página e a última #}
     {% if paginator.current != paginator.last %}
     <li class="page-item"
        >{{ link_to(paginatorPath~"/?page="~paginator.next, 
        "&rsaquo;", "title": _('Next Page'), "class": "page-link") }}
     </li>
     <li class="page-item"
        >{{ link_to(paginatorPath~"/?page="~paginator.last, 
        "&raquo;", "title": _('Last Page'), "class": "page-link") }}</li>
     {% else %}
     <li class="page-item disabled">{{ link_to(paginatorPath~"#",
        "&rsaquo;", "title": _('First Page'), 
        "class": "page-link") }}</li>
     <li class="page-item disabled"
        >{{ link_to(paginatorPath~"#", "&raquo;",
        "title": _('Last Page'), "class": "page-link") }}</li>
     {% endif %}
  </ul>
</nav>
{% endif %}