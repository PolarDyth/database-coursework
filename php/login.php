<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Food Advisor</title>
  <link rel="stylesheet" href="/database/css/login.css">
</head>

<body>

  <div class="login-card">
    <h1>Login</h1>
    <form method="POST" action="/database/lib/login_handler.php">
      <input type="email" name="email" required placeholder="Email">
      <input type="password" name="password" required placeholder="Password">
      <button type="submit">Log In</button>
      <?php if (isset($_GET["error"]) && $_GET["error"] == "404"): ?>
        <p style="color: red;">Invalid username or password</p>
      <?php endif; ?>
      <?php if (isset($_GET["error"]) && $_GET["error"] == "502"): ?>
        <p style="color: red;">Internal Server Error</p>
      <?php endif; ?>
    </form>
  </div>

</body>

</html>