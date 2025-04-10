<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up</title>
  <link rel="stylesheet" href="/database/css/signup.css">
</head>
<body>
  <div class="signup-card">
    <h2>Create an Account</h2>

    <?php if (isset($_GET["error"])): ?>
      <p class="error">Error: <?php echo htmlspecialchars($_GET["error"]); ?></p>
    <?php endif; ?>

    <form action="/database/lib/signup_handler.php" method="POST">
      <input type="text" name="username" required placeholder="Username">
      <input type="email" name="email" required placeholder="Email">
      <input type="password" name="password" required placeholder="Password">
      <input type="password" name="confirm" required placeholder="Confirm Password">
      <button type="submit">Sign Up</button>
    </form>
  </div>
</body>
</html>
