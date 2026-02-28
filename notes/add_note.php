<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $is_important = isset($_POST['is_important']) ? 1 : 0;

    $sql = "INSERT INTO notes (user_id, title, description, is_important)
            VALUES ('$user_id', '$title', '$description', '$is_important')";

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
<title>Add Note — Planner</title>
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
  /* Ensure main takes all remaining width */
  min-width: 0;
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
  /* FIXED: removed max-width: 600px — now fills the full available area */
  width: 100%;
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

/* ─── FIELDS ───────────────────────────────────── */
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
textarea {
  width: 100%;
  padding: 12px 14px;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 10px;
  color: var(--text);
  font-family: var(--font-body);
  font-size: 14px;
  transition: border-color .2s, box-shadow .2s;
}

input[type="text"]::placeholder,
textarea::placeholder { color: var(--muted); }

input[type="text"]:focus,
textarea:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(124,109,250,0.15);
}

textarea {
  resize: none;
  min-height: 160px;
  overflow: hidden;
  line-height: 1.6;
}

/* Char counter */
.char-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 8px;
}

.char-track {
  flex: 1;
  height: 3px;
  background: var(--surface2);
  border-radius: 10px;
  margin-right: 12px;
  overflow: hidden;
}

.char-fill {
  height: 100%;
  width: 0%;
  background: var(--accent);
  border-radius: 10px;
  transition: width .2s, background .2s;
}

.char-fill.warn  { background: var(--accent2); }
.char-fill.limit { background: #ef4444; }

.char-count {
  font-size: 11.5px;
  color: var(--muted);
  white-space: nowrap;
}

/* ─── IMPORTANT CHECKBOX ───────────────────────── */
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
  border-color: rgba(249,115,22,0.3);
  background: rgba(249,115,22,0.04);
}

.checkbox-field input[type="checkbox"] {
  display: none;
}

.custom-box {
  width: 20px;
  height: 20px;
  border: 2px solid var(--border);
  border-radius: 6px;
  background: var(--surface);
  position: relative;
  flex-shrink: 0;
  transition: all .2s ease;
}

.checkbox-field input[type="checkbox"]:checked ~ .custom-box {
  background: linear-gradient(135deg, var(--accent2), #fbbf24);
  border-color: transparent;
  box-shadow: 0 0 10px rgba(249,115,22,0.3);
}

.checkbox-field input[type="checkbox"]:checked ~ .custom-box::after {
  content: "✔";
  color: white;
  font-size: 11px;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.checkbox-field .cb-label {
  font-size: 14px;
  color: #b0baca;
  user-select: none;
}

/* ─── BUTTONS ──────────────────────────────────── */
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
  position: relative;
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

.toast.show { opacity: 1; transform: translateY(0); }

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
  <a href="add_note.php" class="active">
    <span class="nav-icon">✏️</span> Add Note
  </a>
  <a href="view_notes.php">
    <span class="nav-icon">📝</span> View Notes
  </a>

  <div class="nav-label">Events</div>
  <a href="../events/add_event.php">
    <span class="nav-icon">➕</span> Add Event
  </a>
  <a href="../events/view_events.php">
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
      <h1>Add Note</h1>
      <p>Capture your thoughts and ideas</p>
    </div>
    <div class="profile-avatar">
      <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
    </div>
  </div>

  <!-- FORM CARD -->
  <div class="form-card">

    <div class="form-card-header">
      <h2>📝 Note Details</h2>
      <p>Fill in a title and optional description for your note</p>
    </div>

    <form method="POST" id="noteForm">

      <div class="field">
        <label>Title *</label>
        <input type="text" name="title" placeholder="Give your note a clear title…" required>
      </div>

      <div class="field">
        <label>Description</label>
        <textarea
          name="description"
          id="desc"
          placeholder="Write your note here…"
          maxlength="500"
          oninput="updateCounter(); autoResize(this);"></textarea>

        <div class="char-bar">
          <div class="char-track">
            <div class="char-fill" id="charFill"></div>
          </div>
          <span class="char-count"><span id="count">0</span> / 500</span>
        </div>
      </div>

      <!-- Important Checkbox -->
      <label class="checkbox-field">
        <input type="checkbox" name="is_important" id="importantCheck">
        <span class="custom-box" id="customBox"></span>
        <span class="cb-label">🔥 Mark as Important</span>
      </label>

      <div class="btn-row">
        <button type="submit" class="btn-submit" id="submitBtn">
          Save Note
          <span class="spinner" id="spinner"></span>
        </button>
        <a href="view_notes.php" class="btn-cancel">Cancel</a>
      </div>

    </form>

  </div>

</main>

<!-- TOAST -->
<div id="toast" class="toast">
  <span class="toast-dot"></span>
  Note saved successfully! Redirecting…
</div>

<script>
// Character counter + progress bar
function updateCounter() {
  const len = document.getElementById("desc").value.length;
  const pct = (len / 500) * 100;
  const fill = document.getElementById("charFill");
  document.getElementById("count").textContent = len;
  fill.style.width = pct + "%";
  fill.className = "char-fill" + (pct >= 100 ? " limit" : pct >= 80 ? " warn" : "");
}

// Auto-resize textarea
function autoResize(el) {
  el.style.height = "auto";
  el.style.height = el.scrollHeight + "px";
}

// Sync custom checkbox visual with actual checkbox
document.getElementById("importantCheck").addEventListener("change", function() {
  const box = document.getElementById("customBox");
  if (this.checked) {
    box.style.background = "linear-gradient(135deg, #f97316, #fbbf24)";
    box.style.borderColor = "transparent";
    box.style.boxShadow = "0 0 10px rgba(249,115,22,0.3)";
    box.innerHTML = '<span style="color:white;font-size:11px;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%)">✔</span>';
  } else {
    box.style.background = "var(--surface)";
    box.style.borderColor = "var(--border)";
    box.style.boxShadow = "none";
    box.innerHTML = "";
  }
});

// Spinner on submit
document.getElementById("noteForm").addEventListener("submit", function() {
  document.getElementById("spinner").style.display = "inline-block";
  document.getElementById("submitBtn").disabled = true;
});

// Toast + redirect on success
<?php if($success): ?>
const toast = document.getElementById("toast");
toast.classList.add("show");
setTimeout(() => {
  toast.classList.remove("show");
  window.location.href = "../notes/view_notes.php";
}, 2000);
<?php endif; ?>
</script>

</body>
</html>