<?php
$userName = isset($_SESSION['dr_admin_name']) ? $_SESSION['dr_admin_name'] : 'Renata';
$userAvatar = substr($userName, 0, 1);

// Get the base URL path
$basePath = '/Code_req/dashboard/';
?>

<aside class="sidebar" id="sidebar">
    <div class="login-logo">
        <img src="<?php echo $basePath; ?>dr-logo.png" class="dr-crest" alt="Dollhaus Royale crest" />
        DOLLHAUS <em>ROYALE</em>
    </div>

    <div class="nav-group">
        <div class="nav-group-label">Household</div>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="<?php echo $basePath; ?>dashboard.php">
            <svg viewBox="0 0 24 24"><path d="M3 11l9-8 9 8" /><path d="M5 10v10h14V10" /><path d="M9 20v-6h6v6" /></svg>
            Dashboard
        </a>
        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'website-content.php') !== false ? 'active' : ''; ?>" href="<?php echo $basePath; ?>modules/wcm/website-content.php">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" /><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18" /></svg>
            Website Content Management
        </a>
        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'puppy.php') !== false ? 'active' : ''; ?>" href="<?php echo $basePath; ?>modules/ppm/puppy.php">
            <svg viewBox="0 0 24 24"><circle cx="7" cy="8" r="1.8" /><circle cx="12" cy="6" r="1.8" /><circle cx="17" cy="8" r="1.8" /><path d="M8 14.2c-2.1 0-3.7 1.7-3.7 3.6S6 21.3 8 21.3c1.3 0 2.1-.5 4-.5s2.9.5 4 .5c2.1 0 3.7-1.7 3.7-3.6s-1.6-3.6-3.7-3.6c-1.5 0-2.3.9-4 .9s-2.6-.9-4-.9Z" /></svg>
            Puppy Management 
        </a>
        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'breeding.php') !== false ? 'active' : ''; ?>" href="<?php echo $basePath; ?>modules/bm/breeding.php">
            <svg viewBox="0 0 24 24"><path d="M12 21s-7.5-4.5-9.5-8.9C1.1 9.2 3.2 4 7.8 4c2.1 0 3.4 1.2 4.2 2.4C12.8 5.2 14.1 4 16.2 4 20.8 4 22.9 9.2 21.5 12.1 19.5 16.5 12 21 12 21Z" /></svg>
            Breeding Management
        </a>
        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'adoption.php') !== false ? 'active' : ''; ?>" href="<?php echo $basePath; ?>modules/am/adoption.php">
            <svg viewBox="0 0 24 24"><path d="M20.8 8.8c0 5.2-8.8 10.2-8.8 10.2S3.2 14 3.2 8.8C3.2 6.1 5.2 4 7.8 4c1.8 0 3.4 1 4.2 2.4C12.6 5 14.2 4 16.2 4c2.6 0 4.6 2.1 4.6 4.8Z" /></svg>
            Adoption Management 
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'documents.php' ? 'active' : ''; ?>" href="<?php echo $basePath; ?>documents.php">
            <svg viewBox="0 0 24 24"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" /><path d="M14 3v5h5" /></svg>
            Documents
        </a>
    </div>

    <div class="nav-group">
        <div class="nav-group-label">Account</div>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="<?php echo $basePath; ?>settings.php">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z" /></svg>
            Settings
        </a>
        <button class="nav-link" id="signOutBtn">
            <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /><path d="M16 17l5-5-5-5" /><path d="M21 12H9" /></svg>
            Sign out
        </button>
    </div>

    <div class="sidebar-spacer"></div>


    <div class="user-row">
        <span class="avatar" id="userAvatar"><?php echo htmlspecialchars($userAvatar); ?></span>
        <div class="who">
            <span id="userName"><?php echo htmlspecialchars($userName); ?></span>
            <small>Member since 2024</small>
        </div>
    </div>
</aside>