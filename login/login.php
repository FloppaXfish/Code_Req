<?php
// Start session with proper error handling
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// ENHANCED SESSION CHECK - Force redirect if logged in
// ============================================
// Check if user is already logged in - if yes, redirect to dashboard
if (isset($_SESSION['dr_admin_signed_in']) && $_SESSION['dr_admin_signed_in'] === true) {
    if (isset($_SESSION['dr_admin_id']) && !empty($_SESSION['dr_admin_id'])) {
        header('Location: ../dashboard/dashboard.php');
        exit();
    } else {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        session_start();
    }
}

// Check for any 2FA session that might indicate logged in state
if (isset($_SESSION['2fa_user_id']) && isset($_SESSION['2fa_verified']) && $_SESSION['2fa_verified'] === true) {
    header('Location: ../dashboard/dashboard.php');
    exit();
}

// If there's a cookie with remember me, check if session should be restored
if (!isset($_SESSION['dr_admin_signed_in']) && isset($_COOKIE['dr_admin_email']) && isset($_COOKIE['dr_admin_remember'])) {
    require_once '../config/database.php';
    $db = Database::getInstance();
    $email = $_COOKIE['dr_admin_email'];
    $admin = $db->fetchOne('SELECT * FROM admin_users WHERE email = ? AND is_active = 1', [$email]);
    if ($admin) {
        $_SESSION['dr_admin_signed_in'] = true;
        $_SESSION['dr_admin_id'] = $admin['admin_id'];
        $_SESSION['dr_admin_name'] = $admin['full_name'];
        $_SESSION['dr_admin_email'] = $admin['email'];
        $_SESSION['dr_admin_role'] = $admin['role'];
        $_SESSION['last_activity'] = time();
        $_SESSION['2fa_verified'] = true;
        header('Location: ../dashboard/dashboard.php');
        exit();
    } else {
        setcookie('dr_admin_email', '', time() - 3600, '/');
        setcookie('dr_admin_remember', '', time() - 3600, '/');
    }
}

// Check if user is in 2FA verification flow
if (isset($_SESSION['2fa_user_id']) && isset($_GET['step']) && $_GET['step'] === 'verify') {
    header('Location: 2fa-verify.php');
    exit();
}

// ============================================
// END ENHANCED SESSION CHECK
// ============================================

require_once '../config/database.php';
require_once '../config/mailer.php';

$db = Database::getInstance();

$loginError = '';
$rememberEmail = '';
$timeoutMessage = '';

// Check if user was logged out due to timeout
if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    $timeoutMessage = 'Your session timed out due to inactivity. Please log in again.';
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
}

// Handle 2FA resend
if (isset($_GET['resend_2fa']) && isset($_SESSION['2fa_user_id'])) {
    $userId = $_SESSION['2fa_user_id'];
    $user = $db->fetchOne("SELECT * FROM admin_users WHERE admin_id = ?", [$userId]);
    
    if ($user) {
        $numbers = rand(100, 999);
        $letters = '';
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i < 3; $i++) {
            $letters .= $characters[rand(0, 25)];
        }
        $code = $numbers . $letters;
        
        $_SESSION['2fa_secret'] = $code;
        $_SESSION['2fa_expires'] = time() + 600;
        
        $subject = "Two-Factor Authentication Code - Dollhaus Royale";
        $body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Dollhaus Royale</title>
            <style>
                body { font-family: Georgia, serif; color: #2A1810; max-width: 600px; margin: 0 auto; padding: 0; background: #FCF9F4; }
                .email-wrapper { background: #FCF9F4; padding: 20px; }
                .header { background: #950606; padding: 30px 20px; text-align: center; border-radius: 12px 12px 0 0; }
                .header .logo { width: 80px; height: 80px; border-radius: 50%; object-fit: contain; border: 3px solid #E9C77A; margin-bottom: 12px; }
                .header h1 { color: #FCF9F4; font-family: "Playfair Display", serif; font-size: 28px; margin: 0; font-weight: 500; letter-spacing: 2px; }
                .header .subtitle { color: #E9C77A; font-size: 14px; font-style: italic; margin-top: 4px; font-family: Georgia, serif; }
                .content { padding: 35px 30px; background: #FCF9F4; border: 1px solid #e0d6cc; border-top: none; border-radius: 0 0 12px 12px; }
                .content h2 { font-family: "Playfair Display", serif; font-size: 24px; font-weight: 500; color: #2A1810; margin-top: 0; margin-bottom: 16px; }
                .greeting { font-size: 16px; color: #2A1810; margin-bottom: 12px; }
                .greeting strong { color: #950606; }
                .code-container { text-align: center; margin: 24px 0; padding: 24px; background: #f8f4ed; border-radius: 10px; border: 2px solid #CC9A3D; }
                .code-display { font-family: "Courier New", monospace; font-size: 36px; font-weight: 700; letter-spacing: 8px; color: #950606; padding: 8px 16px; background: #fff; border-radius: 8px; display: inline-block; }
                .divider { border: none; height: 2px; background: linear-gradient(90deg, transparent, #CC9A3D, transparent); margin: 24px 0; }
                .signature { font-family: "Playfair Display", serif; font-style: italic; font-size: 18px; color: #950606; margin-top: 24px; }
                .footer-bar { text-align: center; padding: 16px 20px; font-size: 12px; color: #9C8A78; background: #f8f4ed; border-radius: 0 0 12px 12px; }
                .footer-bar .gold-text { color: #CC9A3D; font-weight: 600; }
                @media (max-width: 480px) {
                    .content { padding: 20px 16px; }
                    .code-display { font-size: 28px; letter-spacing: 4px; }
                    .header h1 { font-size: 22px; }
                    .header .logo { width: 60px; height: 60px; }
                }
            </style>
        </head>
        <body>
            <div class="email-wrapper">
                <div class="header">
                    <img src="https://yukkilogic.gt.tc/dashboard/img/dr-logo.png" class="logo" alt="Dollhaus Royale" />
                    <h1>Dollhaus Royale</h1>
                    <div class="subtitle">Where Exceptional Shih Tzu Begin</div>
                </div>
                <div class="content">
                    <h2>Two-Factor Authentication</h2>
                    <p class="greeting">Dear <strong>' . htmlspecialchars($user['full_name']) . '</strong>,</p>
                    <p>Please use the verification code below to complete your login:</p>
                    
                    <div class="code-container">
                        <div class="code-display">' . $code . '</div>
                        <p style="margin-top:12px;font-size:12px;color:#5C4A3E;font-family:Inter,sans-serif;">
                            This code will expire in 10 minutes.
                        </p>
                    </div>
                    
                    <hr class="divider">
                    
                    <p style="font-size:14px;color:#5C4A3E;">
                        If you did not attempt to log in, please ignore this email and contact the system administrator.
                    </p>
                    
                    <div class="signature">Warm regards,<br>The Dollhaus Royale Team</div>
                </div>
                <div class="footer-bar">
                    <p style="margin:0;">&copy; 2026 <span class="gold-text">Dollhaus Royale</span> · Raised with intention. Loved as family.</p>
                    <p style="margin:4px 0 0 0;font-size:11px;color:#B3A292;">Premium Shih Tzu · Health-Tested · Family-Raised</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        sendEmail($user['email'], $subject, $body);
        
        header('Location: 2fa-verify.php?resent=1');
        exit();
    }
}

// Handle login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['keep_signed_in']) ? true : false;
    
    if (empty($email) || empty($password)) {
        $loginError = 'Please enter both email and password.';
    } else {
        $admin = $db->fetchOne('SELECT * FROM admin_users WHERE email = ? AND is_active = 1', [$email]);
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION = array();
            
            if ($admin['two_factor_enabled'] == 1) {
                $numbers = rand(100, 999);
                $letters = '';
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                for ($i = 0; $i < 3; $i++) {
                    $letters .= $characters[rand(0, 25)];
                }
                $code = $numbers . $letters;
                
                $_SESSION['2fa_user_id'] = $admin['admin_id'];
                $_SESSION['2fa_user_name'] = $admin['full_name'];
                $_SESSION['2fa_user_email'] = $admin['email'];
                $_SESSION['2fa_user_role'] = $admin['role'];
                $_SESSION['2fa_secret'] = $code;
                $_SESSION['2fa_expires'] = time() + 600;
                $_SESSION['2fa_remember'] = $remember;
                $_SESSION['2fa_verified'] = false;
                
                $subject = "Two-Factor Authentication Code - Dollhaus Royale";
                $body = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Dollhaus Royale</title>
                    <style>
                        body { font-family: Georgia, serif; color: #2A1810; max-width: 600px; margin: 0 auto; padding: 0; background: #FCF9F4; }
                        .email-wrapper { background: #FCF9F4; padding: 20px; }
                        .header { background: #950606; padding: 30px 20px; text-align: center; border-radius: 12px 12px 0 0; }
                        .header .logo { width: 80px; height: 80px; border-radius: 50%; object-fit: contain; border: 3px solid #E9C77A; margin-bottom: 12px; }
                        .header h1 { color: #FCF9F4; font-family: "Playfair Display", serif; font-size: 28px; margin: 0; font-weight: 500; letter-spacing: 2px; }
                        .header .subtitle { color: #E9C77A; font-size: 14px; font-style: italic; margin-top: 4px; font-family: Georgia, serif; }
                        .content { padding: 35px 30px; background: #FCF9F4; border: 1px solid #e0d6cc; border-top: none; border-radius: 0 0 12px 12px; }
                        .content h2 { font-family: "Playfair Display", serif; font-size: 24px; font-weight: 500; color: #2A1810; margin-top: 0; margin-bottom: 16px; }
                        .greeting { font-size: 16px; color: #2A1810; margin-bottom: 12px; }
                        .greeting strong { color: #950606; }
                        .code-container { text-align: center; margin: 24px 0; padding: 24px; background: #f8f4ed; border-radius: 10px; border: 2px solid #CC9A3D; }
                        .code-display { font-family: "Courier New", monospace; font-size: 36px; font-weight: 700; letter-spacing: 8px; color: #950606; padding: 8px 16px; background: #fff; border-radius: 8px; display: inline-block; }
                        .divider { border: none; height: 2px; background: linear-gradient(90deg, transparent, #CC9A3D, transparent); margin: 24px 0; }
                        .signature { font-family: "Playfair Display", serif; font-style: italic; font-size: 18px; color: #950606; margin-top: 24px; }
                        .footer-bar { text-align: center; padding: 16px 20px; font-size: 12px; color: #9C8A78; background: #f8f4ed; border-radius: 0 0 12px 12px; }
                        .footer-bar .gold-text { color: #CC9A3D; font-weight: 600; }
                        @media (max-width: 480px) {
                            .content { padding: 20px 16px; }
                            .code-display { font-size: 28px; letter-spacing: 4px; }
                            .header h1 { font-size: 22px; }
                            .header .logo { width: 60px; height: 60px; }
                        }
                    </style>
                </head>
                <body>
                    <div class="email-wrapper">
                        <div class="header">
                            <img src="https://yukkilogic.gt.tc/dashboard/img/dr-logo.png" class="logo" alt="Dollhaus Royale" />
                            <h1>Dollhaus Royale</h1>
                            <div class="subtitle">Where Exceptional Shih Tzu Begin</div>
                        </div>
                        <div class="content">
                            <h2>Two-Factor Authentication</h2>
                            <p class="greeting">Dear <strong>' . htmlspecialchars($admin['full_name']) . '</strong>,</p>
                            <p>Please use the verification code below to complete your login:</p>
                            
                            <div class="code-container">
                                <div class="code-display">' . $code . '</div>
                                <p style="margin-top:12px;font-size:12px;color:#5C4A3E;font-family:Inter,sans-serif;">
                                    This code will expire in 10 minutes.
                                </p>
                            </div>
                            
                            <hr class="divider">
                            
                            <p style="font-size:14px;color:#5C4A3E;">
                                If you did not attempt to log in, please ignore this email and contact the system administrator.
                            </p>
                            
                            <div class="signature">Warm regards,<br>The Dollhaus Royale Team</div>
                        </div>
                        <div class="footer-bar">
                            <p style="margin:0;">&copy; 2026 <span class="gold-text">Dollhaus Royale</span> · Raised with intention. Loved as family.</p>
                            <p style="margin:4px 0 0 0;font-size:11px;color:#B3A292;">Premium Shih Tzu · Health-Tested · Family-Raised</p>
                        </div>
                    </div>
                </body>
                </html>
                ';
                
                sendEmail($admin['email'], $subject, $body);
                
                header('Location: 2fa-verify.php');
                exit();
            } else {
                $_SESSION['dr_admin_signed_in'] = true;
                $_SESSION['dr_admin_id'] = $admin['admin_id'];
                $_SESSION['dr_admin_name'] = $admin['full_name'];
                $_SESSION['dr_admin_email'] = $admin['email'];
                $_SESSION['dr_admin_role'] = $admin['role'];
                $_SESSION['last_activity'] = time();
                $_SESSION['2fa_verified'] = true;
                
                $db->update('admin_users',
                    ['last_login' => date('Y-m-d H:i:s'), 'last_ip' => $_SERVER['REMOTE_ADDR'] ?? null],
                    'admin_id = ?',
                    [$admin['admin_id']]
                );
                
                if ($remember) {
                    setcookie('dr_admin_email', $email, time() + (86400 * 30), '/');
                    setcookie('dr_admin_remember', '1', time() + (86400 * 30), '/');
                }
                
                // Redirect to dashboard in parent folder
                header('Location: ../dashboard/dashboard.php');
                exit();
            }
        } else {
            $loginError = 'Incorrect email or password.';
            $rememberEmail = $email;
        }
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
    <link rel="icon" type="image/png" href="image/dr-logo.png" />
    <style>
        .login-timeout {
            background: #fff3e0;
            border: 1px solid #e65100;
            color: #e65100;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            display: none;
            align-items: center;
            gap: 10px;
        }
        .login-timeout.show {
            display: flex;
        }
        .login-timeout svg {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            stroke: #e65100;
            fill: none;
            stroke-width: 2;
        }
        .login-timeout .timeout-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-error {
            background: #fce4ec;
            border: 1px solid #c62828;
            color: #c62828;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            display: none;
            align-items: center;
            gap: 10px;
        }
        .login-error.show {
            display: flex;
        }
        .login-error svg {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            stroke: #c62828;
            fill: none;
            stroke-width: 2;
        }
        .login-error .error-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @media (max-width: 480px) {
            .demo-creds .cred-row {
                flex-direction: column;
                gap: 2px;
            }
        }
    </style>
</head>
<body>

<div class="login-wrap">

  <div class="login-panel">
    <div class="login-logo">
      <img src="image/dr-logo.png" class="dr-crest" alt="Dollhaus Royale crest" />
      DOLLHAUS <em>ROYALE</em>
    </div>

    <div class="login-hero">
      <h1>Every litter, every family,<br />every detail — in one ledger.</h1>
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
      <div class="eyebrow"></div>
      <h2>Sign in</h2>
      <p>Every litter, every family, every detail — in one ledger.</p>

      <?php if ($timeoutMessage): ?>
      <div class="login-timeout show" id="loginTimeout">
        <span class="timeout-icon">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /><path d="M12 6v6l4 2" /></svg>
        </span>
        <?= htmlspecialchars($timeoutMessage) ?>
      </div>
      <?php endif; ?>

      <div class="login-error <?= $loginError ? 'show' : '' ?>" id="loginError">
        <span class="error-icon">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /><path d="M12 8v4M12 16h.01" /></svg>
        </span>
        <?= $loginError ? htmlspecialchars($loginError) : 'Incorrect email or password.' ?>
      </div>

      <form method="POST" action="login.php" id="loginForm">
        <div class="field">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="you@dollhausroyale.com" autocomplete="username" required value="<?= htmlspecialchars($rememberEmail) ?>" />
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
          <label><input type="checkbox" name="keep_signed_in" id="keepSignedIn" <?= isset($_COOKIE['dr_admin_email']) ? 'checked' : '' ?> /> Keep me signed in</label>
        </div>

        <button type="submit" class="btn btn-solid">Sign in</button>
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
  const timeoutBox = document.getElementById('loginTimeout');
  
  if (emailInput) {
    emailInput.addEventListener('input', function() {
      errorBox.classList.remove('show');
    });
  }
  
  if (passwordInput) {
    passwordInput.addEventListener('input', function() {
      errorBox.classList.remove('show');
    });
  }
  
  if (timeoutBox) {
    setTimeout(function() {
      timeoutBox.classList.remove('show');
      setTimeout(function() {
        timeoutBox.style.display = 'none';
      }, 500);
    }, 5000);
  }
</script>

</body>
</html>