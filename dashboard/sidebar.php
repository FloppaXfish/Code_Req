<?php
$userName = isset($_SESSION['dr_admin_name']) ? $_SESSION['dr_admin_name'] : 'Renata';
$userAvatar = substr($userName, 0, 1);

$scriptPath = $_SERVER['SCRIPT_NAME'];
$basePath = '';

if (strpos($scriptPath, '/modules/') !== false) {
    $basePath = '../../';
} else {
    $basePath = '';
}
?>

<aside class="sidebar" id="sidebar">

    <div class="login-logo">
        <img 
            src="<?php echo $basePath; ?>dr-logo.png" 
            class="dr-crest" 
            alt="Dollhaus Royale crest"
        />
        DOLLHAUS <em>ROYALE</em>
    </div>


    <div class="nav-group">

        <div class="nav-group-label">
            Household
        </div>

        <a 
            class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>dashboard.php"
        >
            <svg viewBox="0 0 24 24">
                <path d="M3 11l9-8 9 8" />
                <path d="M5 10v10h14V10" />
                <path d="M9 20v-6h6v6" />
            </svg>
            Dashboard
        </a>

        <a 
            class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'website-content.php') !== false ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/wcm/website-content.php"
        >
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9" />
                <path d="M3 12h18" />
                <path d="M12 3a14 14 0 0 1 0 18" />
                <path d="M12 3a14 14 0 0 0 0 18" />
            </svg>
            Website Content Management
        </a>

    </div>


    <div class="nav-group">

        <div class="nav-group-label">
            Breeding & Puppy Management
        </div>

        <a 
            class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'puppy.php') !== false ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/ppm/puppy.php"
        >
            <svg viewBox="0 0 24 24">
                <path d="M8 11c-2.2 0-4 1.8-4 4v1c0 2.2 1.8 4 4 4h8c2.2 0 4-1.8 4-4v-1c0-2.2-1.8-4-4-4" />
                <circle cx="7" cy="7" r="2" />
                <circle cx="12" cy="5.5" r="2" />
                <circle cx="17" cy="7" r="2" />
                <path d="M9 15h.01M15 15h.01" />
            </svg>
            Puppy Management
        </a>

        <a 
            class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'parent.php') !== false ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/pm/parent.php"
        >
            <svg viewBox="0 0 24 24">
                <circle cx="9" cy="8" r="3" />
                <circle cx="17" cy="9" r="2.5" />
                <path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6" />
                <path d="M15 14.5c3.3 0 6 2.2 6 5.5" />
            </svg>
            Parent Management
        </a>

        <a 
            class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'breeding.php') !== false ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/bm/breeding.php"
        >
            <svg viewBox="0 0 24 24">
                <path d="M20.8 8.7c0 5.5-8.8 10.3-8.8 10.3S3.2 14.2 3.2 8.7C3.2 6.1 5.2 4 7.8 4c1.7 0 3.3.9 4.2 2.3C13 4.9 14.5 4 16.2 4c2.6 0 4.6 2.1 4.6 4.7Z" />
            </svg>
            Breeding Management
        </a>

    </div>


    <div class="nav-group">

        <div class="nav-group-label">
            Adoption & Client Management
        </div>

        <a 
            class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'application.php') !== false ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/am/application.php"
        >
            <svg viewBox="0 0 24 24">
                <rect x="5" y="3" width="14" height="18" rx="2" />
                <path d="M9 3h6v3H9z" />
                <path d="M9 11h6" />
                <path d="M9 15h6" />
                <path d="M9 18h3" />
            </svg>
            Application Management
        </a>

        <a 
            class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'client.php') !== false ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/cm/client.php"
        >
            <svg viewBox="0 0 24 24">
                <circle cx="9" cy="8" r="3" />
                <circle cx="17" cy="9" r="2.5" />
                <path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6" />
                <path d="M15 14.5c3.3 0 6 2.2 6 5.5" />
            </svg>
            Client Management
        </a>

    </div>


    <div class="nav-group">

        <div class="nav-group-label">
            Account
        </div>

        <a 
            class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>settings.php"
        >
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3" />
                <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z" />
            </svg>
            Settings
        </a>

        <a 
            class="nav-link" 
            id="signOutBtn" 
            href="<?php echo $basePath; ?>logout.php"
        >
            <svg viewBox="0 0 24 24">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                <path d="M16 17l5-5-5-5" />
                <path d="M21 12H9" />
            </svg>
            Sign out
        </a>

    </div>


    <div class="sidebar-spacer"></div>

    <div class="user-row">

        <span class="avatar" id="userAvatar">
            <?php echo htmlspecialchars($userAvatar); ?>
        </span>

        <div class="who">

            <span id="userName">
                <?php echo htmlspecialchars($userName); ?>
            </span>

            <small>
                Member since 2024
            </small>

        </div>

    </div>

</aside>