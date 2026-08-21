<?php
session_start();

if (!isset($_SESSION['dr_admin_signed_in']) || $_SESSION['dr_admin_signed_in'] !== true) {
    header('Location: login.html');
    exit();
}

$userName = isset($_SESSION['dr_admin_name']) ? $_SESSION['dr_admin_name'] : 'there';
$userFirstName = explode(' ', $userName)[0]; // Get first name only for greeting
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
            <img src="dr-logo.png" class="dr-crest" alt="Dollhaus Royale crest" />
            DOLLHAUS <em>ROYALE</em>
        </div>
        <button class="menu-btn" id="menuBtn" aria-label="Open menu">
            <svg viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18" /></svg>
        </button>
    </div>

    <!-- ito yung side bar tuwing gagawa kayo ng module include nyo to para di mawala sidebar -->
     <!--    -yukki      -->
    <?php include 'sidebar.php'; ?>

    <main class="main">
        <div class="main-header">
            <div>
                <div class="eyebrow" id="welcomeLabel">Welcome back, <?php echo htmlspecialchars($userFirstName); ?></div>
                <h1>Your household, at a glance</h1>
            </div>
            <div class="header-actions">
                <a href="#" class="btn btn-outline">
                    <svg viewBox="0 0 24 24"><path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5 8.4 8.4 0 0 1-3.9-.9L3 21l1.9-5.6A8.4 8.4 0 0 1 4 11.5 8.5 8.5 0 0 1 12.5 3 8.5 8.5 0 0 1 21 11.5Z" /></svg>
                    Message the Breeder
                </a>
                <a href="#" class="btn btn-solid">
                    <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M16 3v4M8 3v4M3 10h18" /></svg>
                    Book a Visit
                </a>
            </div>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="label">Reserved Puppies</div>
                <div class="value">1 <small>of 1 litter</small></div>
            </div>
            <div class="stat-card">
                <div class="label">Days Until Ready</div>
                <div class="value">54</div>
            </div>
            <div class="stat-card">
                <div class="label">Health Records</div>
                <div class="value">6 <small>3 on file</small></div>
            </div>
            <div class="stat-card">
                <div class="label">Unread Messages</div>
                <div class="value">2</div>
            </div>
        </div>

        <div class="content-grid">      
            <div>
                <div class="card">
                    <div class="card-header">
                        <h3>Your Puppy's Journey</h3>
                        <a href="#" class="link">View full record</a>
                    </div>

                    <div class="journey-row">
                        <img src="https://images.unsplash.com/photo-1581582962839-0ff77bb4e4db?w=100&h=100&fit=crop" alt="Buttercup" />
                        <div class="info">
                            <h4>Buttercup</h4>
                            <p>Week 6 of 10 · Weaning &amp; socialisation</p>
                        </div>
                        <div class="progress-wrap">
                            <div class="progress-bar"><span style="width:60%"></span></div>
                            <small>Whelped → Ready Oct 12</small>
                        </div>
                        <span class="pill pill-track">On Track</span>
                    </div>

                    <div class="journey-row">
                        <img src="https://images.unsplash.com/photo-1519677194310-7b6f32e6446f?w=100&h=100&fit=crop" alt="Luna" />
                        <div class="info">
                            <h4>Luna — Dam</h4>
                            <p>Recovery check · next vet visit Aug 24</p>
                        </div>
                        <div class="progress-wrap">
                            <div class="progress-bar"><span style="width:80%"></span></div>
                            <small>Post-whelp recovery</small>
                        </div>
                        <span class="pill pill-healthy">Healthy</span>
                    </div>

                    <div class="journey-row">
                        <img src="https://images.unsplash.com/photo-1608787449797-1b42f22f0ee9?w=100&h=100&fit=crop" alt="Spring litter" />
                        <div class="info">
                            <h4>Spring Litter Deposit</h4>
                            <p>Waitlist position #2 · Cheesecake x Ottie</p>
                        </div>
                        <div class="progress-wrap">
                            <div class="progress-bar"><span style="width:15%"></span></div>
                            <small>Awaiting whelp</small>
                        </div>
                        <span class="pill pill-wait">Waitlisted</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Recent Activity</h3>
                        <a href="#" class="link">View all</a>
                    </div>

                    <div class="activity-row">
                        <span class="icon">
                            <svg viewBox="0 0 24 24"><path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5 8.4 8.4 0 0 1-3.9-.9L3 21l1.9-5.6A8.4 8.4 0 0 1 4 11.5 8.5 8.5 0 0 1 12.5 3 8.5 8.5 0 0 1 21 11.5Z" /></svg>
                        </span>
                        <div>
                            <p>The breeder shared 4 new photos of Buttercup.</p>
                            <small>Yesterday · 4:12 PM</small>
                        </div>
                    </div>
                    <div class="activity-row">
                        <span class="icon">
                            <svg viewBox="0 0 24 24"><path d="M12 21s-7-4.35-9.5-8.5C.5 8.5 3 5 6.5 5c2 0 3.3 1 4.5 2.5C12.2 6 13.5 5 15.5 5 19 5 21.5 8.5 19.5 12.5 17 16.65 12 21 12 21Z" /></svg>
                        </span>
                        <div>
                            <p>Second round of vaccinations completed and logged.</p>
                            <small>Aug 14 · Vet Visit</small>
                        </div>
                    </div>
                    <div class="activity-row">
                        <span class="icon">
                            <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M16 3v4M8 3v4M3 10h18" /></svg>
                        </span>
                        <div>
                            <p>Home visit scheduled for Aug 24, 2:00 PM.</p>
                            <small>Aug 11 · Appointment</small>
                        </div>
                    </div>
                    <div class="activity-row">
                        <span class="icon">
                            <svg viewBox="0 0 24 24"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" /><path d="M14 3v5h5" /></svg>
                        </span>
                        <div>
                            <p>Puppy contract and health guarantee uploaded.</p>
                            <small>Aug 2 · Document</small>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="card">
                    <div class="card-header">
                        <h3>Health Checklist</h3>
                        <span class="count">3 / 6</span>
                    </div>

                    <div class="check-row done">
                        <span class="check-dot"><svg viewBox="0 0 24 24"><path d="M5 13l4 4 10-10" /></svg></span>
                        <span class="label-text">Vet health exam</span>
                        <span class="date">Jul 20</span>
                    </div>
                    <div class="check-row done">
                        <span class="check-dot"><svg viewBox="0 0 24 24"><path d="M5 13l4 4 10-10" /></svg></span>
                        <span class="label-text">First vaccination</span>
                        <span class="date">Aug 3</span>
                    </div>
                    <div class="check-row done">
                        <span class="check-dot"><svg viewBox="0 0 24 24"><path d="M5 13l4 4 10-10" /></svg></span>
                        <span class="label-text">Second vaccination</span>
                        <span class="date">Aug 14</span>
                    </div>
                    <div class="check-row">
                        <span class="check-dot"></span>
                        <span class="label-text">Deworming, round 3</span>
                        <span class="date">Aug 28</span>
                    </div>
                    <div class="check-row">
                        <span class="check-dot"></span>
                        <span class="label-text">Microchip &amp; registration</span>
                        <span class="date">Sep 10</span>
                    </div>
                    <div class="check-row">
                        <span class="check-dot"></span>
                        <span class="label-text">Final wellness exam</span>
                        <span class="date">Oct 9</span>
                    </div>
                </div>

                <div class="card note-card">
                    <div class="eyebrow">A Note From the Breeder</div>
                    <p class="quote">"Buttercup found her voice this week — she's the first to greet us at the gate every morning. Sweet, steady, and full of mischief."</p>
                    <div class="who">
                        <span class="avatar">M</span>
                        Marisol, Dollhaus Royale
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
    document.getElementById('userName').textContent = name;
    document.getElementById('userAvatar').textContent = firstName.charAt(0).toUpperCase();

    document.getElementById('signOutBtn').addEventListener('click', function () {
        window.location.href = 'logout.php';
    });

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