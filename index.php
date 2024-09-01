<?php require_once "controllerUserData.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LOG IN</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- icon -->
    <link rel="icon" href="icons/favicon.ico.png">
    <link rel="manifest" href="manifest.json">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .login-box {
            width: 360px;
            margin: 7% auto;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #95999e;
            color: #fff;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .card-header img {
            max-width: 120px;
        }

        .card-body {
            padding: 30px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 5px;
        }

        @keyframes iconBounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .input-group-text span {
            animation: iconBounce 1.5s infinite;
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h4 text-white">PTT (CAMBODIA) Limited</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Login with your email and password.</p>
                <?php
                if (count($errors) > 0) {
                ?>
                    <div class="alert alert-danger text-center">
                        <?php
                        foreach ($errors as $showerror) {
                            echo $showerror;
                        }
                        ?>
                    </div>
                <?php
                }
                ?>
                <form method="post">
                    <div class="input-group mb-3">
                        <input class="form-control" type="email" name="email" placeholder="Email Address" required value="<?php echo $email ?>">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group ">
                        <input class="form-control" type="password" name="password" id="password" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>


                    </div>
                    <div class="input-group mt-1">
                        <input type="checkbox" id="showpassword" value="" onclick="togglePasswordVisibility()">
                        <p class="m-0">
                            Show Password
                        </p>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember">
                                <a href="forgot-password.php">I forgot my password</a>
                            </div>
                        </div>
                        <!-- /.col -->

                    </div>
                    <div class="row">

                        <!-- /.col -->
                        <div class="col-12">
                            <input class="form-control btn btn-primary" type="submit" name="login" value="Login">
                        </div>
                        <!-- /.col -->
                    </div>
                </form>


            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->
    <!-- show password -->
    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password");
            var togglePasswordIcon = document.getElementById("togglePasswordIcon");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";

            } else {
                passwordInput.type = "password";
            }
        }
    </script>
    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
</body>

</html>