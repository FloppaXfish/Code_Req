<?php
$userName = isset($_SESSION['dr_admin_name']) ? $_SESSION['dr_admin_name'] : 'Renata';
$userAvatar = substr($userName, 0, 1);
?>

<aside class="sidebar" id="sidebar">
    <div class="login-logo">
        <img src="dr-logo.png" class="dr-crest" alt="Dollhaus Royale crest" />
        DOLLHAUS <em>ROYALE</em>
    </div>

    <div class="nav-group">
        <div class="nav-group-label">Household</div>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
            <svg viewBox="0 0 24 24"><path d="M3 11l9-8 9 8" /><path d="M5 10v10h14V10" /><path d="M9 20v-6h6v6" /></svg>
            Dashboard
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'my-puppy.php' ? 'active' : ''; ?>" href="my-puppy.php">
            <svg viewBox="0 0 24 24"><circle cx="7" cy="8" r="1.8" /><circle cx="12" cy="6" r="1.8" /><circle cx="17" cy="8" r="1.8" /><path d="M8 14.2c-2.1 0-3.7 1.7-3.7 3.6S6 21.3 8 21.3c1.3 0 2.1-.5 4-.5s2.9.5 4 .5c2.1 0 3.7-1.7 3.7-3.6s-1.6-3.6-3.7-3.6c-1.5 0-2.3.9-4 .9s-2.6-.9-4-.9Z" /></svg>
            My Puppy
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>" href="messages.php">
            <svg viewBox="0 0 24 24"><path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5 8.4 8.4 0 0 1-3.9-.9L3 21l1.9-5.6A8.4 8.4 0 0 1 4 11.5 8.5 8.5 0 0 1 12.5 3 8.5 8.5 0 0 1 21 11.5Z" /></svg>
            Messages
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'appointments.php' ? 'active' : ''; ?>" href="appointments.php">
            <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M16 3v4M8 3v4M3 10h18" /></svg>
            Appointments
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'documents.php' ? 'active' : ''; ?>" href="documents.php">
            <svg viewBox="0 0 24 24"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" /><path d="M14 3v5h5" /></svg>
            Documents
        </a>
    </div>

    <div class="nav-group">
        <div class="nav-group-label">Account</div>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z" /></svg>
            Settings
        </a>
        <button class="nav-link" id="signOutBtn">
            <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /><path d="M16 17l5-5-5-5" /><path d="M21 12H9" /></svg>
            Sign out
        </button>
    </div>

    <div class="sidebar-spacer"></div>

    <div class="puppy-card">
        <img src="https://images.unsplash.com/photo-1604579493738-c12b9c0ef36a?w=300&h=200&fit=crop" alt="Buttercup" />
        <div class="body">
            <div class="tag">Reserved Puppy</div>
            <h4>Buttercup</h4>
            <p>Female · Gold &amp; White · Ready Oct 12</p>
        </div>
    </div>

    <div class="user-row">
        <span class="avatar" id="userAvatar"><?php echo htmlspecialchars($userAvatar); ?></span>
        <div class="who">
            <span id="userName"><?php echo htmlspecialchars($userName); ?></span>
            <small>Member since 2024</small>
        </div>
    </div>
</aside>