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

// Puppy Stats
$totalPuppies = $db->fetchOne('SELECT COUNT(*) as count FROM puppies WHERE is_archived = 0');
$totalPuppies = $totalPuppies['count'] ?? 0;

$availablePuppies = $db->fetchOne('SELECT COUNT(*) as count FROM puppies WHERE status = "available" AND is_visible = 1 AND is_archived = 0');
$availablePuppies = $availablePuppies['count'] ?? 0;

$reservedPuppies = $db->fetchOne('SELECT COUNT(*) as count FROM puppies WHERE status = "reserved" AND is_archived = 0');
$reservedPuppies = $reservedPuppies['count'] ?? 0;

$soldPuppies = $db->fetchOne('SELECT COUNT(*) as count FROM puppies WHERE status = "sold" AND is_archived = 0');
$soldPuppies = $soldPuppies['count'] ?? 0;

// Inquiry Stats
$totalInquiries = $db->fetchOne('SELECT COUNT(*) as count FROM inquiries');
$totalInquiries = $totalInquiries['count'] ?? 0;

$newInquiries = $db->fetchOne('SELECT COUNT(*) as count FROM inquiries WHERE status = "new"');
$newInquiries = $newInquiries['count'] ?? 0;

$contactedInquiries = $db->fetchOne('SELECT COUNT(*) as count FROM inquiries WHERE status = "contacted"');
$contactedInquiries = $contactedInquiries['count'] ?? 0;

$repliedInquiries = $db->fetchOne('SELECT COUNT(*) as count FROM inquiries WHERE status = "replied"');
$repliedInquiries = $repliedInquiries['count'] ?? 0;

// Application Stats
$totalApplications = $db->fetchOne('SELECT COUNT(*) as count FROM puppy_applications');
$totalApplications = $totalApplications['count'] ?? 0;

$newApplications = $db->fetchOne('SELECT COUNT(*) as count FROM puppy_applications WHERE status = "new"');
$newApplications = $newApplications['count'] ?? 0;

$approvedApplications = $db->fetchOne('SELECT COUNT(*) as count FROM puppy_applications WHERE status = "approved"');
$approvedApplications = $approvedApplications['count'] ?? 0;

$rejectedApplications = $db->fetchOne('SELECT COUNT(*) as count FROM puppy_applications WHERE status = "rejected"');
$rejectedApplications = $rejectedApplications['count'] ?? 0;

// Client Stats
$totalClients = $db->fetchOne('SELECT COUNT(*) as count FROM clients');
$totalClients = $totalClients['count'] ?? 0;

$activeClients = $db->fetchOne('SELECT COUNT(*) as count FROM clients WHERE is_active = 1');
$activeClients = $activeClients['count'] ?? 0;

$adoptedClients = $db->fetchOne('SELECT COUNT(*) as count FROM clients WHERE status = "adopted"');
$adoptedClients = $adoptedClients['count'] ?? 0;

// Breeding Stats
$totalBreedings = $db->fetchOne('SELECT COUNT(*) as count FROM breeding_records');
$totalBreedings = $totalBreedings['count'] ?? 0;

$activeBreedings = $db->fetchOne('SELECT COUNT(*) as count FROM breeding_records WHERE status IN ("breeding","confirmed","pregnant")');
$activeBreedings = $activeBreedings['count'] ?? 0;

$pregnantBreedings = $db->fetchOne('SELECT COUNT(*) as count FROM breeding_records WHERE status = "pregnant"');
$pregnantBreedings = $pregnantBreedings['count'] ?? 0;

// Export Stats
$totalExports = $db->fetchOne('SELECT COUNT(*) as count FROM export_records');
$totalExports = $totalExports['count'] ?? 0;

$pendingExports = $db->fetchOne('SELECT COUNT(*) as count FROM export_records WHERE status IN ("pending","processing")');
$pendingExports = $pendingExports['count'] ?? 0;

$shippedExports = $db->fetchOne('SELECT COUNT(*) as count FROM export_records WHERE status IN ("shipped","delivered","completed")');
$shippedExports = $shippedExports['count'] ?? 0;

// Gallery Stats
$totalGallery = $db->fetchOne('SELECT COUNT(*) as count FROM gallery_media');
$totalGallery = $totalGallery['count'] ?? 0;

$publishedGallery = $db->fetchOne('SELECT COUNT(*) as count FROM gallery_media WHERE is_published = 1');
$publishedGallery = $publishedGallery['count'] ?? 0;

// Ownership Stats
$totalOwnership = $db->fetchOne('SELECT COUNT(*) as count FROM ownership_records');
$totalOwnership = $totalOwnership['count'] ?? 0;

$activeOwnership = $db->fetchOne('SELECT COUNT(*) as count FROM ownership_records WHERE ownership_status = "active"');
$activeOwnership = $activeOwnership['count'] ?? 0;

// Monthly data for charts (last 6 months)
$monthlyData = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $monthLabel = date('M', strtotime("-$i months"));
    
    $puppiesCount = $db->fetchOne("SELECT COUNT(*) as count FROM puppies WHERE DATE_FORMAT(created_at, '%Y-%m') = ?", [$month]);
    $inquiriesCount = $db->fetchOne("SELECT COUNT(*) as count FROM inquiries WHERE DATE_FORMAT(created_at, '%Y-%m') = ?", [$month]);
    $applicationsCount = $db->fetchOne("SELECT COUNT(*) as count FROM puppy_applications WHERE DATE_FORMAT(created_at, '%Y-%m') = ?", [$month]);
    $clientsCount = $db->fetchOne("SELECT COUNT(*) as count FROM clients WHERE DATE_FORMAT(created_at, '%Y-%m') = ?", [$month]);
    
    $monthlyData[] = [
        'month' => $monthLabel,
        'puppies' => (int)($puppiesCount['count'] ?? 0),
        'inquiries' => (int)($inquiriesCount['count'] ?? 0),
        'applications' => (int)($applicationsCount['count'] ?? 0),
        'clients' => (int)($clientsCount['count'] ?? 0)
    ];
}

// Recent data
$recentInquiries = $db->fetchAll('SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 5');
$recentPuppies = $db->fetchAll('SELECT * FROM puppies WHERE is_archived = 0 ORDER BY created_at DESC LIMIT 5');
$recentApplications = $db->fetchAll('SELECT * FROM puppy_applications ORDER BY created_at DESC LIMIT 5');
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
        <link rel="icon" type="image/png" href="dr-logo.png" />
    <style>
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--line);
            padding: 18px 22px;
            min-height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .stat-card .label {
            position: absolute;
            top: 14px;
            left: 20px;
            font-size: 0.65rem;
            color: var(--ink-faint);
            font-family: 'Inter', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 600;
            margin: 0;
        }

        .stat-card .value {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 600;
            color: var(--ink);
            line-height: 1.2;
            margin: 0;
            padding-top: 8px;
        }

        .stat-card .trend {
            font-size: 0.65rem;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            margin-top: 4px;
        }

        .stat-card .trend.up { color: #2e7d32; }
        .stat-card .trend.down { color: #c62828; }
        .stat-card .trend.neutral { color: var(--ink-faint); }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 8px;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--line);
            padding: 20px 24px;
            box-shadow: var(--shadow);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--line);
        }

        .card-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--ink);
            margin: 0;
        }

        .card-header .link {
            font-family: 'Inter', sans-serif;
            font-size: 0.75rem;
            color: var(--maroon);
            text-decoration: none;
            font-weight: 600;
        }

        .card-header .link:hover {
            text-decoration: underline;
        }

        .activity-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(139,26,26,0.06);
        }

        .activity-row:last-child {
            border-bottom: none;
        }

        .activity-row .icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--cream-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activity-row .icon svg {
            width: 18px;
            height: 18px;
            stroke: var(--maroon);
            fill: none;
            stroke-width: 1.8;
        }

        .activity-row p {
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            color: var(--ink);
            margin: 0;
        }

        .activity-row p strong {
            color: var(--maroon);
        }

        .activity-row small {
            font-family: 'Inter', sans-serif;
            font-size: 0.7rem;
            color: var(--ink-faint);
        }

        .eyebrow {
            font-family: 'Inter', sans-serif;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--ink-faint);
            font-weight: 600;
        }

        .note-card .btn {
            display: block;
            text-align: center;
            padding: 12px;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .note-card .btn-solid {
            background: var(--maroon);
            color: #fff;
            border-color: var(--maroon);
        }

        .note-card .btn-solid:hover {
            opacity: 0.92;
        }

        .note-card .btn-outline {
            background: #fff;
            border-color: var(--line);
            color: var(--ink);
        }

        .note-card .btn-outline:hover {
            background: var(--cream-soft);
        }

        .chart-container {
            position: relative;
            height: 200px;
            margin-top: 10px;
        }

        .chart-bars {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            height: 180px;
            gap: 12px;
            padding: 0 4px;
        }

        .chart-bar-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            height: 100%;
            justify-content: flex-end;
        }

        .chart-bar-wrapper {
            display: flex;
            align-items: flex-end;
            gap: 4px;
            height: 150px;
            width: 100%;
            justify-content: center;
        }

        .chart-bar {
            width: 100%;
            max-width: 20px;
            border-radius: 4px 4px 0 0;
            min-height: 2px;
            transition: height 0.6s ease;
            position: relative;
        }

        .chart-bar.puppies { background: var(--maroon); }
        .chart-bar.inquiries { background: #CC9A3D; }
        .chart-bar.applications { background: #5b7c4f; }
        .chart-bar.clients { background: #0d47a1; }

        .chart-label {
            font-size: 0.6rem;
            font-family: 'Inter', sans-serif;
            color: var(--ink-faint);
            margin-top: 6px;
            font-weight: 600;
        }

        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 12px;
            justify-content: center;
        }

        .chart-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.7rem;
            font-family: 'Inter', sans-serif;
            color: var(--ink-soft);
        }

        .chart-legend-item .dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
        }

        .chart-legend-item .dot.puppies { background: var(--maroon); }
        .chart-legend-item .dot.inquiries { background: #CC9A3D; }
        .chart-legend-item .dot.applications { background: #5b7c4f; }
        .chart-legend-item .dot.clients { background: #0d47a1; }

        .mini-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 8px;
        }

        .mini-stat {
            background: var(--cream-soft);
            border-radius: 10px;
            padding: 12px 16px;
            text-align: center;
        }

        .mini-stat .number {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--ink);
        }

        .mini-stat .label {
            font-size: 0.6rem;
            text-transform: uppercase;
            color: var(--ink-faint);
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            letter-spacing: 0.05em;
        }

        @media (max-width: 768px) {
            .stat-grid {
                grid-template-columns: 1fr 1fr;
            }
            .content-grid {
                grid-template-columns: 1fr;
            }
            .chart-bars {
                gap: 8px;
            }
            .chart-bar {
                max-width: 16px;
            }
        }

        @media (max-width: 480px) {
            .stat-grid {
                grid-template-columns: 1fr;
            }
            .mini-stats {
                grid-template-columns: 1fr 1fr;
            }
            .chart-bar {
                max-width: 12px;
            }
            .chart-legend {
                gap: 10px;
            }
        }
    </style>
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

        <!-- Main Stats -->
        <div class="stat-grid">
            <div class="stat-card">
                <span class="label">Total Puppies</span>
                <span class="value"><?= $totalPuppies ?></span>
            </div>
            <div class="stat-card">
                <span class="label">Available</span>
                <span class="value"><?= $availablePuppies ?></span>
            </div>
            <div class="stat-card">
                <span class="label">Reserved</span>
                <span class="value"><?= $reservedPuppies ?></span>
            </div>
            <div class="stat-card">
                <span class="label">Sold</span>
                <span class="value"><?= $soldPuppies ?></span>
            </div>
            <div class="stat-card">
                <span class="label">Inquiries</span>
                <span class="value"><?= $totalInquiries ?></span>
                <span class="trend <?= $newInquiries > 0 ? 'up' : 'neutral' ?>"><?= $newInquiries ?> new</span>
            </div>
            <div class="stat-card">
                <span class="label">Applications</span>
                <span class="value"><?= $totalApplications ?></span>
                <span class="trend <?= $newApplications > 0 ? 'up' : 'neutral' ?>"><?= $newApplications ?> new</span>
            </div>
        </div>

        <div class="content-grid">      
            <!-- Left Column -->
            <div>
                <!-- Monthly Trends Chart -->
                <div class="card" style="margin-bottom:24px;">
                    <div class="card-header">
                        <h3>Monthly Trends</h3>
                        <span class="link" style="font-size:0.7rem;color:var(--ink-faint);">Last 6 months</span>
                    </div>
                    <div class="chart-container">
                        <div class="chart-bars">
                            <?php foreach ($monthlyData as $data): 
                                $maxValue = 1;
                                foreach ($monthlyData as $d) {
                                    $maxValue = max($maxValue, $d['puppies'], $d['inquiries'], $d['applications'], $d['clients']);
                                }
                                if ($maxValue < 1) $maxValue = 1;
                            ?>
                            <div class="chart-bar-group">
                                <div class="chart-bar-wrapper">
                                    <div class="chart-bar puppies" style="height: <?= max(2, ($data['puppies'] / $maxValue) * 140) ?>px;" title="Puppies: <?= $data['puppies'] ?>"></div>
                                    <div class="chart-bar inquiries" style="height: <?= max(2, ($data['inquiries'] / $maxValue) * 140) ?>px;" title="Inquiries: <?= $data['inquiries'] ?>"></div>
                                    <div class="chart-bar applications" style="height: <?= max(2, ($data['applications'] / $maxValue) * 140) ?>px;" title="Applications: <?= $data['applications'] ?>"></div>
                                    <div class="chart-bar clients" style="height: <?= max(2, ($data['clients'] / $maxValue) * 140) ?>px;" title="Clients: <?= $data['clients'] ?>"></div>
                                </div>
                                <span class="chart-label"><?= $data['month'] ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="chart-legend">
                            <span class="chart-legend-item"><span class="dot puppies"></span> Puppies</span>
                            <span class="chart-legend-item"><span class="dot inquiries"></span> Inquiries</span>
                            <span class="chart-legend-item"><span class="dot applications"></span> Applications</span>
                            <span class="chart-legend-item"><span class="dot clients"></span> Clients</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Recent Inquiries</h3>
                        <a href="modules/im/inquiry.php" class="link">View all</a>
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
            </div>

            <!-- Right Column -->
            <div>
                <!-- Quick Stats -->
                <div class="card" style="margin-bottom:24px;">
                    <div class="card-header">
                        <h3>Quick Stats</h3>
                        <span class="link" style="font-size:0.7rem;color:var(--ink-faint);">Overview</span>
                    </div>
                    <div class="mini-stats">
                        <div class="mini-stat">
                            <div class="number"><?= $totalClients ?></div>
                            <div class="label">Total Clients</div>
                        </div>
                        <div class="mini-stat">
                            <div class="number"><?= $activeClients ?></div>
                            <div class="label">Active Clients</div>
                        </div>
                        <div class="mini-stat">
                            <div class="number"><?= $adoptedClients ?></div>
                            <div class="label">Adopted</div>
                        </div>
                        <div class="mini-stat">
                            <div class="number"><?= $totalGallery ?></div>
                            <div class="label">Gallery Items</div>
                        </div>
                        <div class="mini-stat">
                            <div class="number"><?= $publishedGallery ?></div>
                            <div class="label">Published</div>
                        </div>
                        <div class="mini-stat">
                            <div class="number"><?= $totalOwnership ?></div>
                            <div class="label">Ownership Records</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card note-card" style="margin-bottom:24px;">
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

    // Animate chart bars on load
    document.addEventListener('DOMContentLoaded', function() {
        const bars = document.querySelectorAll('.chart-bar');
        bars.forEach((bar, index) => {
            const targetHeight = bar.style.height;
            bar.style.height = '2px';
            setTimeout(() => {
                bar.style.height = targetHeight;
            }, 100 + (index * 30));
        });
    });
</script>

</body>
</html>