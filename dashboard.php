<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$today = date("Y-m-d");
$tomorrow = date("Y-m-d", strtotime("+1 day"));

$totalNotes = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) as total FROM notes WHERE user_id='$user_id'")
)['total'];

$importantNotes = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) as total FROM notes WHERE user_id='$user_id' AND is_important=1")
)['total'];

$todayEvents = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) as total FROM events WHERE user_id='$user_id' AND event_date='$today'")
)['total'];

$upcomingEvents = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) as total FROM events WHERE user_id='$user_id' AND event_date > '$today'")
)['total'];

$notifications = [];

$res = mysqli_query($conn,"SELECT title FROM events WHERE user_id='$user_id' AND event_date='$today'");
while($row = mysqli_fetch_assoc($res)){
    $notifications[] = "📅 Today: ".$row['title'];
}

$res = mysqli_query($conn,"SELECT title FROM events WHERE user_id='$user_id' AND event_date='$tomorrow' AND notify_before=1");
while($row = mysqli_fetch_assoc($res)){
    $notifications[] = "⏰ Tomorrow: ".$row['title'];
}

$res = mysqli_query($conn,"SELECT title FROM notes WHERE user_id='$user_id' AND is_important=1");
while($row = mysqli_fetch_assoc($res)){
    $notifications[] = "🔥 Important: ".$row['title'];
}

$notificationCount = count($notifications);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Planner — Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">

<style>
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

:root {
  --bg:       #0d0f14;
  --surface:  #13161e;
  --surface2: #1a1e28;
  --border:   rgba(255,255,255,0.06);
  --accent:   #7c6dfa;
  --accent2:  #f97316;
  --accent3:  #22d3ee;
  --accent4:  #a3e635;
  --text:     #e8eaf0;
  --muted:    #6b7280;
  --font-head: 'Syne', sans-serif;
  --font-body: 'DM Sans', sans-serif;
  --sidebar-w: 260px;
  --radius:   16px;
  --shadow:   0 8px 32px rgba(0,0,0,0.4);
}

html { scroll-behavior: smooth; }

body {
  display: flex;
  background: var(--bg);
  color: var(--text);
  font-family: var(--font-body);
  min-height: 100vh;
  overflow-x: hidden;
}

body::before {
  content: '';
  position: fixed;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
  pointer-events: none;
  z-index: 0;
  opacity: .4;
}

/* ─── SIDEBAR ──────────────────────────────────── */
.sidebar {
  width: var(--sidebar-w);
  min-height: 100vh;
  background: var(--surface);
  border-right: 1px solid var(--border);
  padding: 28px 20px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  position: sticky;
  top: 0;
  height: 100vh;
  overflow-y: auto;
  z-index: 10;
  flex-shrink: 0;
}

.sidebar-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 0 8px 24px;
  border-bottom: 1px solid var(--border);
  margin-bottom: 10px;
}

.logo-icon {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: linear-gradient(135deg, var(--accent), #a78bfa);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
  box-shadow: 0 0 20px rgba(124,109,250,0.4);
}

.logo-text {
  font-family: var(--font-head);
  font-size: 18px;
  font-weight: 800;
  letter-spacing: -0.3px;
  color: var(--text);
}

.nav-label {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--muted);
  padding: 14px 10px 6px;
}

.sidebar a {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  color: #8b9bb4;
  text-decoration: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 400;
  transition: all .2s ease;
}

.sidebar a .nav-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  background: transparent;
  transition: all .2s ease;
  flex-shrink: 0;
}

.sidebar a:hover { color: var(--text); background: var(--surface2); }
.sidebar a:hover .nav-icon { background: var(--surface); }
.sidebar a.active { color: var(--text); background: var(--surface2); }
.sidebar a.active .nav-icon { background: rgba(124,109,250,0.15); }

.sidebar-divider { height: 1px; background: var(--border); margin: 8px 10px; }
.sidebar-bottom { margin-top: auto; padding-top: 12px; border-top: 1px solid var(--border); }

/* ─── MAIN ─────────────────────────────────────── */
.main {
  flex: 1;
  padding: 32px 40px;
  min-height: 100vh;
  position: relative;
  z-index: 1;
  min-width: 0;
}

/* ─── TOPBAR ───────────────────────────────────── */
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40px;
  gap: 20px;
  animation: fadeUp .3s ease both;
  /* KEY FIX: topbar sits above stat cards so panel can overlay them */
  position: relative;
  z-index: 500;
}

.welcome-block h1 {
  font-family: var(--font-head);
  font-size: 26px;
  font-weight: 800;
  letter-spacing: -0.5px;
  line-height: 1.2;
}

.welcome-block p {
  color: var(--muted);
  font-size: 13.5px;
  margin-top: 3px;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 14px;
}

.quick-actions { display: flex; gap: 8px; }

.quick-actions a {
  display: flex;
  align-items: center;
  gap: 6px;
  background: var(--surface2);
  border: 1px solid var(--border);
  color: var(--text);
  padding: 8px 16px;
  border-radius: 100px;
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  transition: all .2s ease;
  white-space: nowrap;
}

.quick-actions a:hover {
  background: var(--accent);
  border-color: var(--accent);
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(124,109,250,0.35);
}

/* ─── NOTIFICATION BELL ─────────────────────────── */
.notification-wrap {
  position: relative;
}

.notif-btn {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: var(--surface2);
  border: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 17px;
  transition: all .2s;
  position: relative;
  user-select: none;
}

.notif-btn:hover {
  background: var(--surface);
  border-color: rgba(255,255,255,0.12);
}

.badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #ef4444;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  min-width: 18px;
  height: 18px;
  padding: 0 4px;
  border-radius: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid var(--bg);
  font-family: var(--font-head);
  pointer-events: none;
}

/* ─── NOTIFICATION PANEL ─────────────────────────── */
.notification-panel {
  position: absolute;
  top: calc(100% + 12px);
  right: 0;
  width: 320px;
  background: var(--surface);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: var(--radius);
  /* Highest z-index — floats above ALL page content */
  z-index: 9999;
  box-shadow: 0 24px 64px rgba(0,0,0,0.7), 0 0 0 1px rgba(124,109,250,0.1);
  overflow: hidden;
  /* Smooth open/close animation */
  opacity: 0;
  visibility: hidden;
  transform: translateY(-8px) scale(0.97);
  transform-origin: top right;
  transition: opacity .2s ease, transform .2s ease, visibility 0s linear .2s;
}

.notification-panel.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0) scale(1);
  transition: opacity .2s ease, transform .2s ease, visibility 0s linear 0s;
}

.notif-header {
  padding: 14px 16px 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--border);
  background: var(--surface2);
}

.notif-header-title {
  font-family: var(--font-head);
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 1.8px;
  color: var(--muted);
  text-transform: uppercase;
}

.notif-count-pill {
  background: var(--accent);
  color: white;
  font-size: 10px;
  font-weight: 700;
  font-family: var(--font-head);
  padding: 2px 9px;
  border-radius: 100px;
  line-height: 1.6;
}

.notification-list {
  max-height: 300px;
  overflow-y: auto;
}

.notification-item {
  padding: 13px 16px;
  font-size: 13.5px;
  border-bottom: 1px solid var(--border);
  color: #b0baca;
  transition: background .15s;
  line-height: 1.45;
}

.notification-item:last-child { border-bottom: none; }
.notification-item:hover { background: var(--surface2); color: var(--text); }

.notification-empty {
  padding: 28px 16px;
  font-size: 13.5px;
  color: var(--muted);
  text-align: center;
}

/* Profile Avatar */
.profile-avatar {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--accent), #a78bfa);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-head);
  font-weight: 700;
  font-size: 16px;
  box-shadow: 0 4px 12px rgba(124,109,250,0.35);
  flex-shrink: 0;
}

/* ─── SECTION HEADER ───────────────────────────── */
.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}

.section-title {
  font-family: var(--font-head);
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--muted);
}

/* ─── STAT CARDS ───────────────────────────────── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 36px;
  position: relative;
  z-index: 1;
}

@media (max-width: 900px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
}

.stat-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 22px 22px 20px;
  position: relative;
  overflow: hidden;
  transition: transform .25s ease, box-shadow .25s ease;
  cursor: default;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
  border-radius: 2px 2px 0 0;
  opacity: 0;
  transition: opacity .3s;
}

.stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.35); }
.stat-card:hover::before { opacity: 1; }

.stat-card.c1::before { background: linear-gradient(90deg, var(--accent), #a78bfa); }
.stat-card.c2::before { background: linear-gradient(90deg, #ef4444, #f97316); }
.stat-card.c3::before { background: linear-gradient(90deg, var(--accent3), #67e8f9); }
.stat-card.c4::before { background: linear-gradient(90deg, var(--accent4), #84cc16); }

.stat-card-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  margin-bottom: 16px;
}

.c1 .stat-card-icon { background: rgba(124,109,250,0.12); }
.c2 .stat-card-icon { background: rgba(249,115,22,0.12); }
.c3 .stat-card-icon { background: rgba(34,211,238,0.12); }
.c4 .stat-card-icon { background: rgba(163,230,53,0.12); }

.stat-number {
  font-family: var(--font-head);
  font-size: 36px;
  font-weight: 800;
  line-height: 1;
  letter-spacing: -1px;
  margin-bottom: 4px;
}

.c1 .stat-number { color: var(--accent); }
.c2 .stat-number { color: var(--accent2); }
.c3 .stat-number { color: var(--accent3); }
.c4 .stat-number { color: var(--accent4); }

.stat-label {
  font-size: 12.5px;
  color: var(--muted);
  font-weight: 400;
}

/* ─── BOTTOM PANELS ────────────────────────────── */
.panels-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  position: relative;
  z-index: 1;
}

.panel {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
}

.panel-head {
  padding: 16px 20px 14px;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.panel-head h3 {
  font-family: var(--font-head);
  font-size: 15px;
  font-weight: 700;
}

.panel-head a {
  font-size: 12px;
  color: var(--accent);
  text-decoration: none;
  font-weight: 500;
  opacity: 0.8;
  transition: opacity .2s;
}

.panel-head a:hover { opacity: 1; }

.panel-body { padding: 16px 20px 20px; }

.action-row { display: flex; flex-direction: column; gap: 10px; }

.action-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 14px;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 12px;
  text-decoration: none;
  color: var(--text);
  font-size: 14px;
  transition: all .2s ease;
}

.action-link:hover {
  border-color: rgba(255,255,255,0.12);
  background: #1f2535;
  transform: translateX(4px);
}

.action-link-icon {
  width: 34px;
  height: 34px;
  border-radius: 9px;
  background: var(--surface);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  flex-shrink: 0;
}

.action-link span { flex: 1; }
.action-link-arrow { color: var(--muted); font-size: 16px; transition: transform .2s; }
.action-link:hover .action-link-arrow { transform: translateX(3px); }

.summary-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid var(--border);
  font-size: 13.5px;
}

.summary-item:last-child { border-bottom: none; }
.summary-item-label { color: #8b9bb4; }
.summary-item-val { font-family: var(--font-head); font-weight: 700; font-size: 16px; }

/* ─── ANIMATIONS ───────────────────────────────── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(18px); }
  to   { opacity: 1; transform: translateY(0); }
}

.stat-card { animation: fadeUp .4s ease both; }
.stat-card:nth-child(1) { animation-delay: .05s; }
.stat-card:nth-child(2) { animation-delay: .10s; }
.stat-card:nth-child(3) { animation-delay: .15s; }
.stat-card:nth-child(4) { animation-delay: .20s; }
.panels-row { animation: fadeUp .4s ease .25s both; }

::-webkit-scrollbar { width: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 4px; }
</style>
</head>

<body>

<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">🚀</div>
    <span class="logo-text">Planner</span>
  </div>

  <a href="index.php" class="active">
    <span class="nav-icon">🏠</span> Dashboard
  </a>

  <div class="nav-label">Notes</div>
  <a href="notes/add_note.php">
    <span class="nav-icon">✏️</span> Add Note
  </a>
  <a href="notes/view_notes.php">
    <span class="nav-icon">📝</span> View Notes
  </a>

  <div class="nav-label">Events</div>
  <a href="events/add_event.php">
    <span class="nav-icon">➕</span> Add Event
  </a>
  <a href="events/view_events.php">
    <span class="nav-icon">📅</span> View Events
  </a>

  <div class="sidebar-divider"></div>

  <a href="calendar.php">
    <span class="nav-icon">📆</span> Calendar
  </a>
  <a href="profile.php">
    <span class="nav-icon">👤</span> Profile
  </a>

  <div class="sidebar-bottom">
    <a href="auth/logout.php">
      <span class="nav-icon">🚪</span> Logout
    </a>
  </div>
</aside>

<main class="main">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="welcome-block">
      <h1>Good <?php
        $h = (int)date('H');
        if($h < 12) echo 'morning';
        elseif($h < 18) echo 'afternoon';
        else echo 'evening';
      ?>, <?php echo htmlspecialchars($_SESSION['user_name']); ?> 👋</h1>
      <p><?php echo date('l, F j, Y'); ?></p>
    </div>

    <div class="topbar-right">

      <div class="quick-actions">
        <a href="notes/add_note.php">＋ Note</a>
        <a href="events/add_event.php">＋ Event</a>
      </div>

      <!-- BELL + PANEL -->
      <div class="notification-wrap" id="notifWrap">
        <div class="notif-btn" onclick="toggleNotifications(event)">
          🔔
          <?php if($notificationCount > 0): ?>
            <div class="badge"><?php echo $notificationCount; ?></div>
          <?php endif; ?>
        </div>

        <div class="notification-panel" id="notifPanel">
          <div class="notif-header">
            <span class="notif-header-title">Notifications</span>
            <?php if($notificationCount > 0): ?>
              <span class="notif-count-pill"><?php echo $notificationCount; ?> new</span>
            <?php endif; ?>
          </div>
          <div class="notification-list">
            <?php if($notificationCount == 0): ?>
              <div class="notification-empty">🎉 All clear! No notifications.</div>
            <?php else: ?>
              <?php foreach($notifications as $note): ?>
                <div class="notification-item"><?php echo htmlspecialchars($note); ?></div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <a href="profile.php" style="text-decoration:none;">
        <div class="profile-avatar">
          <?php echo strtoupper(substr($_SESSION['user_name'],0,1)); ?>
        </div>
      </a>

    </div>
  </div>

  <!-- STATS -->
  <div class="section-header">
    <span class="section-title">Overview</span>
  </div>

  <div class="stats-grid">
    <div class="stat-card c1">
      <div class="stat-card-icon">📝</div>
      <div class="stat-number"><?php echo $totalNotes; ?></div>
      <div class="stat-label">Total Notes</div>
    </div>
    <div class="stat-card c2">
      <div class="stat-card-icon">🔥</div>
      <div class="stat-number"><?php echo $importantNotes; ?></div>
      <div class="stat-label">Important Notes</div>
    </div>
    <div class="stat-card c3">
      <div class="stat-card-icon">📅</div>
      <div class="stat-number"><?php echo $todayEvents; ?></div>
      <div class="stat-label">Today's Events</div>
    </div>
    <div class="stat-card c4">
      <div class="stat-card-icon">🗓️</div>
      <div class="stat-number"><?php echo $upcomingEvents; ?></div>
      <div class="stat-label">Upcoming Events</div>
    </div>
  </div>

  <!-- PANELS -->
  <div class="panels-row">

    <div class="panel">
      <div class="panel-head"><h3>Quick Access</h3></div>
      <div class="panel-body">
        <div class="action-row">
          <a href="notes/add_note.php" class="action-link">
            <div class="action-link-icon">✏️</div>
            <span>Create a new note</span>
            <span class="action-link-arrow">›</span>
          </a>
          <a href="events/add_event.php" class="action-link">
            <div class="action-link-icon">📌</div>
            <span>Schedule an event</span>
            <span class="action-link-arrow">›</span>
          </a>
          <a href="calendar.php" class="action-link">
            <div class="action-link-icon">📆</div>
            <span>Open calendar</span>
            <span class="action-link-arrow">›</span>
          </a>
          <a href="notes/view_notes.php" class="action-link">
            <div class="action-link-icon">📋</div>
            <span>Browse all notes</span>
            <span class="action-link-arrow">›</span>
          </a>
        </div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head">
        <h3>Summary</h3>
        <a href="events/view_events.php">View all →</a>
      </div>
      <div class="panel-body">
        <div class="summary-item">
          <span class="summary-item-label">Notes created</span>
          <span class="summary-item-val" style="color:var(--accent)"><?php echo $totalNotes; ?></span>
        </div>
        <div class="summary-item">
          <span class="summary-item-label">Flagged as important</span>
          <span class="summary-item-val" style="color:var(--accent2)"><?php echo $importantNotes; ?></span>
        </div>
        <div class="summary-item">
          <span class="summary-item-label">Events happening today</span>
          <span class="summary-item-val" style="color:var(--accent3)"><?php echo $todayEvents; ?></span>
        </div>
        <div class="summary-item">
          <span class="summary-item-label">Events ahead</span>
          <span class="summary-item-val" style="color:var(--accent4)"><?php echo $upcomingEvents; ?></span>
        </div>
        <div class="summary-item">
          <span class="summary-item-label">Pending notifications</span>
          <span class="summary-item-val" style="color:#f472b6"><?php echo $notificationCount; ?></span>
        </div>
      </div>
    </div>

  </div>

</main>

<script>
function toggleNotifications(e) {
  e.stopPropagation();
  document.getElementById('notifPanel').classList.toggle('show');
}

document.addEventListener('click', function(e) {
  const wrap = document.getElementById('notifWrap');
  if (wrap && !wrap.contains(e.target)) {
    document.getElementById('notifPanel').classList.remove('show');
  }
});
</script>

</body>
</html>