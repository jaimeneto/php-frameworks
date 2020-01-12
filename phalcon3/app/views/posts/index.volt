<div class="page-header">
   {{ link_to("posts/new", _('Create'), 
              "class": "btn btn-primary float-right") }}
   <h1>Posts</h1>
   <hr>
</div>

{{ content() }}

{{ form("posts/index", "method":"post", "autocomplete" : "off", 
         "class" : "form-inline") }}

<div class="form-group">
   <label for="fieldId" class="col-sm-2 control-label">Id</label>
   <div class="col-sm-10">
      {{ text_field("id", "size" : 10, "class" : "form-control", 
           "id" : "fieldId") }}
   </div>
</div>

<div class="form-group">
   <label for="fieldTitle" class="col-sm-2 control-label">{{ _('Title') }}</label>
   <div class="col-sm-10">
      {{ text_field("title", "size" : 20, "class" : "form-control", 
           "id" : "fieldTitle") }}
   </div>
</div>

<div class="form-group">
   <label for="fieldUserId" class="col-sm-2 control-label">{{ _('User') }}</label>
   <div class="col-sm-10">
      {{ select_static("user_id", users, 'using': ['id', 'name'],
           'useEmpty': true, 'emptyText': _('All Users'),
           'class': 'form-control', 'id': 'fieldUserId') }}
   </div>
</div>

<div class="form-group">
   <div class="col-sm-offset-2 col-sm-10">
      {{ submit_button(_('Search'), 'class': 'btn btn-default') }}
   </div>
</div>

{{ end_form() }}

<br>

{{ partial('posts/search') }}