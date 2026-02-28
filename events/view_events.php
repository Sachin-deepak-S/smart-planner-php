<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM events WHERE user_id='$user_id' ORDER BY event_date ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Events — Planner</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">

<style>
/* ─── RESET & BASE ─────────────────────────────── */
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

.sidebar-divider {
  height: 1px;
  background: var(--border);
  margin: 8px 10px;
}

.sidebar-bottom {
  margin-top: auto;
  padding-top: 12px;
  border-top: 1px solid var(--border);
}

/* ─── MAIN ─────────────────────────────────────── */
.main {
  flex: 1;
  padding: 32px 40px;
  min-height: 100vh;
  position: relative;
  z-index: 1;
}

/* ─── TOPBAR ───────────────────────────────────── */
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
  animation: fadeUp .3s ease both;
}

.topbar-left h1 {
  font-family: var(--font-head);
  font-size: 26px;
  font-weight: 800;
  letter-spacing: -0.5px;
}

.topbar-left p {
  color: var(--muted);
  font-size: 13.5px;
  margin-top: 3px;
}

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

/* ─── CONTROLS BAR ─────────────────────────────── */
.controls-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 24px;
  flex-wrap: wrap;
  animation: fadeUp .35s ease .05s both;
}

.search-wrap {
  position: relative;
  flex: 1;
  max-width: 300px;
}

.search-wrap .search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 14px;
  pointer-events: none;
  opacity: 0.5;
}

.search-wrap input {
  width: 100%;
  padding: 10px 14px 10px 38px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 100px;
  color: var(--text);
  font-family: var(--font-body);
  font-size: 13.5px;
  transition: border-color .2s, box-shadow .2s;
}

.search-wrap input::placeholder { color: var(--muted); }

.search-wrap input:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(124,109,250,0.15);
}

.filter-group {
  display: flex;
  gap: 6px;
}

.filter-btn {
  padding: 8px 16px;
  border-radius: 100px;
  border: 1px solid var(--border);
  background: var(--surface);
  color: var(--muted);
  font-family: var(--font-body);
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all .2s ease;
}

.filter-btn:hover {
  color: var(--text);
  border-color: rgba(255,255,255,0.1);
  background: var(--surface2);
}

.filter-btn.active {
  background: var(--accent);
  border-color: var(--accent);
  color: white;
  box-shadow: 0 4px 12px rgba(124,109,250,0.3);
}

.add-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 9px 18px;
  background: var(--accent);
  color: white;
  border-radius: 100px;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  font-family: var(--font-head);
  transition: all .2s ease;
  white-space: nowrap;
  box-shadow: 0 4px 12px rgba(124,109,250,0.3);
}

.add-btn:hover {
  background: #6d5fe8;
  transform: translateY(-1px);
  box-shadow: 0 8px 20px rgba(124,109,250,0.4);
}

/* ─── EVENTS GRID ──────────────────────────────── */
.events-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
  gap: 16px;
  animation: fadeUp .4s ease .1s both;
}

/* ─── EVENT CARD ───────────────────────────────── */
.event-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 20px;
  position: relative;
  overflow: hidden;
  transition: transform .25s ease, box-shadow .25s ease, border-color .25s;
}

.event-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
  border-radius: 2px 2px 0 0;
  background: var(--border);
  transition: background .3s;
}

.event-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 40px rgba(0,0,0,0.35);
  border-color: rgba(255,255,255,0.1);
}

.event-card.is-today::before   { background: linear-gradient(90deg, #e91e63, #f43f5e); }
.event-card.is-upcoming::before { background: linear-gradient(90deg, var(--accent3), #67e8f9); }
.event-card.is-yearly::before  { background: linear-gradient(90deg, var(--accent2), #fbbf24); }
.event-card.is-past::before    { background: var(--border); }

/* Badges */
.badges {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  margin-bottom: 14px;
}

.badge {
  padding: 3px 10px;
  border-radius: 100px;
  font-size: 11px;
  font-weight: 600;
  font-family: var(--font-head);
  letter-spacing: 0.3px;
}

.badge.today    { background: rgba(233,30,99,0.15);  color: #f472b6; border: 1px solid rgba(233,30,99,0.2); }
.badge.upcoming { background: rgba(34,211,238,0.12); color: var(--accent3); border: 1px solid rgba(34,211,238,0.2); }
.badge.yearly   { background: rgba(249,115,22,0.12); color: var(--accent2); border: 1px solid rgba(249,115,22,0.2); }
.badge.past     { background: rgba(107,114,128,0.12); color: var(--muted); border: 1px solid rgba(107,114,128,0.15); }

/* Card content */
.event-title {
  font-family: var(--font-head);
  font-size: 16px;
  font-weight: 700;
  letter-spacing: -0.2px;
  margin-bottom: 8px;
  line-height: 1.3;
}

.event-datetime {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: var(--accent3);
  margin-bottom: 10px;
  font-weight: 500;
}

.event-desc {
  font-size: 13.5px;
  color: #8b9bb4;
  line-height: 1.55;
  margin-bottom: 14px;
}

.event-meta-row {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.meta-pill {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 11.5px;
  color: var(--muted);
  background: var(--surface2);
  padding: 3px 10px;
  border-radius: 100px;
  border: 1px solid var(--border);
}

/* ─── EMPTY STATE ──────────────────────────────── */
.empty-state {
  grid-column: 1 / -1;
  text-align: center;
  padding: 60px 20px;
  color: var(--muted);
}

.empty-state .empty-icon {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.5;
}

.empty-state h3 {
  font-family: var(--font-head);
  font-size: 18px;
  font-weight: 700;
  color: #4b5563;
  margin-bottom: 6px;
}

.empty-state p {
  font-size: 13.5px;
}

/* ─── ANIMATIONS ───────────────────────────────── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(18px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ─── SCROLLBAR ────────────────────────────────── */
::-webkit-scrollbar { width: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 4px; }
</style>
</head>

<body>

<!-- ═══════════════ SIDEBAR ═══════════════ -->
<aside class="sidebar">

  <div class="sidebar-logo">
    <div class="logo-icon">🚀</div>
    <span class="logo-text">Planner</span>
  </div>

  <a href="../index.php">
    <span class="nav-icon">🏠</span> Dashboard
  </a>

  <div class="nav-label">Notes</div>
  <a href="../notes/add_note.php">
    <span class="nav-icon">✏️</span> Add Note
  </a>
  <a href="../notes/view_notes.php">
    <span class="nav-icon">📝</span> View Notes
  </a>

  <div class="nav-label">Events</div>
  <a href="add_event.php">
    <span class="nav-icon">➕</span> Add Event
  </a>
  <a href="view_events.php" class="active">
    <span class="nav-icon">📅</span> View Events
  </a>

  <div class="sidebar-divider"></div>

  <a href="../calendar.php">
    <span class="nav-icon">📆</span> Calendar
  </a>
  <a href="../profile.php">
    <span class="nav-icon">👤</span> Profile
  </a>

  <div class="sidebar-bottom">
    <a href="../auth/logout.php">
      <span class="nav-icon">🚪</span> Logout
    </a>
  </div>

</aside>

<!-- ═══════════════ MAIN ═══════════════════ -->
<main class="main">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-left">
      <h1>Your Events</h1>
      <p>Browse, search and filter all your scheduled events</p>
    </div>
    <div class="profile-avatar">
      <?php echo strtoupper(substr($_SESSION['user_name'],0,1)); ?>
    </div>
  </div>

  <!-- CONTROLS -->
  <div class="controls-bar">
    <div class="search-wrap">
      <span class="search-icon">🔍</span>
      <input type="text" id="searchInput" placeholder="Search events…">
    </div>

    <div class="filter-group">
      <button class="filter-btn active" data-filter="all">All</button>
      <button class="filter-btn" data-filter="today">Today</button>
      <button class="filter-btn" data-filter="upcoming">Upcoming</button>
      <button class="filter-btn" data-filter="yearly">Yearly</button>
    </div>

    <a href="add_event.php" class="add-btn">＋ Add Event</a>
  </div>

  <!-- EVENTS GRID -->
  <div class="events-grid" id="eventsGrid">

  <?php
  $today = date("Y-m-d");
  $hasEvents = false;

  mysqli_data_seek($result, 0);
  while($row = mysqli_fetch_assoc($result)):
    $hasEvents = true;

    $isToday    = $row['event_date'] == $today;
    $isUpcoming = $row['event_date'] >  $today;
    $isYearly   = $row['repeat_type'] == 'yearly';
    $isPast     = $row['event_date'] <  $today;

    $cardClass = 'event-card';
    if ($isToday)    $cardClass .= ' is-today';
    elseif ($isUpcoming) $cardClass .= ' is-upcoming';
    if ($isYearly)   $cardClass .= ' is-yearly';
    if ($isPast)     $cardClass .= ' is-past';
  ?>

  <div class="<?php echo $cardClass; ?>"
       data-title="<?php echo strtolower(htmlspecialchars($row['title'])); ?>"
       data-description="<?php echo strtolower(htmlspecialchars($row['description'])); ?>"
       data-date="<?php echo $row['event_date']; ?>"
       data-type="<?php echo $row['repeat_type']; ?>">

    <!-- Badges -->
    <div class="badges">
      <?php if ($isToday): ?>
        <span class="badge today">Today</span>
      <?php elseif ($isUpcoming): ?>
        <span class="badge upcoming">Upcoming</span>
      <?php elseif ($isPast): ?>
        <span class="badge past">Past</span>
      <?php endif; ?>
      <?php if ($isYearly): ?>
        <span class="badge yearly">Yearly</span>
      <?php endif; ?>
    </div>

    <div class="event-title"><?php echo htmlspecialchars($row['title']); ?></div>

    <div class="event-datetime">
      📅 <?php echo date("d M Y", strtotime($row['event_date'])); ?>
      <?php if ($row['event_time']): ?>
        &nbsp;·&nbsp; ⏰ <?php echo $row['event_time']; ?>
      <?php endif; ?>
    </div>

    <?php if ($row['description']): ?>
      <div class="event-desc"><?php echo htmlspecialchars($row['description']); ?></div>
    <?php endif; ?>

    <div class="event-meta-row">
      <span class="meta-pill">🔁 <?php echo ucfirst($row['repeat_type']); ?></span>
      <span class="meta-pill">🔔 Notify: <?php echo $row['notify_before'] ? 'Yes' : 'No'; ?></span>
    </div>

  </div>

  <?php endwhile; ?>

  <?php if (!$hasEvents): ?>
    <div class="empty-state">
      <div class="empty-icon">📭</div>
      <h3>No events yet</h3>
      <p>Create your first event to get started</p>
    </div>
  <?php endif; ?>

  </div><!-- /events-grid -->

</main>

<script>
const searchInput   = document.getElementById("searchInput");
const filterButtons = document.querySelectorAll(".filter-btn");
const cards         = document.querySelectorAll(".event-card");

let activeFilter = "all";

searchInput.addEventListener("keyup", filterEvents);

filterButtons.forEach(btn => {
  btn.addEventListener("click", function() {
    filterButtons.forEach(b => b.classList.remove("active"));
    this.classList.add("active");
    activeFilter = this.dataset.filter;
    filterEvents();
  });
});

function filterEvents() {
  const searchText = searchInput.value.toLowerCase();
  const today = new Date().toISOString().split("T")[0];

  cards.forEach(card => {
    const title       = card.dataset.title;
    const description = card.dataset.description;
    const date        = card.dataset.date;
    const type        = card.dataset.type;

    const matchesSearch = title.includes(searchText) || description.includes(searchText);

    let matchesFilter = true;
    if      (activeFilter === "today")    matchesFilter = date === today;
    else if (activeFilter === "upcoming") matchesFilter = date > today;
    else if (activeFilter === "yearly")   matchesFilter = type === "yearly";

    card.style.display = (matchesSearch && matchesFilter) ? "block" : "none";
  });
}
</script>

</body>
</html>