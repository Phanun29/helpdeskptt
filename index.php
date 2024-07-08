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
    <link rel="icon" href="img/favicon.ico.png">
    <link rel="manifest" href="manifest.json">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h4 text-primary">PTT (CAMBODIA) Limited</a>
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
                        <input class="form-control" type="password" name="password" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
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



                <p class="mb-1">

                </p>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
</body>

</html>