<?php
session_start();

if (isset($_SESSION['dr_admin_signed_in']) && $_SESSION['dr_admin_signed_in'] === true) {
    header('Location: ../dashboard/dashboard.php');
    exit();
}

$loginError = '';
$rememberEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['keep_signed_in']) ? true : false;
    
    $validEmail = 'admin@123.com';
    $validPassword = 'admin123';
    $validName = 'Renata';
    
    if (strtolower($email) === strtolower($validEmail) && $password === $validPassword) {
        $_SESSION['dr_admin_signed_in'] = true;
        $_SESSION['dr_admin_name'] = $validName;
        $_SESSION['dr_admin_email'] = $email;
        
        if ($remember) {
            setcookie('dr_admin_email', $email, time() + (86400 * 30), '/'); // 30 days
        }
        
        header('Location: ../dashboard/dashboard.php');
        exit();
    } else {
        $loginError = 'Incorrect email or password. Try the demo credentials below.';
        $rememberEmail = $email;
    }
}

if (empty($rememberEmail) && isset($_COOKIE['dr_admin_email'])) {
    $rememberEmail = $_COOKIE['dr_admin_email'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login · Dollhaus Royale</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;1,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/portal.css" />
</head>
<body>

<div class="login-wrap">

  <div class="login-panel">
    <div class="login-logo">
      <img src="image/dr-logo.png" class="dr-crest" alt="Dollhaus Royale crest" />
      DOLLHAUS <em>ROYALE</em>
    </div>

    <div class="login-hero">
   
      <h1>Every litter, every family,<br>every detail — in one ledger.</h1>
      <p>Sign in to manage reservations, health records, and messages across the kennel.</p>
    </div>

    <div class="login-quote">
      <p>"Keeping the record straight is how we keep our promise to every family — start to finish."</p>
      <div class="who">
        <span class="avatar">M</span>
        Marisol, Kennel Manager
      </div>
    </div>
  </div>

  <div class="login-form-side">
    <div class="login-form-card">
      <div class="eyebrow">
       
      </div>
      <h2>Sign in</h2>
        <p>Every litter, every family,
every detail — in one ledger.</p>

      <?php if ($loginError): ?>
      <div class="login-error show" id="loginError">
        <?php echo htmlspecialchars($loginError); ?>
      </div>
      <?php else: ?>
      <div class="login-error" id="loginError">
        Incorrect email or password. Try the demo credentials below.
      </div>
      <?php endif; ?>

      <form method="POST" action="login.php" id="loginForm">
        <div class="field">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="you@dollhausroyale.com" autocomplete="username" required value="<?php echo htmlspecialchars($rememberEmail); ?>" />
        </div>

        <div class="field field-password">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password" required />
          <button type="button" class="toggle-pw" id="togglePw" aria-label="Show password">
            <svg class="icon-eye" viewBox="0 0 24 24"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z" /><circle cx="12" cy="12" r="3" /></svg>
            <svg class="icon-eye-off" viewBox="0 0 24 24" style="display:none;"><path d="M17.9 17.9A10.6 10.6 0 0 1 12 19c-7 0-11-7-11-7a19.6 19.6 0 0 1 5-5.6M9.9 4.2A9.7 9.7 0 0 1 12 4c7 0 11 7 11 7a19.6 19.6 0 0 1-2.2 3.1M14.1 14.1a3 3 0 1 1-4.2-4.2" /><path d="M1 1l22 22" /></svg>
          </button>
        </div>

        <div class="field-row">
          <label><input type="checkbox" name="keep_signed_in" id="keepSignedIn" <?php echo isset($_COOKIE['dr_admin_email']) ? 'checked' : ''; ?> /> Keep me signed in</label>
        
        </div>

        <button type="submit" class="btn btn-solid">Sign in to Admin</button>
      </form>

      
    
    </div>
  </div>

</div>

<script>

  const pwInput = document.getElementById('password');
  const toggleBtn = document.getElementById('togglePw');
  const eyeIcon = toggleBtn.querySelector('.icon-eye');
  const eyeOffIcon = toggleBtn.querySelector('.icon-eye-off');
  
  toggleBtn.addEventListener('click', function () {
    const isHidden = pwInput.type === 'password';
    pwInput.type = isHidden ? 'text' : 'password';
    toggleBtn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    eyeIcon.style.display = isHidden ? 'none' : 'block';
    eyeOffIcon.style.display = isHidden ? 'block' : 'none';
  });


  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const errorBox = document.getElementById('loginError');
  
  emailInput.addEventListener('input', function() {
    errorBox.classList.remove('show');
  });
  
  passwordInput.addEventListener('input', function() {
    errorBox.classList.remove('show');
  });
</script>

</body>
</html>