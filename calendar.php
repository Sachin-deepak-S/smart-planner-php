<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM events WHERE user_id='$user_id'";
$result = mysqli_query($conn, $sql);

$events = [];

while($row = mysqli_fetch_assoc($result)) {
    $color = $row['repeat_type'] == 'yearly' ? "#f97316" : "#7c6dfa";

    $events[] = [
        "title"       => $row['title'],
        "start"       => $row['event_date'] . ($row['event_time'] ? "T".$row['event_time'] : ""),
        "description" => $row['description'],
        "color"       => $color,
        "repeat_type" => $row['repeat_type']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Calendar — Planner</title>

<!-- FullCalendar -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">

<style>
/* ─── RESET & BASE ─────────────────────────────── */
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

:root {
  --bg:        #0d0f14;
  --surface:   #13161e;
  --surface2:  #1a1e28;
  --border:    rgba(255,255,255,0.06);
  --accent:    #7c6dfa;
  --accent2:   #f97316;
  --accent3:   #22d3ee;
  --accent4:   #a3e635;
  --text:      #e8eaf0;
  --muted:     #6b7280;
  --font-head: 'Syne', sans-serif;
  --font-body: 'DM Sans', sans-serif;
  --sidebar-w: 260px;
  --radius:    16px;
  --shadow:    0 8px 32px rgba(0,0,0,0.4);
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
  display: flex;
  flex-direction: column;
}

/* ─── TOPBAR ───────────────────────────────────── */
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 28px;
  animation: fadeUp .3s ease both;
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.topbar-left h1 {
  font-family: var(--font-head);
  font-size: 26px;
  font-weight: 800;
  letter-spacing: -0.5px;
}

.event-count-pill {
  padding: 4px 12px;
  background: rgba(124,109,250,0.12);
  border: 1px solid rgba(124,109,250,0.25);
  border-radius: 100px;
  font-size: 12px;
  font-weight: 600;
  color: var(--accent);
  font-family: var(--font-head);
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 10px;
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

/* Legend */
.legend {
  display: flex;
  gap: 16px;
  align-items: center;
  flex-wrap: wrap;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12.5px;
  color: var(--muted);
}

.legend-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  flex-shrink: 0;
}

/* ─── CALENDAR CARD ────────────────────────────── */
.calendar-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 28px;
  flex: 1;
  box-shadow: var(--shadow);
  animation: fadeUp .4s ease .1s both;
}

/* ─── FULLCALENDAR DARK THEME ──────────────────── */
.fc {
  font-family: var(--font-body);
  --fc-border-color: rgba(255,255,255,0.06);
  --fc-button-text-color: #fff;
  --fc-button-bg-color: var(--accent);
  --fc-button-border-color: transparent;
  --fc-button-hover-bg-color: #6d5fe8;
  --fc-button-hover-border-color: transparent;
  --fc-button-active-bg-color: #5b4fd6;
  --fc-today-bg-color: rgba(124,109,250,0.07);
  --fc-neutral-bg-color: var(--surface2);
  --fc-list-event-hover-bg-color: var(--surface2);
  --fc-page-bg-color: transparent;
  color: var(--text);
}

.fc .fc-toolbar-title {
  font-family: var(--font-head);
  font-weight: 800;
  font-size: 22px;
  letter-spacing: -0.4px;
  color: var(--text);
}

.fc .fc-button {
  border-radius: 100px !important;
  padding: 7px 16px !important;
  font-family: var(--font-body) !important;
  font-weight: 500 !important;
  font-size: 13px !important;
  text-transform: capitalize;
  box-shadow: none !important;
  transition: all .2s !important;
  border: none !important;
}

.fc .fc-button-primary:not(:disabled):hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(124,109,250,0.3) !important;
}

.fc .fc-button-primary:disabled {
  opacity: 0.4;
  background: var(--surface2) !important;
}

.fc .fc-button-active {
  background: #5b4fd6 !important;
}

/* Day headers */
.fc .fc-col-header-cell-cushion {
  color: var(--muted);
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 1px;
  text-transform: uppercase;
  text-decoration: none;
  padding: 10px 4px;
}

/* Day numbers */
.fc .fc-daygrid-day-number {
  color: #8b9bb4;
  font-size: 13px;
  text-decoration: none;
  font-weight: 500;
  padding: 6px 8px;
}

.fc .fc-day-today .fc-daygrid-day-number {
  color: var(--accent);
  font-weight: 700;
}

/* Today highlight */
.fc .fc-daygrid-day.fc-day-today {
  background: rgba(124,109,250,0.06) !important;
}

/* Grid lines */
.fc .fc-scrollgrid {
  border-color: var(--border) !important;
}

.fc td, .fc th {
  border-color: var(--border) !important;
}

/* Events */
.fc-event {
  border-radius: 100px !important;
  padding: 3px 10px !important;
  font-weight: 500 !important;
  font-size: 12px !important;
  border: none !important;
  box-shadow: none !important;
  transition: transform .15s, opacity .15s !important;
  cursor: pointer;
  margin: 2px 3px !important;
}

.fc-event:hover {
  transform: translateY(-1px) scale(1.02) !important;
  opacity: 0.9;
}

.fc-event-title {
  font-family: var(--font-body);
  font-weight: 500;
}

/* More link */
.fc .fc-daygrid-more-link {
  color: var(--accent);
  font-size: 11.5px;
  font-weight: 600;
}

/* Time grid */
.fc .fc-timegrid-slot-label {
  color: var(--muted);
  font-size: 11px;
}

/* Week/day view bg */
.fc .fc-timegrid-col.fc-day-today {
  background: rgba(124,109,250,0.04) !important;
}

/* ─── MODAL ────────────────────────────────────── */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.6);
  backdrop-filter: blur(6px);
  display: flex;
  align-items: center;
  justify-content: center;
  visibility: hidden;
  opacity: 0;
  transition: opacity .25s, visibility .25s;
  z-index: 1000;
  padding: 20px;
}

.modal-overlay.active {
  visibility: visible;
  opacity: 1;
}

.modal-box {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 20px;
  width: min(420px, 100%);
  padding: 28px;
  box-shadow: 0 40px 80px rgba(0,0,0,0.6);
  transform: scale(0.95) translateY(10px);
  transition: transform .3s ease;
}

.modal-overlay.active .modal-box {
  transform: scale(1) translateY(0);
}

.modal-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 20px;
}

.modal-title {
  font-family: var(--font-head);
  font-size: 20px;
  font-weight: 800;
  letter-spacing: -0.3px;
  line-height: 1.2;
  color: var(--text);
  flex: 1;
  padding-right: 12px;
}

.modal-close {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: var(--surface2);
  border: 1px solid var(--border);
  color: var(--muted);
  font-size: 18px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all .2s;
  flex-shrink: 0;
  line-height: 1;
}

.modal-close:hover {
  background: rgba(239,68,68,0.12);
  border-color: rgba(239,68,68,0.2);
  color: #ef4444;
}

.modal-datetime {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 13px;
  color: var(--accent3);
  background: rgba(34,211,238,0.08);
  border: 1px solid rgba(34,211,238,0.15);
  padding: 7px 14px;
  border-radius: 100px;
  margin-bottom: 16px;
  font-weight: 500;
}

.modal-type-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  padding: 4px 12px;
  border-radius: 100px;
  font-weight: 600;
  font-family: var(--font-head);
  margin-bottom: 16px;
  margin-left: 8px;
}

.modal-type-pill.regular {
  background: rgba(124,109,250,0.12);
  color: var(--accent);
  border: 1px solid rgba(124,109,250,0.2);
}

.modal-type-pill.yearly {
  background: rgba(249,115,22,0.12);
  color: var(--accent2);
  border: 1px solid rgba(249,115,22,0.2);
}

.modal-desc {
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 14px 16px;
  font-size: 14px;
  line-height: 1.6;
  color: #b0baca;
  margin-bottom: 24px;
  min-height: 60px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
}

.btn-modal-close {
  display: inline-flex;
  align-items: center;
  padding: 10px 22px;
  background: var(--accent);
  color: white;
  border: none;
  border-radius: 100px;
  font-family: var(--font-head);
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all .2s;
  box-shadow: 0 4px 14px rgba(124,109,250,0.3);
}

.btn-modal-close:hover {
  background: #6d5fe8;
  transform: translateY(-1px);
  box-shadow: 0 8px 20px rgba(124,109,250,0.4);
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

  <a href="index.php">
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

  <a href="calendar.php" class="active">
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

<!-- ═══════════════ MAIN ═══════════════════ -->
<main class="main">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-left">
      <h1>Calendar</h1>
      <span class="event-count-pill" id="eventCountPill">— events</span>
    </div>
    <div class="topbar-right">
      <div class="legend">
        <div class="legend-item">
          <div class="legend-dot" style="background:var(--accent)"></div>
          Regular
        </div>
        <div class="legend-item">
          <div class="legend-dot" style="background:var(--accent2)"></div>
          Yearly
        </div>
      </div>
      <div class="profile-avatar" style="margin-left:14px">
        <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
      </div>
    </div>
  </div>

  <!-- CALENDAR -->
  <div class="calendar-card">
    <div id="calendar"></div>
  </div>

</main>

<!-- ═══════════════ MODAL ═══════════════════ -->
<div id="eventModal" class="modal-overlay" role="dialog" aria-modal="true">
  <div class="modal-box">

    <div class="modal-top">
      <div class="modal-title" id="modalTitle"></div>
      <button class="modal-close" onclick="closeModal()" aria-label="Close">×</button>
    </div>

    <div style="display:flex;align-items:center;flex-wrap:wrap;gap:0;margin-bottom:16px;">
      <div class="modal-datetime" id="modalDateTime"></div>
      <div class="modal-type-pill" id="modalTypePill"></div>
    </div>

    <div class="modal-desc" id="modalDescription"></div>

    <div class="modal-footer">
      <button class="btn-modal-close" onclick="closeModal()">Got it</button>
    </div>

  </div>
</div>

<script>
(function() {
  const events = <?php echo json_encode($events); ?>;

  // Update count pill
  const count = events.length;
  document.getElementById('eventCountPill').textContent = count + ' event' + (count !== 1 ? 's' : '');

  function formatEventDate(startStr) {
    if (!startStr) return '';
    const [datePart, timePart] = startStr.split('T');
    if (!datePart) return '';
    const dateObj = new Date(datePart + 'T00:00:00');
    const options = { year: 'numeric', month: 'long', day: 'numeric', weekday: 'short' };
    let formatted = dateObj.toLocaleDateString(undefined, options);
    if (timePart) formatted += ' at ' + timePart.substring(0, 5);
    return formatted;
  }

  document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      height: 680,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      events: events,
      eventDidMount: function(info) {
        if (info.event.extendedProps.description) {
          info.el.setAttribute('title', info.event.extendedProps.description);
        }
      },
      eventClick: function(info) {
        const title       = info.event.title;
        const description = info.event.extendedProps.description || 'No description provided.';
        const startStr    = info.event.startStr;
        const repeatType  = info.event.extendedProps.repeat_type || 'none';
        const isYearly    = repeatType === 'yearly';

        document.getElementById('modalTitle').textContent       = title;
        document.getElementById('modalDescription').textContent = description;
        document.getElementById('modalDateTime').innerHTML      = '🕒 ' + formatEventDate(startStr);

        const pill = document.getElementById('modalTypePill');
        if (isYearly) {
          pill.textContent  = '🔁 Yearly';
          pill.className    = 'modal-type-pill yearly';
        } else {
          pill.textContent  = '📌 One-time';
          pill.className    = 'modal-type-pill regular';
        }

        document.getElementById('eventModal').classList.add('active');
      }
    });

    calendar.render();
  });

  window.closeModal = function() {
    document.getElementById('eventModal').classList.remove('active');
  };

  const modal = document.getElementById('eventModal');
  modal.addEventListener('click', function(e) {
    if (e.target === modal) closeModal();
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modal.classList.contains('active')) closeModal();
  });
})();
</script>

</body>
</html>