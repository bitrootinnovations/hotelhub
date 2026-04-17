<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HotelHub — Sign In</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ $base_url }}/assets/img/favicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $base_url }}/assets/img/apple-touch-icon.png">
    <link rel="stylesheet" href="{{ $base_url }}/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ $base_url }}/assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="{{ $base_url }}/assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="{{ $base_url }}/assets/plugins/tabler-icons/tabler-icons.css">
    <link rel="stylesheet" href="{{ $base_url }}/assets/css/style.css">
</head>
<body class="account-page bg-white">

    <div id="global-loader">
        <div class="whirly-loader"></div>
    </div>

    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper login-new">
                <div class="row w-100">
                    <div class="col-lg-5 mx-auto">
                        <div class="login-content user-login">
                            <div class="login-logo">
                                <img src="{{ $base_url }}/assets/img/logo.svg" alt="HotelHub">
                                <a href="{{ route('login') }}" class="login-logo logo-white">
                                    <img src="{{ $base_url }}/assets/img/logo-white.svg" alt="HotelHub">
                                </a>
                            </div>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="card">
                                    <div class="card-body p-5">
                                        <div class="login-userheading">
                                            <h3>Sign In</h3>
                                            <h4>Access the HotelHub panel using your email and password.</h4>
                                        </div>

                                        @if($errors->any())
                                            <div class="alert alert-danger mb-3">
                                                {{ $errors->first() }}
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <label class="form-label">Email <span class="text-danger"> *</span></label>
                                            <div class="input-group">
                                                <input type="email"
                                                    name="email"
                                                    value="{{ old('email') }}"
                                                    class="form-control border-end-0 @error('email') is-invalid @enderror"
                                                    placeholder="Enter your email"
                                                    required>
                                                <span class="input-group-text border-start-0">
                                                    <i class="ti ti-mail"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Password <span class="text-danger"> *</span></label>
                                            <div class="pass-group">
                                                <input type="password"
                                                    name="password"
                                                    class="pass-input form-control"
                                                    placeholder="Enter your password"
                                                    required>
                                                <span class="ti toggle-password ti-eye-off text-gray-9"></span>
                                            </div>
                                        </div>

                                        <div class="form-login authentication-check">
                                            <div class="row">
                                                <div class="col-12 d-flex align-items-center justify-content-between">
                                                    <div class="custom-control custom-checkbox">
                                                        <label class="checkboxs ps-4 mb-0 pb-0 line-height-1 fs-16 text-gray-6">
                                                            <input type="checkbox" name="remember" class="form-control">
                                                            <span class="checkmarks"></span>Remember me
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-login mt-3">
                                            <button type="submit" class="btn btn-primary w-100">Sign In</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="my-4 d-flex justify-content-center align-items-center copyright-text">
                            <p>Copyright &copy; {{ date('Y') }} HotelHub</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ $base_url }}/assets/js/jquery-3.7.1.min.js"></script>
    <script src="{{ $base_url }}/assets/js/feather.min.js"></script>
    <script src="{{ $base_url }}/assets/js/bootstrap.bundle.min.js"></script>
    <script src="{{ $base_url }}/assets/js/script.js"></script>

</body>
</html>
