<?php
require_once 'db.php';
if (empty($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>All Users</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/core-js-bundle@3.30.0/minified.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">MyApp</a>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add user</button>
                <a class="btn btn-outline-secondary" href="index.php">Back</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="mb-3">
            <input id="search" class="form-control" placeholder="Search users by username, first or last name">
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>First name</th>
                        <th>Last name</th>
                        <th>Is admin</th>
                        <th>Date added</th>
                    </tr>
                </thead>
                <tbody id="usersBody">

                </tbody>
            </table>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="addUserForm" class="modal-content" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title">Add user</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Username</label>
                        <input id="add_username" name="username" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="mb-2 col">
                            <label class="form-label">First name</label>
                            <input id="add_firstname" name="firstname" class="form-control" required>
                        </div>
                        <div class="mb-2 col">
                            <label class="form-label">Last name</label>
                            <input id="add_lastname" name="lastname" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Password</label>
                        <input id="add_password" name="password" type="password" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Confirm password</label>
                        <input id="add_confirm_password" name="confirm_password" type="password" class="form-control"
                            required>
                    </div>
                    <div class="form-check">
                        <input id="add_is_admin" name="is_admin" type="checkbox" class="form-check-input">
                        <label class="form-check-label" for="add_is_admin">Is admin?</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="btnAddUser" type="submit" class="btn btn-primary">Add user</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="api.js"></script>
</body>

</html>