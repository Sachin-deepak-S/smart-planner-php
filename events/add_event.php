<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $repeat_type = $_POST['repeat_type'];
    $notify_before = isset($_POST['notify_before']) ? 1 : 0;

    $sql = "INSERT INTO events 
            (user_id, title, description, event_date, event_time, repeat_type, notify_before)
            VALUES 
            ('$user_id', '$title', '$description', '$event_date', '$event_time', '$repeat_type', '$notify_before')";

    if (mysqli_query($conn, $sql)) {
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Event — Planner</title>
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
  min-width: 0; /* prevent flex overflow */
}

/* ─── TOPBAR ───────────────────────────────────── */
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 36px;
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

/* ─── FORM CARD ────────────────────────────────── */
.form-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 32px;
  width: 100%; /* FIXED: was max-width: 620px — now fills full area */
  box-shadow: var(--shadow);
  animation: fadeUp .4s ease .1s both;
}

.form-card-header {
  margin-bottom: 28px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--border);
}

.form-card-header h2 {
  font-family: var(--font-head);
  font-size: 18px;
  font-weight: 700;
  letter-spacing: -0.2px;
}

.form-card-header p {
  color: var(--muted);
  font-size: 13px;
  margin-top: 4px;
}

/* ─── FORM FIELDS ──────────────────────────────── */
.field {
  margin-bottom: 18px;
}

.field label {
  display: block;
  font-size: 12px;
  font-weight: 500;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 7px;
}

input[type="text"],
input[type="date"],
input[type="time"],
textarea,
select {
  width: 100%;
  padding: 12px 14px;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 10px;
  color: var(--text);
  font-family: var(--font-body);
  font-size: 14px;
  transition: border-color .2s, box-shadow .2s;
  appearance: none;
  -webkit-appearance: none;
}

input[type="text"]::placeholder,
textarea::placeholder { color: var(--muted); }

input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator {
  filter: invert(0.6);
  cursor: pointer;
}

input:focus, textarea:focus, select:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(124,109,250,0.15);
}

textarea {
  resize: vertical;
  min-height: 100px;
}

select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  padding-right: 36px;
}

select option {
  background: var(--surface2);
  color: var(--text);
}

/* Row layout */
.field-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
  margin-bottom: 18px;
}

/* Checkbox */
.checkbox-field {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 28px;
  padding: 14px 16px;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 10px;
  cursor: pointer;
  transition: border-color .2s, background .2s;
}

.checkbox-field:hover {
  border-color: rgba(124,109,250,0.3);
  background: rgba(124,109,250,0.05);
}

.checkbox-field input[type="checkbox"] {
  width: 18px;
  height: 18px;
  border-radius: 5px;
  accent-color: var(--accent);
  cursor: pointer;
  flex-shrink: 0;
}

.checkbox-field label {
  font-size: 14px;
  color: #b0baca;
  cursor: pointer;
  user-select: none;
}

/* ─── SUBMIT BUTTON ────────────────────────────── */
.btn-row {
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-submit {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 13px 28px;
  background: var(--accent);
  color: white;
  border: none;
  border-radius: 100px;
  font-family: var(--font-head);
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all .2s ease;
  box-shadow: 0 4px 16px rgba(124,109,250,0.35);
}

.btn-submit:hover {
  background: #6d5fe8;
  transform: translateY(-1px);
  box-shadow: 0 8px 24px rgba(124,109,250,0.45);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-cancel {
  display: inline-flex;
  align-items: center;
  padding: 13px 20px;
  background: transparent;
  border: 1px solid var(--border);
  color: var(--muted);
  border-radius: 100px;
  font-size: 14px;
  text-decoration: none;
  transition: all .2s;
}

.btn-cancel:hover {
  border-color: rgba(255,255,255,0.12);
  color: var(--text);
}

/* Spinner */
.spinner {
  display: none;
  width: 15px;
  height: 15px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* ─── TOAST ────────────────────────────────────── */
.toast {
  position: fixed;
  bottom: 28px;
  right: 28px;
  display: flex;
  align-items: center;
  gap: 10px;
  background: var(--surface);
  border: 1px solid rgba(34,197,94,0.25);
  color: var(--text);
  padding: 14px 20px;
  border-radius: 12px;
  font-size: 14px;
  box-shadow: 0 16px 40px rgba(0,0,0,0.5);
  opacity: 0;
  transform: translateY(12px);
  transition: opacity .3s, transform .3s;
  z-index: 999;
  pointer-events: none;
}

.toast-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #22c55e;
  flex-shrink: 0;
  box-shadow: 0 0 8px rgba(34,197,94,0.6);
}

.toast.show {
  opacity: 1;
  transform: translateY(0);
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
  <a href="add_event.php" class="active">
    <span class="nav-icon">➕</span> Add Event
  </a>
  <a href="view_events.php">
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
      <h1>Add New Event</h1>
      <p>Fill in the details to schedule your event</p>
    </div>
    <div class="profile-avatar">
      <?php echo strtoupper(substr($_SESSION['user_name'],0,1)); ?>
    </div>
  </div>

  <!-- FORM CARD -->
  <div class="form-card">

    <div class="form-card-header">
      <h2>📌 Event Details</h2>
      <p>All fields marked are required to save the event</p>
    </div>

    <form method="POST" id="eventForm">

      <div class="field">
        <label>Event Title *</label>
        <input type="text" name="title" placeholder="e.g. Team standup, Doctor's appointment…" required>
      </div>

      <div class="field">
        <label>Description</label>
        <textarea name="description" placeholder="Add any notes or context for this event…"></textarea>
      </div>

      <div class="field-row">
        <div class="field" style="margin-bottom:0">
          <label>Date *</label>
          <input type="date" name="event_date" required>
        </div>
        <div class="field" style="margin-bottom:0">
          <label>Time</label>
          <input type="time" name="event_time">
        </div>
      </div>

      <div class="field" style="margin-top:18px">
        <label>Repeat</label>
        <select name="repeat_type">
          <option value="none">No Repeat</option>
          <option value="yearly">Repeat Yearly</option>
        </select>
      </div>

      <label class="checkbox-field">
        <input type="checkbox" name="notify_before" id="notify">
        <span>🔔 Notify me 1 day before this event</span>
      </label>

      <div class="btn-row">
        <button type="submit" class="btn-submit" id="submitBtn">
          Save Event
          <span class="spinner" id="spinner"></span>
        </button>
        <a href="view_events.php" class="btn-cancel">Cancel</a>
      </div>

    </form>

  </div>

</main>

<!-- TOAST -->
<div id="toast" class="toast">
  <span class="toast-dot"></span>
  Event added successfully! Redirecting…
</div>

<script>
document.getElementById("eventForm").addEventListener("submit", function() {
  document.getElementById("spinner").style.display = "inline-block";
  document.getElementById("submitBtn").disabled = true;
});

<?php if ($success): ?>
const toast = document.getElementById("toast");
toast.classList.add("show");
setTimeout(() => {
  window.location.href = "view_events.php";
}, 2000);
<?php endif; ?>
</script>

</body>
</html>