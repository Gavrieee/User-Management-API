<?php

header('Content-Type: application/json; charset=utf-8');
require_once 'db.php';

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $input['action'] ?? null;

if (!$action) {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
    exit;
}

function json_res($arr)
{
    echo json_encode($arr);
    exit;
}

switch ($action) {

    case 'register':
        $username = trim($input['username'] ?? '');
        $firstname = trim($input['firstname'] ?? '');
        $lastname = trim($input['lastname'] ?? '');
        $password = $input['password'] ?? '';
        $confirm = $input['confirm_password'] ?? '';
        $is_admin = !empty($input['is_admin']) ? 1 : 0;

        // validations
        if ($username === '' || $firstname === '' || $lastname === '' || $password === '' || $confirm === '') {
            json_res(['status' => 'error', 'code' => 'empty_field', 'message' => 'All fields are required']);
        }
        if (strlen($password) < 8) {
            json_res(['status' => 'error', 'code' => 'short_password', 'message' => 'Password must be at least 8 characters']);
        }
        if ($password !== $confirm) {
            json_res(['status' => 'error', 'code' => 'mismatch_password', 'message' => 'Passwords do not match']);
        }

        // check username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u");
        $stmt->execute(['u' => $username]);
        if ($stmt->fetch()) {
            json_res(['status' => 'error', 'code' => 'username_exists', 'message' => 'Username already exists!']);
        }

        // create
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO users (username, firstname, lastname, is_admin, password) VALUES (:u,:f,:l,:a,:p)");
        $ins->execute(['u' => $username, 'f' => $firstname, 'l' => $lastname, 'a' => $is_admin, 'p' => $hash]);

        json_res(['status' => 'success', 'message' => 'Registration successful']);

    case 'login':
        // expected username, password
        $username = trim($input['username'] ?? '');
        $password = $input['password'] ?? '';

        if ($username === '' || $password === '') {
            json_res(['status' => 'error', 'code' => 'empty_field', 'message' => 'Please enter username and password']);
        }

        $stmt = $pdo->prepare("SELECT id, username, password, is_admin, firstname, lastname FROM users WHERE username = :u");
        $stmt->execute(['u' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user)
            json_res(['status' => 'error', 'code' => 'invalid', 'message' => 'Invalid credentials']);

        if (!password_verify($password, $user['password'])) {
            json_res(['status' => 'error', 'code' => 'invalid', 'message' => 'Invalid credentials']);
        }

        // set session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
            'is_admin' => (int) $user['is_admin']
        ];

        json_res(['status' => 'success', 'message' => 'Logged in', 'user' => $_SESSION['user']]);

    case 'logout':
        session_unset();
        session_destroy();
        json_res(['status' => 'success', 'message' => 'Logged out']);

    case 'current_user':
        if (!empty($_SESSION['user'])) {
            json_res(['status' => 'success', 'user' => $_SESSION['user']]);
        } else {
            json_res(['status' => 'error', 'message' => 'Not logged in']);
        }

    case 'check_username':
        $username = trim($input['username'] ?? '');
        if ($username === '')
            json_res(['status' => 'error', 'message' => 'No username provided']);
        $stmt = $pdo->prepare("SELECT 1 FROM users WHERE username = :u");
        $stmt->execute(['u' => $username]);
        if ($stmt->fetch()) {
            json_res(['status' => 'exists']);
        } else {
            json_res(['status' => 'available']);
        }

    case 'get_users':
        // only admin can access
        if (empty($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
            json_res(['status' => 'error', 'message' => 'Unauthorized']);
        }
        $stmt = $pdo->query("SELECT id, username, firstname, lastname, is_admin, date_added FROM users ORDER BY date_added DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_res(['status' => 'success', 'users' => $users]);

    case 'search_users':
        if (empty($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
            json_res(['status' => 'error', 'message' => 'Unauthorized']);
        }
        $q = trim($input['q'] ?? '');
        $stmt = $pdo->prepare("SELECT id, username, firstname, lastname, is_admin, date_added 
                               FROM users
                               WHERE username LIKE :q OR firstname LIKE :q OR lastname LIKE :q
                               ORDER BY date_added DESC");
        $stmt->execute(['q' => "%{$q}%"]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_res(['status' => 'success', 'users' => $users]);

    case 'add_user':
        // admin-only API to add user (e.g., from modal)
        if (empty($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
            json_res(['status' => 'error', 'message' => 'Unauthorized']);
        }
        $username = trim($input['username'] ?? '');
        $firstname = trim($input['firstname'] ?? '');
        $lastname = trim($input['lastname'] ?? '');
        $password = $input['password'] ?? '';
        $confirm = $input['confirm_password'] ?? '';
        $is_admin = !empty($input['is_admin']) ? 1 : 0;

        if ($username === '' || $firstname === '' || $lastname === '' || $password === '' || $confirm === '') {
            json_res(['status' => 'error', 'code' => 'empty_field', 'message' => 'All fields are required']);
        }
        if (strlen($password) < 8) {
            json_res(['status' => 'error', 'code' => 'short_password', 'message' => 'Password must be at least 8 characters']);
        }
        if ($password !== $confirm) {
            json_res(['status' => 'error', 'code' => 'mismatch_password', 'message' => 'Passwords do not match']);
        }
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u");
        $stmt->execute(['u' => $username]);
        if ($stmt->fetch()) {
            json_res(['status' => 'error', 'code' => 'username_exists', 'message' => 'Username already exists']);
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO users (username, firstname, lastname, is_admin, password) VALUES (:u,:f,:l,:a,:p)");
        $ins->execute(['u' => $username, 'f' => $firstname, 'l' => $lastname, 'a' => $is_admin, 'p' => $hash]);

        json_res(['status' => 'success', 'message' => 'This user has successfully been added']);

    default:
        json_res(['status' => 'error', 'message' => 'Unknown action']);
}