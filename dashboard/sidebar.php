<?php
// Fix: Determine the correct base path dynamically
$basePath = '';

// Detect if we're in a subdirectory
if (strpos($_SERVER['SCRIPT_NAME'], '/modules/') !== false) {
    // Inside modules directory - go up 3 levels to root
    $basePath = '../../';
} else {
    // In dashboard root
    $basePath = '';
}

// Build the correct path to config
$configPath = __DIR__ . '/' . $basePath . 'config/database.php';
if (!file_exists($configPath)) {
    // Try alternative path
    $configPath = __DIR__ . '/../config/database.php';
    if (!file_exists($configPath)) {
        $configPath = __DIR__ . '/../../config/database.php';
        if (!file_exists($configPath)) {
            $configPath = __DIR__ . '/../../../config/database.php';
        }
    }
}

require_once $configPath;

// Get database instance
$db = Database::getInstance();

// Get notification counts
$newInquiries = $db->fetchOne('SELECT COUNT(*) as count FROM inquiries WHERE status = "new"');
$newInquiries = $newInquiries['count'] ?? 0;

$newApplications = $db->fetchOne('SELECT COUNT(*) as count FROM puppy_applications WHERE status = "new"');
$newApplications = $newApplications['count'] ?? 0;

$pendingExports = $db->fetchOne('SELECT COUNT(*) as count FROM export_records WHERE status IN ("pending","processing")');
$pendingExports = $pendingExports['count'] ?? 0;

$pendingBreeding = $db->fetchOne('SELECT COUNT(*) as count FROM breeding_records WHERE status IN ("planned","breeding")');
$pendingBreeding = $pendingBreeding['count'] ?? 0;

$unassignedPuppies = $db->fetchOne('SELECT COUNT(*) as count FROM puppies WHERE status = "available" AND is_visible = 1 AND is_archived = 0');
$unassignedPuppies = $unassignedPuppies['count'] ?? 0;

$unpublishedGallery = $db->fetchOne('SELECT COUNT(*) as count FROM gallery_media WHERE is_published = 0');
$unpublishedGallery = $unpublishedGallery['count'] ?? 0;

$unreadAudit = $db->fetchOne('SELECT COUNT(*) as count FROM audit_log WHERE DATE(created_at) = CURDATE()');
$unreadAudit = $unreadAudit['count'] ?? 0;

// Calculate total notifications
$totalNotifications = $newInquiries + $newApplications + $pendingExports + $pendingBreeding + $unassignedPuppies + $unpublishedGallery + $unreadAudit;

// Store notification data in session for real-time updates
$_SESSION['notifications'] = [
    'total' => $totalNotifications,
    'newInquiries' => $newInquiries,
    'newApplications' => $newApplications,
    'pendingExports' => $pendingExports,
    'pendingBreeding' => $pendingBreeding,
    'unassignedPuppies' => $unassignedPuppies,
    'unpublishedGallery' => $unpublishedGallery,
    'unreadAudit' => $unreadAudit
];

$userName = isset($_SESSION['dr_admin_name']) ? $_SESSION['dr_admin_name'] : 'Renata';
$userAvatar = substr($userName, 0, 1);

// Recalculate basePath for URLs
$basePath = '';
if (strpos($_SERVER['SCRIPT_NAME'], '/modules/') !== false) {
    $basePath = '../../';
} else {
    $basePath = '';
}

// Determine which module is active for highlighting
$currentScript = basename($_SERVER['PHP_SELF']);
$currentPath = $_SERVER['SCRIPT_NAME'];

// Helper function to check if a module is active
function isModuleActive($pattern, $currentPath, $currentScript) {
    if (strpos($currentPath, $pattern) !== false) {
        return true;
    }
    return false;
}

// Generate a unique sidebar ID for this session to store scroll position
$sidebarStorageKey = 'sidebar_scroll_' . session_id();
?>

<aside class="sidebar" id="sidebar">

    <div class="login-logo">
        <img 
            src="<?php echo $basePath; ?>img/dr-logo.png" 
            class="dr-crest" 
            alt="Dollhaus Royale crest"
        />
        DOLLHAUS <em>ROYALE</em>
    </div>

    <!-- System Overview -->
    <div class="nav-group">
        <div class="nav-group-label">System Overview</div>

        <a 
            class="nav-link sidebar-link <?php echo $currentScript == 'dashboard.php' ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>dashboard.php"
            data-scroll-preserve="true"
        >
            <svg viewBox="0 0 24 24">
                <path d="M3 11l9-8 9 8" />
                <path d="M5 10v10h14V10" />
                <path d="M9 20v-6h6v6" />
            </svg>
            Dashboard
           
        </a>
    </div>

    <!-- Website Management -->
    <div class="nav-group">
        <div class="nav-group-label">Website Management</div>

         <a 
            class="nav-link sidebar-link <?php echo isModuleActive('testimonial.php', $currentPath, $currentScript) ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/tm/testimonial.php"
            data-scroll-preserve="true"
        >
            <svg viewBox="0 0 24 24">
                <path d="M5 18.5V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v7.5a2 2 0 0 1-2 2H9l-4 3v-3.5A2 2 0 0 1 5 18.5z" />
                <path d="M8.5 10.5h7" />
                <path d="M8.5 14h4" />
            </svg>
            Testimonal Management
        </a>

        <a 
            class="nav-link sidebar-link <?php echo isModuleActive('website-content.php', $currentPath, $currentScript) ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/wcm/website-content.php"
            data-scroll-preserve="true"
        >
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9" />
                <path d="M3 12h18" />
                <path d="M12 3a14 14 0 0 1 0 18" />
                <path d="M12 3a14 14 0 0 0 0 18" />
            </svg>
            Website Content
        </a>

        <a 
            class="nav-link sidebar-link <?php echo isModuleActive('gallery.php', $currentPath, $currentScript) ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/gm/gallery.php"
            data-scroll-preserve="true"
        >
            <svg viewBox="0 0 24 24">
                <rect x="3" y="3" width="18" height="18" rx="2" />
                <circle cx="8.5" cy="8.5" r="1.5" />
                <path d="M21 15l-5-5-5 5-5-5-5 5" />
            </svg>
            Gallery Management
           
        </a>
    </div>

    <!-- Breeding Management -->
    <div class="nav-group">
        <div class="nav-group-label">Breeding Management</div>

        <a 
            class="nav-link sidebar-link <?php echo isModuleActive('parent.php', $currentPath, $currentScript) ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/pm/parent.php"
            data-scroll-preserve="true"
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
            class="nav-link sidebar-link <?php echo isModuleActive('breeding.php', $currentPath, $currentScript) ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/bm/breeding.php"
            data-scroll-preserve="true"
        >
            <svg viewBox="0 0 24 24">
                <path d="M20.8 8.7c0 5.5-8.8 10.3-8.8 10.3S3.2 14.2 3.2 8.7C3.2 6.1 5.2 4 7.8 4c1.7 0 3.3.9 4.2 2.3C13 4.9 14.5 4 16.2 4c2.6 0 4.6 2.1 4.6 4.7Z" />
            </svg>
            Breeding Management
            <?php if ($pendingBreeding > 0): ?>
                <span class="nav-badge has-notification" id="badgeBreeding"><?= $pendingBreeding ?></span>
            <?php else: ?>
                <span class="nav-badge hidden" id="badgeBreeding">0</span>
            <?php endif; ?>
        </a>

        <a 
            class="nav-link sidebar-link <?php echo isModuleActive('puppy.php', $currentPath, $currentScript) ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/ppm/puppy.php"
            data-scroll-preserve="true"
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
    </div>

    <!-- Client & Adoption Management -->
    <div class="nav-group">
        <div class="nav-group-label">Client &amp; Adoption</div>

        <a 
            class="nav-link sidebar-link <?php echo isModuleActive('application.php', $currentPath, $currentScript) ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/am/application.php"
            data-scroll-preserve="true"
        >
            <svg viewBox="0 0 24 24">
                <rect x="5" y="3" width="14" height="18" rx="2" />
                <path d="M9 3h6v3H9z" />
                <path d="M9 11h6" />
                <path d="M9 15h6" />
                <path d="M9 18h3" />
            </svg>
            Application Management
            <?php if ($newApplications > 0): ?>
                <span class="nav-badge has-notification" id="badgeApplications"><?= $newApplications ?></span>
            <?php else: ?>
                <span class="nav-badge hidden" id="badgeApplications">0</span>
            <?php endif; ?>
        </a>

        <a 
            class="nav-link sidebar-link <?php echo isModuleActive('client.php', $currentPath, $currentScript) ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/cm/client.php"
            data-scroll-preserve="true"
        >
            <svg viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
            </svg>
            Client Management
        </a>

        <a 
            class="nav-link sidebar-link <?php echo isModuleActive('inquiry.php', $currentPath, $currentScript) ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/im/inquiry.php"
            data-scroll-preserve="true"
        >
            <svg viewBox="0 0 24 24">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
            </svg>
            Inquiry Management
            <?php if ($newInquiries > 0): ?>
                <span class="nav-badge has-notification" id="badgeInquiries"><?= $newInquiries ?></span>
            <?php else: ?>
                <span class="nav-badge hidden" id="badgeInquiries">0</span>
            <?php endif; ?>
        </a>
    </div>

    <!-- Ownership & Records -->
    <div class="nav-group">
        <div class="nav-group-label">Ownership &amp; Records</div>

        <a 
            class="nav-link sidebar-link <?php echo isModuleActive('ownership.php', $currentPath, $currentScript) ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/om/ownership.php"
            data-scroll-preserve="true"
        >
            <svg viewBox="0 0 24 24">
                <path d="M12 2C7.58 2 4 5.58 4 10c0 2.76 1.36 5.2 3.5 6.66V18a2 2 0 0 0 2 2h5a2 2 0 0 0 2-2v-1.34c2.14-1.46 3.5-3.9 3.5-6.66 0-4.42-3.58-8-8-8z" />
                <path d="M8 16h8" />
            </svg>
            Ownership Management
        </a>

        <a 
            class="nav-link sidebar-link <?php echo isModuleActive('export.php', $currentPath, $currentScript) ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/em/export.php"
            data-scroll-preserve="true"
        >
            <svg viewBox="0 0 24 24">
                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 1 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6" />
            </svg>
            Export Management
            <?php if ($pendingExports > 0): ?>
                <span class="nav-badge has-notification" id="badgeExports"><?= $pendingExports ?></span>
            <?php else: ?>
                <span class="nav-badge hidden" id="badgeExports">0</span>
            <?php endif; ?>
        </a>
    </div>

    <!-- Reports & Analytics -->
    <div class="nav-group">
        <div class="nav-group-label">Reports &amp; Analytics</div>

        <a 
            class="nav-link sidebar-link <?php echo isModuleActive('report.php', $currentPath, $currentScript) ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/rm/report.php"
            data-scroll-preserve="true"
        >
            <svg viewBox="0 0 24 24">
                <rect x="3" y="3" width="18" height="18" rx="2" />
                <path d="M7 16l4-4 4 4 4-4" />
                <path d="M7 12l4-4 4 4 4-4" />
            </svg>
            Report Management
        </a>

        <a 
            class="nav-link sidebar-link <?php echo isModuleActive('audit.php', $currentPath, $currentScript) ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/aum/audit.php"
            data-scroll-preserve="true"
        >
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 6v6l4 2" />
            </svg>
            Audit Management
            <?php if ($unreadAudit > 0): ?>
                <span class="nav-badge has-notification" id="badgeAudit"><?= $unreadAudit ?></span>
            <?php else: ?>
                <span class="nav-badge hidden" id="badgeAudit">0</span>
            <?php endif; ?>
        </a>

       
    </div>

    <!-- Account -->
    <div class="nav-group">
        <div class="nav-group-label">Account</div>

                   <a 
        class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'user-manual.php') !== false ? 'active' : ''; ?>" 
        href="<?php echo $basePath; ?>modules/um/user-manual.php"
    >
        <svg viewBox="0 0 24 24">
            <path d="M4 6h16" />
            <path d="M4 12h16" />
            <path d="M4 18h10" />
        </svg>
        User Manual
    </a>

         <a 
            class="nav-link sidebar-link <?php echo isModuleActive('account.php', $currentPath, $currentScript) ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>modules/accm/account.php"
            data-scroll-preserve="true"
        >
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="4" />
                <path d="M4 20c1.5-3 4.5-4.5 8-4.5s6.5 1.5 8 4.5" />
            </svg>
            Account
        </a>

        <a 
            class="nav-link sidebar-link <?php echo $currentScript == 'settings.php' ? 'active' : ''; ?>" 
            href="<?php echo $basePath; ?>settings.php"
            data-scroll-preserve="true"
        >
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3" />
                <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z" />
            </svg>
            Settings
        </a>



        <a 
            class="nav-link sidebar-link" 
            id="signOutBtn" 
            href="<?php echo $basePath; ?>logout.php"
            data-scroll-preserve="true"
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

<!-- Notification Sound -->
<audio id="notificationSound" preload="auto">
    <source src="<?php echo $basePath; ?>audio/notification.mp3" type="audio/mpeg">
</audio>

<!-- Session Timeout Warning Modal -->
<div class="session-modal-overlay" id="sessionModalOverlay" style="display:none;">
    <div class="session-modal">
        <div class="session-modal-content">
            <div class="session-modal-icon">
                <svg viewBox="0 0 24 24" width="48" height="48">
                    <circle cx="12" cy="12" r="10" stroke="#CC9A3D" stroke-width="2" fill="none"/>
                    <path d="M12 6v6l4 2" stroke="#CC9A3D" stroke-width="2" fill="none"/>
                </svg>
            </div>
            <h3>Session Expiring Soon</h3>
            <p>You will be automatically logged out in <strong id="countdownTimer">30</strong> seconds.</p>
            <p style="font-size:0.85rem;color:#9C8A78;">Please click the button below to stay logged in.</p>
            <button class="session-modal-btn" id="stayLoggedInBtn">Stay Logged In</button>
        </div>
    </div>
</div>

<style>
    /* Nav Badge Styles */
    .nav-link {
        position: relative;
    }

    .nav-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        margin-left: auto;
        font-size: 0.6rem;
        font-weight: 700;
        color: #fff;
        background: var(--maroon);
        border-radius: 20px;
        font-family: 'Inter', sans-serif;
        line-height: 1;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .nav-badge.badge-update {
        animation: badgePop 0.4s ease;
    }

    .nav-badge.hidden {
        display: none !important;
    }

    .nav-link.active .nav-badge {
        background: #fff;
        color: var(--maroon);
    }

    @keyframes badgePop {
        0% { transform: scale(1); }
        30% { transform: scale(1.5); }
        70% { transform: scale(0.9); }
        100% { transform: scale(1); }
    }

    @keyframes badgePulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.08); }
    }

    .nav-badge.has-notification {
        animation: badgePulse 2s ease-in-out infinite;
    }

    /* Session Modal Styles */
    .session-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(6px);
        animation: sessionFadeIn 0.3s ease;
    }

    .session-modal-overlay.show {
        display: flex;
    }

    @keyframes sessionFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .session-modal {
        background: #fff;
        border-radius: 20px;
        max-width: 420px;
        width: 90%;
        padding: 40px 36px 32px;
        box-shadow: 0 24px 64px rgba(0, 0, 0, 0.3);
        animation: sessionSlideIn 0.3s ease;
        text-align: center;
    }

    @keyframes sessionSlideIn {
        from { transform: translateY(-30px) scale(0.95); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
    }

    .session-modal-icon {
        margin-bottom: 16px;
    }

    .session-modal-icon svg {
        animation: sessionPulse 1.5s ease-in-out infinite;
    }

    @keyframes sessionPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .session-modal h3 {
        font-family: 'Playfair Display', serif;
        font-size: 1.4rem;
        font-weight: 500;
        color: var(--maroon-dark);
        margin: 0 0 8px 0;
    }

    .session-modal p {
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        color: var(--ink-soft);
        margin: 8px 0;
    }

    .session-modal p strong {
        color: var(--maroon);
        font-size: 1.2rem;
    }

    .session-modal-btn {
        margin-top: 20px;
        padding: 12px 40px;
        background: var(--maroon);
        color: #fff;
        border: none;
        border-radius: 40px;
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(149, 6, 6, 0.25);
    }

    .session-modal-btn:hover {
        background: var(--maroon-light);
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(149, 6, 6, 0.35);
    }

    .session-modal-btn:active {
        transform: scale(0.97);
    }

    @media (max-width: 480px) {
        .session-modal {
            padding: 28px 20px 24px;
            margin: 16px;
        }
        .session-modal h3 {
            font-size: 1.2rem;
        }
        .session-modal p {
            font-size: 0.85rem;
        }
        .session-modal-btn {
            padding: 10px 28px;
            font-size: 0.8rem;
        }
    }
</style>

<script>
    // ============================================
    // SIDEBAR SCROLL POSITION PRESERVATION
    // ============================================
    
    (function() {
        // Generate a unique key for this session
        var storageKey = 'sidebar_scroll_' + '<?php echo session_id(); ?>';
        
        // Function to save sidebar scroll position
        function saveSidebarScroll() {
            var sidebar = document.getElementById('sidebar');
            if (sidebar) {
                var scrollTop = sidebar.scrollTop;
                if (scrollTop > 0) {
                    try {
                        sessionStorage.setItem(storageKey, scrollTop);
                    } catch(e) {
                        // Session storage not available
                    }
                }
            }
        }
        
        // Function to restore sidebar scroll position
        function restoreSidebarScroll() {
            var sidebar = document.getElementById('sidebar');
            if (sidebar) {
                try {
                    var savedScroll = sessionStorage.getItem(storageKey);
                    if (savedScroll !== null) {
                        var scrollPosition = parseInt(savedScroll);
                        if (scrollPosition > 0) {
                            // Use requestAnimationFrame to ensure DOM is ready
                            requestAnimationFrame(function() {
                                sidebar.scrollTop = scrollPosition;
                            });
                        }
                        // Clear after restoring to prevent issues
                        sessionStorage.removeItem(storageKey);
                    }
                } catch(e) {
                    // Session storage not available
                }
            }
        }
        
        // Save scroll position on navigation
        document.addEventListener('DOMContentLoaded', function() {
            // Restore scroll position
            restoreSidebarScroll();
            
            // Set up scroll event listener to save position
            var sidebar = document.getElementById('sidebar');
            if (sidebar) {
                // Save scroll position periodically while scrolling
                var scrollTimeout = null;
                sidebar.addEventListener('scroll', function() {
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(function() {
                        saveSidebarScroll();
                    }, 200);
                });
            }
            
            // Save scroll position before page unload
            window.addEventListener('beforeunload', function() {
                saveSidebarScroll();
            });
            
            // Handle sidebar link clicks to save scroll position
            document.querySelectorAll('.sidebar-link[data-scroll-preserve="true"]').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    // Save current scroll position before navigation
                    saveSidebarScroll();
                });
            });
            
            // Also handle the sign out button
            var signOutBtn = document.getElementById('signOutBtn');
            if (signOutBtn) {
                signOutBtn.addEventListener('click', function(e) {
                    saveSidebarScroll();
                });
            }
        });
    })();

    // ============================================
    // NOTIFICATION SYSTEM
    // ============================================

    // Store initial notification values
    var previousNotifications = {
        total: <?= $totalNotifications ?>,
        inquiries: <?= $newInquiries ?>,
        applications: <?= $newApplications ?>,
        exports: <?= $pendingExports ?>,
        breeding: <?= $pendingBreeding ?>,
        puppies: <?= $unassignedPuppies ?>,
        gallery: <?= $unpublishedGallery ?>,
        audit: <?= $unreadAudit ?>
    };

    var isFirstLoad = true;

    // Function to fetch latest notifications
    function fetchNotifications() {
        var formData = new FormData();
        formData.append('action', 'get_notifications');
        
        fetch('<?php echo $basePath; ?>modules/aum/audit.php', {
            method: 'POST',
            body: formData
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                updateBadges(data);
            }
        })
        .catch(function(err) {
            // Silent fail - don't show errors for polling
        });
    }

    // Function to update badge numbers
    function updateBadges(data) {
        var badges = {
            total: document.getElementById('badgeTotal'),
            inquiries: document.getElementById('badgeInquiries'),
            applications: document.getElementById('badgeApplications'),
            exports: document.getElementById('badgeExports'),
            breeding: document.getElementById('badgeBreeding'),
            puppies: document.getElementById('badgePuppies'),
            gallery: document.getElementById('badgeGallery'),
            audit: document.getElementById('badgeAudit')
        };

        // Update each badge
        var newValues = {
            total: data.total || 0,
            inquiries: data.newInquiries || 0,
            applications: data.newApplications || 0,
            exports: data.pendingExports || 0,
            breeding: data.pendingBreeding || 0,
            puppies: data.unassignedPuppies || 0,
            gallery: data.unpublishedGallery || 0,
            audit: data.unreadAudit || 0
        };

        // Check for changes and update
        Object.keys(newValues).forEach(function(key) {
            var badge = badges[key];
            if (!badge) return;
            
            var newValue = newValues[key];
            var oldValue = previousNotifications[key];
            
            // Update the badge text and visibility
            if (newValue > 0) {
                badge.textContent = newValue;
                badge.classList.remove('hidden');
                badge.classList.add('has-notification');
            } else {
                badge.textContent = '0';
                badge.classList.add('hidden');
                badge.classList.remove('has-notification');
            }
            
            // If value changed and it increased, play animation and sound
            if (newValue > oldValue && !isFirstLoad) {
                // Pop animation
                badge.classList.remove('badge-update');
                // Force reflow
                void badge.offsetWidth;
                badge.classList.add('badge-update');
                
                // Play sound if there's a new notification
                playNotificationSound();
            }
            
            // Update stored value
            previousNotifications[key] = newValue;
        });
        
        isFirstLoad = false;
    }

    // Play notification sound
    function playNotificationSound() {
        try {
            var audio = document.getElementById('notificationSound');
            if (audio) {
                audio.play().catch(function(e) {
                    // Autoplay was prevented - user needs to interact first
                });
            }
        } catch(e) {
            // Silent fail
        }
    }

    // ============================================
    // SESSION TIMEOUT SYSTEM
    // ============================================

    var SESSION_TIMEOUT = 5 * 60 * 1000; // 5 minutes
    var WARNING_TIME = 30 * 1000; // 30 seconds before timeout

    var sessionTimer = null;
    var warningTimer = null;
    var countdownTimer = null;
    var countdownValue = 30;

    function resetSessionTimer() {
        clearTimeout(sessionTimer);
        clearTimeout(warningTimer);
        clearInterval(countdownTimer);

        var modal = document.getElementById('sessionModalOverlay');
        modal.classList.remove('show');
        modal.style.display = 'none';

        countdownValue = 30;
        document.getElementById('countdownTimer').textContent = countdownValue;

        startSessionTimers();
    }

    function startSessionTimers() {
        warningTimer = setTimeout(function() {
            showWarningModal();
        }, SESSION_TIMEOUT - WARNING_TIME);

        sessionTimer = setTimeout(function() {
            window.location.href = '<?php echo $basePath; ?>logout.php?timeout=1';
        }, SESSION_TIMEOUT);
    }

    function showWarningModal() {
        var modal = document.getElementById('sessionModalOverlay');
        modal.style.display = 'flex';
        setTimeout(function() {
            modal.classList.add('show');
        }, 50);

        countdownValue = 30;
        document.getElementById('countdownTimer').textContent = countdownValue;

        countdownTimer = setInterval(function() {
            countdownValue--;
            document.getElementById('countdownTimer').textContent = countdownValue;

            if (countdownValue <= 0) {
                clearInterval(countdownTimer);
                window.location.href = '<?php echo $basePath; ?>logout.php?timeout=1';
            }
        }, 1000);
    }

    function stayLoggedIn() {
        resetSessionTimer();
    }

    // ============================================
    // INITIALIZATION
    // ============================================

    document.addEventListener('DOMContentLoaded', function() {
        // Session timers
        startSessionTimers();

        // User activity events
        var events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click', 'keydown', 'input', 'change'];
        events.forEach(function(event) {
            document.addEventListener(event, function() {
                if (document.getElementById('sessionModalOverlay').style.display === 'flex') {
                    resetSessionTimer();
                } else {
                    clearTimeout(sessionTimer);
                    clearTimeout(warningTimer);
                    clearInterval(countdownTimer);
                    startSessionTimers();
                }
            }, { passive: true });
        });

        // Stay logged in button
        document.getElementById('stayLoggedInBtn').addEventListener('click', function() {
            stayLoggedIn();
        });

        // Click on modal background
        document.getElementById('sessionModalOverlay').addEventListener('click', function(e) {
            if (e.target === this) {
                stayLoggedIn();
            }
        });

        // Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('sessionModalOverlay').style.display === 'flex') {
                stayLoggedIn();
            }
        });

        // Notification polling
        fetchNotifications();
        setInterval(fetchNotifications, 10000);
    });

    var basePath = '<?php echo $basePath; ?>';
</script>