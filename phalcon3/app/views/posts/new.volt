<div class="page-header">
    <h1>{{ _('Create Post') }}</h1>
    <hr>
</div>

{{ content() }}

{{ form("posts/create", "method":"post", "autocomplete" : "off", 
        "class" : "form-horizontal") }}

<div class="form-group">
    <label for="fieldTitle" class="col-sm-2 control-label">{{ _('Title') }}</label>
    <div class="col-sm-10">
        {{ text_field("title", "size" : 30, "class" : "form-control", 
               "id" : "fieldTitle") }}
    </div>
</div>

<div class="form-group">
    <label for="fieldText" class="col-sm-2 control-label">{{ _('Text') }}</label>
    <div class="col-sm-10">
        {{ text_area("text", "cols": "30", "rows": "15", 
               "class" : "form-control", "id" : "fieldText") }}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        {{ submit_button(_('Save'), 'class': 'btn btn-lg btn-primary') }}
        {{ link_to("posts", _('Cancel'), "class": "btn btn-lg btn-default") }}
    </div>
</div>

{{ end_form() }}