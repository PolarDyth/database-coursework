<?php

include($_SERVER["DOCUMENT_ROOT"] . "/database/lib/database.php");

$conn = DBConnect(); // Call the function to connect to the database
require_once $_SERVER["DOCUMENT_ROOT"] . '/database/vendor/autoload.php';

use Firebase\JWT\JWT;

session_start(); // Start the session

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST["email"]);
  $password = trim($_POST["password"]);

  try {
  $stmt = $conn->prepare("SELECT id, username, password, preferences FROM Users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
      $payload = [
        "user_id" => $user["id"],
        "email" => $email,
        "username" => $user["username"],
        "preferences" => $user["preferences"],
        "exp" => time() + 86400 * 30
      ];

      $env = parse_ini_file(__DIR__ . '/../../../.env', true);
      $secret = $env["JWT_SECRET"] ?: 'default_secret_key';
      $jwt = JWT::encode($payload, $secret, 'HS256');

      setcookie("token", $jwt, time() + 86400 * 30, "/", "", false, true);
      header("Location: /database/");
      exit();
    }
  }
  } catch (Exception $e) {
    header("Location: login?error=502");
  }
  header("Location: login?error=404");
  exit();
}

?>