<?php
include($_SERVER["DOCUMENT_ROOT"] . "/database/lib/database.php");
$conn = DBConnect(); // Call the function to connect to the database


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    if ($password !== $confirm) {
        header("Location: signup?error=Passwords+do+not+match");
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        header("Location: signup?error=Email+already+registered");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO Users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    if ($stmt->execute()) {
        header("Location: /database/login/");
        exit();
    } else {
        header("Location: signup?error=Signup+failed");
        exit();
    }
}
?>