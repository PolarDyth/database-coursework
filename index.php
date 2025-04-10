<?php
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'login':
        require_once __DIR__ . '/php/login.php';
        break;
    case 'signup':
        require_once __DIR__ . '/php/signup.php';
        break;
    case 'logout':
        require_once __DIR__ . '/lib/logout_handler.php';
        break;
    case 'home':
        require_once __DIR__ . '/php/home.php';
        break;
    default:
        http_response_code(404);
        echo "<h1>404 Page Not Found</h1>";
}
