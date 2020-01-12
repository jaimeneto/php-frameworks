{% if items is defined %}
<table class="table">
   <tbody>
       {% for comment in items %}
       {% if comment.getApprovedAt() !== null or
               (session.get('auth') != null and (
               session.get('auth').id === comment.getUserId() or
               session.get('auth').type === 'admin')) %}
       <tr {% if comment.getApprovedAt() === null %}
           class="warning" {% endif %}>
           <td>
               <div>
                   <strong>{{ comment.getUser().getName() }}</strong>
                   <small class="text-muted">&minus;
                       {{ comment.getCreatedAt('d/m/Y H:i') }}</small>
                   {% if comment.getApprovedAt() === null %}
                   <span class="badge badge-danger">
                       Pendente de aprovação</span>
                   {% endif %}
               </div>
               <div>{{ comment.getText() }}</div>
           </td>
           {% if session.get('auth') != null %}
           <td>
               {% if comment.getApprovedAt() === null
                   and session.get('auth').type === 'admin' %}
               {{ link_to("comments/approve/"~comment.getId()
                   ~"/"~comment.getPostId(), "Aprovar",
                   "class":"btn btn-sm btn-primary",
                   "style":"margin-bottom: 2px; width: 100%",
                   "onclick":"return confirm('Deseja Aprovar?')") }}
               <br>
               {% endif %}

               {% if session.get('auth').id === comment.getUserId()     
                   or session.get('auth').type === 'admin' %}
               {{ link_to("comments/delete/"~comment.getId()
                   ~"/"~comment.getPostId(), "Excluir",
                   "class":"btn btn-sm btn-danger",
                   "style":"width: 100%",
                   "onclick":"return confirm('Deseja Excluir?')") }}
               {% endif %}
           </td>
           {% endif %}
       </tr>
       {% endif %}
       {% endfor %}
   </tbody>
</table>
{% endif %}