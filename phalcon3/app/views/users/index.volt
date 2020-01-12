<div class="page-header">
    <h1>{{ _('Users') }}</h1>
    <hr>
</div>

{{ content() }}

{{ form("users/index", "method":"post", "autocomplete": "off",
    "class": "form-inline") }}

<div class="form-group">
<label for="fieldName" class="col-sm-3 control-label"
    >{{ _('Name') }}</label>
<div class="col-sm-9">
    {{ text_field("name", "size": 20, "class": "form-control",
        "id": "fieldName") }}
</div>
</div>

<div class="form-group">
<label for="fieldEmail" class="col-sm-3 control-label"
    >{{ _('E-mail') }}</label>
<div class="col-sm-9">
    {{ text_field("email", "size": 20, "class": "form-control",
        "id": "fieldEmail") }}
</div>
</div>

<div class="form-group">
<label for="fieldType" class="col-sm-3 control-label"
    >{{ _('Type') }}</label>
<div class="col-sm-9">
    {{ select_static("type", ['': _('All Types'),
        'user': _('Common User'), 'admin': _('Administrator')],
        'class': 'form-control', 'id': 'fieldType') }}
</div>
</div>

<div class="form-group">
<div class="col-sm-offset-3 col-sm-9">
    {{ submit_button(_('Search'), 'class': 'btn btn-default') }}
</div>
</div>

{{ end_form() }}

<br>

{{ partial('users/search') }}
     