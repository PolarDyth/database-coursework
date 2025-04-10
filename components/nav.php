<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/cw2/php/components/auth/auth.php';
?>

<header>
  <div class="container">
    <h1><a href="/database" style="color: white; text-decoration: none;">Food Advisor</a></h1>
    <nav>
      <ul>
        <?php if ($user): ?>
          <li><a href="profile.php">Profile (<?php echo htmlspecialchars($user['username']); ?>)</a></li>
          <li><a href="logout">Logout</a></li>
        <?php else: ?>
          <li><a href="login">Login</a></li>
          <li><a href="signup">Sign Up</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>
