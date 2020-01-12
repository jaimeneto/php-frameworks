<div class="page-header">
    <h1>{{ _('Register') }}</h1>
    <hr>
</div>

{{ content() }}

{{ form("users/create", "method":"post", "autocomplete":"off",
            "class":"form-horizontal col-sm-offset-2") }}

<div class="form-group">
    <label for="fieldName" class="col-sm-2 control-label">
        {{ _('Name') }}</label>
    <div class="col-sm-5">
        {{ text_field("name", "class":"form-control", "id":"fieldName",
                    "autofocus":"autofocus") }}
    </div>
</div>

<div class="form-group">
    <label for="fieldEmail" class="col-sm-2 control-label">
        {{ _('E-mail') }}</label>
    <div class="col-sm-5">
        {{ text_field("email", "class":"form-control",
                    "id":"fieldEmail") }}
    </div>
</div>

<div class="form-group">
    <label for="fieldPswd" class="col-sm-2 control-label">
        {{ _('Password') }}</label>
    <div class="col-sm-5">
        {{ password_field("password", "class": "form-control",
                    "id":"fieldPswd") }}
    </div>
</div>

<div class="form-group">
    <label for="fieldPasswordConfirm" class="col-sm-2 control-label">
        {{ _('Confirm password') }}</label>
    <div class="col-sm-5">
        {{ password_field("password_confirmation",
                    "class":"form-control", "id":"fieldPasswordConfirm") }}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-5">
        {{ submit_button(_('Register'),
                    "class":"btn btn-lg btn-primary") }}
    </div>
</div>

{{ end_form() }}