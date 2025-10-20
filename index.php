<?php
require_once 'db.php';
if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Index</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/core-js-bundle@3.30.0/minified.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">MyApp</a>
            <div>
                <span class="me-3">Hello there <strong><?= htmlspecialchars($user['username']) ?></strong></span>
                <?php if ($user['is_admin']): ?>
                    <a class="btn btn-outline-primary btn-sm" href="all_users.php">All Users</a>
                <?php endif; ?>
                <button id="logoutBtn" class="btn btn-link">Logout</button>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4>Welcome, <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></h4>
                <p>You are logged in as <code><?= htmlspecialchars($user['username']) ?></code>.
                    <?= $user['is_admin'] ? 'You have admin privileges.' : '' ?>
                </p>
            </div>
        </div>
    </div>

    <script src="api.js"></script>
</body>

</html>