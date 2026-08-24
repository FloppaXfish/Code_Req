<?php
session_start();

if (!isset($_SESSION['dr_admin_signed_in']) || $_SESSION['dr_admin_signed_in'] !== true) {
    header('Location: login.php');
    exit();
}

$userName = isset($_SESSION['dr_admin_name']) ? $_SESSION['dr_admin_name'] : 'there';
$userFirstName = explode(' ', $userName)[0];
$userRole = isset($_SESSION['dr_admin_role']) ? $_SESSION['dr_admin_role'] : 'admin';

require_once '../config/database.php';
$db = Database::getInstance();

$totalPuppies = $db->fetchOne('SELECT COUNT(*) as count FROM puppies WHERE is_archived = 0');
$totalPuppies = $totalPuppies['count'] ?? 0;

$availablePuppies = $db->fetchOne('SELECT COUNT(*) as count FROM puppies WHERE status = "available" AND is_visible = 1 AND is_archived = 0');
$availablePuppies = $availablePuppies['count'] ?? 0;

$totalInquiries = $db->fetchOne('SELECT COUNT(*) as count FROM inquiries');
$totalInquiries = $totalInquiries['count'] ?? 0;

$newInquiries = $db->fetchOne('SELECT COUNT(*) as count FROM inquiries WHERE status = "new"');
$newInquiries = $newInquiries['count'] ?? 0;

$totalApplications = $db->fetchOne('SELECT COUNT(*) as count FROM puppy_applications');
$totalApplications = $totalApplications['count'] ?? 0;

$newApplications = $db->fetchOne('SELECT COUNT(*) as count FROM puppy_applications WHERE status = "new"');
$newApplications = $newApplications['count'] ?? 0;

$recentInquiries = $db->fetchAll('SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 5');
$recentPuppies = $db->fetchAll('SELECT * FROM puppies WHERE is_archived = 0 ORDER BY created_at DESC LIMIT 5');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard · Dollhaus Royale</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;1,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/portal.css" />
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="dash-wrap">

    <div class="mobile-topbar">
        <div class="login-logo">
            <img src="image/dr-logo.png" class="dr-crest" alt="Dollhaus Royale crest" />
            DOLLHAUS <em>ROYALE</em>
        </div>
        <button class="menu-btn" id="menuBtn" aria-label="Open menu">
            <svg viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" fill="none" /></svg>
        </button>
    </div>

    <?php include 'sidebar.php'; ?>

    <main class="main">
        <div class="main-header">
            <div>
                <div class="eyebrow" id="welcomeLabel">Welcome back, <?php echo htmlspecialchars($userFirstName); ?></div>
                <h1>Your kennel at a glance</h1>
            </div>
            <div class="header-actions">
                <span style="font-size:0.8rem;color:#9C8A78;font-family:'Inter',sans-serif;background:#f8f4ed;padding:6px 16px;border-radius:20px;">
                    Role: <?= ucfirst(htmlspecialchars($userRole)) ?>
                </span>
            </div>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="label">Total Puppies</div>
                <div class="value"><?= $totalPuppies ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Available Puppies</div>
                <div class="value"><?= $availablePuppies ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Total Inquiries</div>
                <div class="value"><?= $totalInquiries ?></div>
            </div>
            <div class="stat-card">
                <div class="label">New Inquiries</div>
                <div class="value"><?= $newInquiries ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Total Applications</div>
                <div class="value"><?= $totalApplications ?></div>
            </div>
            <div class="stat-card">
                <div class="label">New Applications</div>
                <div class="value"><?= $newApplications ?></div>
            </div>
        </div>

        <div class="content-grid">      
            <div>
                <div class="card">
                    <div class="card-header">
                        <h3>Recent Inquiries</h3>
                        <a href="#" class="link">View all</a>
                    </div>
                    <?php if (!empty($recentInquiries)): ?>
                        <?php foreach ($recentInquiries as $inquiry): ?>
                        <div class="activity-row">
                            <span class="icon">
                                <svg viewBox="0 0 24 24"><path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5 8.4 8.4 0 0 1-3.9-.9L3 21l1.9-5.6A8.4 8.4 0 0 1 4 11.5 8.5 8.5 0 0 1 12.5 3 8.5 8.5 0 0 1 21 11.5Z" /></svg>
                            </span>
                            <div>
                                <p>New inquiry from <strong><?= htmlspecialchars($inquiry['full_name']) ?></strong></p>
                                <small><?= date('M d, Y · g:i A', strtotime($inquiry['created_at'])) ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="activity-row">
                            <p style="color:#9C8A78;">No recent inquiries yet.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Recent Puppies</h3>
                        <a href="modules/ppm/puppy.php" class="link">Manage</a>
                    </div>
                    <?php if (!empty($recentPuppies)): ?>
                        <?php foreach ($recentPuppies as $puppy): ?>
                        <div class="activity-row">
                            <span class="icon">
                                <svg viewBox="0 0 24 24"><circle cx="7" cy="8" r="1.8" /><circle cx="12" cy="6" r="1.8" /><circle cx="17" cy="8" r="1.8" /><path d="M8 14.2c-2.1 0-3.7 1.7-3.7 3.6S6 21.3 8 21.3c1.3 0 2.1-.5 4-.5s2.9.5 4 .5c2.1 0 3.7-1.7 3.7-3.6s-1.6-3.6-3.7-3.6c-1.5 0-2.3.9-4 .9s-2.6-.9-4-.9Z" /></svg>
                            </span>
                            <div>
                                <p><strong><?= htmlspecialchars($puppy['name']) ?></strong> · <?= ucfirst($puppy['status']) ?></p>
                                <small><?= date('M d, Y', strtotime($puppy['created_at'])) ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="activity-row">
                            <p style="color:#9C8A78;">No puppies added yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="card note-card">
                    <div class="eyebrow">Quick Actions</div>
                    <div style="display:flex;flex-direction:column;gap:10px;margin-top:12px;">
                        <a href="modules/ppm/puppy.php" class="btn btn-solid" style="text-align:center;text-decoration:none;padding:12px;">Manage Puppies</a>
                        <a href="modules/wcm/website-content.php" class="btn btn-outline" style="text-align:center;text-decoration:none;padding:12px;border:1px solid rgba(139,26,26,0.2);border-radius:8px;color:#2A1810;font-family:'Inter',sans-serif;font-weight:600;font-size:0.85rem;">Manage Website Content</a>
                    </div>
                </div>

                <div class="card" style="background:#f8f4ed;">
                    <div class="eyebrow">Account Info</div>
                    <div style="margin-top:12px;">
                        <p style="font-family:'Inter',sans-serif;font-size:0.9rem;color:#2A1810;margin:4px 0;">
                            <strong>Name:</strong> <?= htmlspecialchars($userName) ?>
                        </p>
                        <p style="font-family:'Inter',sans-serif;font-size:0.9rem;color:#2A1810;margin:4px 0;">
                            <strong>Email:</strong> <?= htmlspecialchars($_SESSION['dr_admin_email'] ?? '') ?>
                        </p>
                        <p style="font-family:'Inter',sans-serif;font-size:0.9rem;color:#2A1810;margin:4px 0;">
                            <strong>Role:</strong> <?= ucfirst(htmlspecialchars($userRole)) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

</div>

<script>
    const name = '<?php echo htmlspecialchars($userName); ?>';
    const firstName = name.split(' ')[0] || 'there';
    document.getElementById('welcomeLabel').textContent = 'Welcome back, ' + firstName;

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const menuBtn = document.getElementById('menuBtn');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('show');
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    }
    menuBtn.addEventListener('click', openSidebar);
    overlay.addEventListener('click', closeSidebar);
</script>

</body>
</html>