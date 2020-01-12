{% if paginator.total_items > 0 %}
<nav>
   <ul class="pagination">
       {% if paginator.current != 1 %}
       <li class="page-item">
           {{ link_to(paginatorPath~"/?page="~paginator.before, 
              "&laquo; "~_('Previous Page'), 
              "class": "page-link") }}</li>
       {% endif %}

       {% if paginator.current != paginator.last %}
       <li class="page-item">
           {{ link_to(paginatorPath~"/?page="~paginator.next, 
              _('Next Page')~" &raquo;", "class": "page-link") }}</li>
       {% endif %}
   </ul>
</nav>
{% endif %}