<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$env = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/database/.env", true);
$secret = $env['JWT_SECRET'] ?? 'default_secret_key';

$user = null;

if (isset($_COOKIE['token'])) {
  try {
    $decoded = JWT::decode($_COOKIE['token'], new Key($secret, 'HS256'));
    $user = [
      'user_id' => $decoded->user_id,
      'email' => $decoded->email,
      'username' => $decoded->username,
      'preferences' => $decoded->preferences ?? "",
    ];
  } catch (Exception $e) {
    // Token is invalid or expired
    setcookie('token', '', time() - 3600, '/');
  }
}
