<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>{{ $title ?? '' }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/img/favicon.png">

    <!-- Fontfamily -->
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;0,900;1,400;1,500;1,700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('/css/bootstrap.min.css') }}">

    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="{{ asset('/plugins/feather/feather.css') }}">

    <!-- Pe7 CSS -->
    <link rel="stylesheet" href="{{ asset('/plugins/icons/flags/flags.css') }}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/fontawesome/css/all.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
</head>

<body>

    <!-- Main Wrapper -->
    <div class="main-wrapper login-body">
        <div class="login-wrapper">
            <div class="container">
                <div class="loginbox">
                    <div class="login-left">
                        <img class="img-fluid" src="{{ asset('/img/login.svg') }}" alt="Logo">
                    </div>
                    <div class="login-right">
                        <div class="login-right-wrap">
                            <h1>Welcome to Accu Management System</h1>
                            <p class="account-subtitle"><a href="register.html"></a></p>

                            @if (session()->has('loginError'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error!</strong> {{ session('loginError') }}
                                </div>
                            @endif

                            <h2>Sign in</h2>
                            <!-- Form -->
                            <form action="/auth">
                                @csrf
                                <div class="form-group">
                                    <label>Username <span class="login-danger">*</span></label>
                                    <input class="form-control" type="text" autofocus name="username" required>
                                    <span class="profile-views"><i class="fas fa-user-circle"></i></span>
                                </div>

                                <div class="form-group">
                                    <label>Password <span class="login-danger">*</span></label>
                                    <input class="form-control pass-input" type="password" name="password" password
                                        required>
                                    <span class="profile-views feather-eye toggle-password"></span>
                                </div>
                                {{-- <div class="forgotpass">
                                    <div class="remember-me">
                                        <label class="custom_check mr-2 mb-0 d-inline-flex remember-me"> Remember me
                                            <input type="checkbox" name="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <a href="forgot-password.html">Forgot Password?</a>
                                </div> --}}
                                <div class="form-group">
                                    <button class="btn btn-primary btn-block" type="button"
                                        id="btn-login">Login</button>
                                </div>
                            </form>
                            <!-- /Form -->

                            {{-- <div class="login-or">
                                <span class="or-line"></span>
                                <span class="span-or">or</span>
                            </div> --}}

                            <!-- Social Login -->
                            {{-- <div class="social-login">
                                <a href="#"><i class="fab fa-google-plus-g"></i></a>
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div> --}}
                            <!-- /Social Login -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('/js/jquery-3.7.1.min.js') }}"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Feather Icon JS -->
    <script src="{{ asset('/js/feather.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('/js/script.js') }}"></script>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#btn-login').click(function() {
                // disabled button
                $(this).attr('disabled', true);
                $(this).html('Loading...');
                var username = $('input[name="username"]').val();
                var password = $('input[name="password"]').val();
                var _token = $('input[name="_token"]').val();

                $.ajax({
                    url: '/auth',
                    type: 'POST',
                    data: {
                        username: username,
                        password: password,
                        _token: _token
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            window.location.href = response.redirect;
                        } else {
                            swal("Error", response.message, "error");
                            $('#btn-login').attr('disabled', false);
                            $('#btn-login').html('Login');
                        }
                    },
                    error: function(xhr, status, error) {
                        location.reload();
                    }
                });
            });
        });
    </script>

</body>

</html>
