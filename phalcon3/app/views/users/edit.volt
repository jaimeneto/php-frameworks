<div class="row">
    <nav>
        <ul class="pager">
            <li class="previous">{{ link_to("users", "Go Back") }}</li>
        </ul>
    </nav>
</div>

<div class="page-header">
    <h1>
        Edit users
    </h1>
</div>

{{ content() }}

{{ form("users/save", "method":"post", "autocomplete" : "off", "class" : "form-horizontal") }}

<div class="form-group">
    <label for="fieldName" class="col-sm-2 control-label">Name</label>
    <div class="col-sm-10">
        {{ text_field("name", "size" : 30, "class" : "form-control", "id" : "fieldName") }}
    </div>
</div>

<div class="form-group">
    <label for="fieldEmail" class="col-sm-2 control-label">Email</label>
    <div class="col-sm-10">
        {{ text_field("email", "size" : 30, "class" : "form-control", "id" : "fieldEmail") }}
    </div>
</div>

<div class="form-group">
    <label for="fieldType" class="col-sm-2 control-label">Type</label>
    <div class="col-sm-10">
        {{ text_field("type", "size" : 30, "class" : "form-control", "id" : "fieldType") }}
    </div>
</div>

<div class="form-group">
    <label for="fieldPassword" class="col-sm-2 control-label">Password</label>
    <div class="col-sm-10">
        {{ text_field("password", "size" : 30, "class" : "form-control", "id" : "fieldPassword") }}
    </div>
</div>

<div class="form-group">
    <label for="fieldRememberToken" class="col-sm-2 control-label">Remember Of Token</label>
    <div class="col-sm-10">
        {{ text_field("remember_token", "size" : 30, "class" : "form-control", "id" : "fieldRememberToken") }}
    </div>
</div>

<div class="form-group">
    <label for="fieldCreatedAt" class="col-sm-2 control-label">Created</label>
    <div class="col-sm-10">
        {{ text_field("created_at", "size" : 30, "class" : "form-control", "id" : "fieldCreatedAt") }}
    </div>
</div>

<div class="form-group">
    <label for="fieldEmailVerifiedAt" class="col-sm-2 control-label">Email Of Verified</label>
    <div class="col-sm-10">
        {{ text_field("email_verified_at", "size" : 30, "class" : "form-control", "id" : "fieldEmailVerifiedAt") }}
    </div>
</div>

<div class="form-group">
    <label for="fieldUpdatedAt" class="col-sm-2 control-label">Updated</label>
    <div class="col-sm-10">
        {{ text_field("updated_at", "size" : 30, "class" : "form-control", "id" : "fieldUpdatedAt") }}
    </div>
</div>

<div class="form-group">
    <label for="fieldAccessedAt" class="col-sm-2 control-label">Accessed</label>
    <div class="col-sm-10">
        {{ text_field("accessed_at", "size" : 30, "class" : "form-control", "id" : "fieldAccessedAt") }}
    </div>
</div>

<div class="form-group">
    <label for="fieldDeletedAt" class="col-sm-2 control-label">Deleted</label>
    <div class="col-sm-10">
        {{ text_field("deleted_at", "size" : 30, "class" : "form-control", "id" : "fieldDeletedAt") }}
    </div>
</div>


{{ hidden_field("id") }}

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        {{ submit_button('Send', 'class': 'btn btn-default') }}
    </div>
</div>

</form>
