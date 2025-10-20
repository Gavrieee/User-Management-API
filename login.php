<?php
// login.php
require_once 'db.php';
if (!empty($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/core-js-bundle@3.30.0/minified.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Login</h4>
                        <form id="loginForm" novalidate>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input id="username" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input id="password" name="password" type="password" class="form-control" required>
                            </div>
                            <button class="btn btn-primary w-100" type="submit">Login</button>
                            <div class="mt-3 text-center">
                                <a href="register.php">Create an account</a>
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