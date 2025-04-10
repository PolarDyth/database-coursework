<?php

function DBConnect()
{
    $env = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/database/.env", true);
    $host = $env['DB_HOST'] ?: 'localhost';
    $username = $env['DB_USERNAME'] ?: 'root';
    $password = $env['DB_PASSWORD'] ?: '';
    $dbname = $env['DB_NAME'] ?: 'test_db';
    $conn = null;
    try {
        $conn = mysqli_connect($host, $username, $password, $dbname);

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    } catch (Exception $e) {
        echo "Failed to Connect: " . $e->getMessage() . "<br>";
    }
}
