<?php
// register.php
require_once 'db.php';

// if logged in, redirect to index
if (!empty($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Register</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Core-js polyfill (suggested) -->
    <script src="https://cdn.jsdelivr.net/npm/core-js-bundle@3.30.0/minified.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Register</h4>
                        <form id="registerForm" novalidate>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input id="username" name="username" class="form-control" required>
                                <div class="form-text">Choose a unique username</div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col">
                                    <label class="form-label">First name</label>
                                    <input id="firstname" name="firstname" class="form-control" required>
                                </div>
                                <div class="mb-3 col">
                                    <label class="form-label">Last name</label>
                                    <input id="lastname" name="lastname" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input id="password" name="password" type="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input id="confirm_password" name="confirm_password" type="password"
                                    class="form-control" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input id="is_admin" name="is_admin" type="checkbox" class="form-check-input">
                                <label class="form-check-label" for="is_admin">Is admin?</label>
                            </div>
                            <button id="btnRegister" class="btn btn-primary w-100" type="submit">Register</button>
                            <div class="mt-3 text-center">
                                <a href="login.php">Already have an account? Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="api.js"></script>

</body>

</html>