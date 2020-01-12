<div class="page-header">
    <h1>{{ _('Login') }}</h1>
    <hr>
</div>

{{ content() }}

{{ form('login' ~ redirectTo, 'role': 'form',
"class":"form-horizontal col-sm-offset-2") }}

<input type="hidden" name="{{ security.getTokenKey() }}" 
    value="{{ security.getToken() }}" />

<div class="form-group">
    <label for="email" class="col-sm-2 control-label">
        {{ _('E-mail Address') }}</label>
    <div class="col-sm-5">
        {{ email_field('email', 'class':'form-control',
                'required':'required') }}
    </div>
</div>

<div class="form-group">
    <label for="password" class="col-sm-2 control-label">
        {{ _('Password') }}</label>
    <div class="col-sm-5">
        {{ password_field('password', 'class':'form-control',
                'required':'required') }}
    </div>
</div>

<div class="form-group form-check">
    <div class="col-sm-offset-2 col-sm-5">
        {{ check_field('remember', 'value':1,
            'class':'form-check-input', 'id': 'remember') }}
        <label class="form-check-label" for="remember">
            {{ _('Remember Me') }}
        </label>
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-5">
        {{ submit_button(_('Login'), 
            'class': 'btn btn-primary btn-lg') }}
        {{ link_to('users/passwordReset', _('Forgot Your Password?'),
            'class': 'btn btn-link') }}
    </div>
</div>

{{ end_form() }}