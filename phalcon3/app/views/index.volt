<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    {{ get_title() }}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('img/favicon.ico') }}" />
</head>

<body>

    <header>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="container">
                {{ link_to('', 'PHP Frameworks - Phalcon',
                        'class': 'navbar-brand') }}
                <button class="navbar-toggler" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        {% if session.get('auth') != null and
                            session.get('auth').type == 'admin' %}
                        <li class="nav-item{{ router.getRewriteUri() == '/users'
                        ? ' active' : ''}}">
                            {{ link_to('users', _('Users'), 'class': 'nav-link') }}</li>
                        <li class="nav-item{{ router.getRewriteUri() == '/posts'
                        ? ' active' : ''}}">
                            {{ link_to('posts', _('Posts'), 'class': 'nav-link') }}</li>
                        <li class="nav-item{{ router.getRewriteUri() == '/comments'
                        ? ' active' : ''}}">
                            {{ link_to('comments', _('Comments'), 'class': 'nav-link') }}
                        </li>
                        {% endif %}
                    </ul>

                    <ul class="nav navbar-nav navbar-right">
                        {% if session.get('auth') == null %}
                        <li class="nav-item{{ router.getRewriteUri() == '/login'
                        ? ' active' : ''}}">
                            {{ link_to('login', _('Login'), 'class': 'nav-link') }}</li>
                        <li class="nav-item{{ router.getRewriteUri() == '/register'
                        ? ' active' : ''}}">
                            {{ link_to('register',_('Register'),'class':'nav-link') }}
                        </li>
                        {% else %}
                        <li class="nav-item">{{ link_to('', session.get('auth').name, 
                       'class': 'nav-link') }}</li>
                        <li class="nav-item">{{ link_to('logout', _('Logout'), 
                       'class': 'nav-link') }}</li>
                        {% endif %}
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
    </header>

    <main class="container" style="margin-top:20px">
        {{ content() }}
    </main>

    <!-- jQuery first, then Popper.js, and then Bootstrap's JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script>
</body>

</html>