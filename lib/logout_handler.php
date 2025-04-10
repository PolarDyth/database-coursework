<?php
// Remove the token cookie by setting it to expire in the past
setcookie("token", "", time() - 3600, "/", "", false, true);

session_start();
session_unset();
session_destroy();

header("Location: /database/");
exit();
?>