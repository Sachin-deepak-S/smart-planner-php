<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$type = "";

// Fetch user
$result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);

// Update Name
if (isset($_POST['update_name'])) {
    $new_name = mysqli_real_escape_string($conn, $_POST['name']);
    if (mysqli_query($conn, "UPDATE users SET name='$new_name' WHERE id='$user_id'")) {
        $_SESSION['user_name'] = $new_name;
        $message = "Name updated successfully!";
        $type = "success";
    }
}

// Change Password
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!password_verify($current, $user['password'])) {
        $message = "Current password is incorrect.";
        $type = "error";
    } elseif ($new !== $confirm) {
        $message = "New passwords do not match.";
        $type = "error";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE id='$user_id'");
        $message = "Password changed successfully!";
        $type = "success";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile — Planner</title>
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

/* ─── PROFILE HERO ─────────────────────────────── */
.profile-hero {
  display: flex;
  align-items: center;
  gap: 20px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 24px 28px;
  margin-bottom: 24px;
  width: 100%; /* FIXED: was max-width: 620px */
  animation: fadeUp .35s ease .05s both;
}

.hero-avatar {
  width: 64px;
  height: 64px;
  border-radius: 16px;
  background: linear-gradient(135deg, var(--accent), #a78bfa);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-head);
  font-weight: 800;
  font-size: 26px;
  box-shadow: 0 6px 20px rgba(124,109,250,0.4);
  flex-shrink: 0;
}

.hero-info h2 {
  font-family: var(--font-head);
  font-size: 20px;
  font-weight: 800;
  letter-spacing: -0.3px;
}

.hero-info p {
  color: var(--muted);
  font-size: 13.5px;
  margin-top: 3px;
}

/* ─── CARDS ────────────────────────────────────── */
.cards-col {
  display: flex;
  flex-direction: column;
  gap: 20px;
  width: 100%; /* FIXED: was max-width: 620px */
}

.form-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow);
  animation: fadeUp .4s ease .1s both;
}

.form-card:nth-child(2) { animation-delay: .16s; }

.form-card-header {
  padding: 18px 24px 16px;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 10px;
}

.form-card-header h3 {
  font-family: var(--font-head);
  font-size: 15px;
  font-weight: 700;
  letter-spacing: -0.1px;
}

.header-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  flex-shrink: 0;
}

.icon-purple { background: rgba(124,109,250,0.12); }
.icon-orange { background: rgba(249,115,22,0.12); }

.form-body {
  padding: 22px 24px;
}

/* ─── FIELDS ───────────────────────────────────── */
.field {
  margin-bottom: 16px;
}

.field:last-of-type { margin-bottom: 0; }

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
input[type="password"] {
  width: 100%;
  padding: 11px 14px;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 10px;
  color: var(--text);
  font-family: var(--font-body);
  font-size: 14px;
  transition: border-color .2s, box-shadow .2s;
}

input[type="text"]::placeholder,
input[type="password"]::placeholder { color: var(--muted); }

input[type="text"]:focus,
input[type="password"]:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(124,109,250,0.15);
}

/* ─── SUBMIT BUTTONS ───────────────────────────── */
.btn-submit {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 12px 24px;
  border: none;
  border-radius: 100px;
  font-family: var(--font-head);
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all .2s ease;
  margin-top: 20px;
}

.btn-purple {
  background: var(--accent);
  color: white;
  box-shadow: 0 4px 16px rgba(124,109,250,0.3);
}

.btn-purple:hover {
  background: #6d5fe8;
  transform: translateY(-1px);
  box-shadow: 0 8px 24px rgba(124,109,250,0.4);
}

.btn-orange {
  background: var(--accent2);
  color: white;
  box-shadow: 0 4px 16px rgba(249,115,22,0.25);
}

.btn-orange:hover {
  background: #ea6a0a;
  transform: translateY(-1px);
  box-shadow: 0 8px 24px rgba(249,115,22,0.35);
}

/* ─── TOAST ────────────────────────────────────── */
.toast {
  position: fixed;
  bottom: 28px;
  right: 28px;
  display: flex;
  align-items: center;
  gap: 10px;
  background: var(--surface);
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
  flex-shrink: 0;
}

.toast.success {
  border: 1px solid rgba(34,197,94,0.25);
}

.toast.success .toast-dot {
  background: #22c55e;
  box-shadow: 0 0 8px rgba(34,197,94,0.6);
}

.toast.error {
  border: 1px solid rgba(239,68,68,0.25);
}

.toast.error .toast-dot {
  background: #ef4444;
  box-shadow: 0 0 8px rgba(239,68,68,0.6);
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

  <a href="calendar.php">
    <span class="nav-icon">📆</span> Calendar
  </a>
  <a href="profile.php" class="active">
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
      <h1>My Profile</h1>
      <p>Manage your account details and password</p>
    </div>
  </div>

  <!-- PROFILE HERO -->
  <div class="profile-hero">
    <div class="hero-avatar">
      <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
    </div>
    <div class="hero-info">
      <h2><?php echo htmlspecialchars($user['name']); ?></h2>
      <p><?php echo htmlspecialchars($user['email'] ?? 'No email on record'); ?></p>
    </div>
  </div>

  <!-- FORM CARDS -->
  <div class="cards-col">

    <!-- Update Name -->
    <div class="form-card">
      <div class="form-card-header">
        <div class="header-icon icon-purple">✏️</div>
        <h3>Update Name</h3>
      </div>
      <div class="form-body">
        <form method="POST">
          <div class="field">
            <label>Display Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
          </div>
          <button type="submit" name="update_name" class="btn-submit btn-purple">
            Save Name
          </button>
        </form>
      </div>
    </div>

    <!-- Change Password -->
    <div class="form-card">
      <div class="form-card-header">
        <div class="header-icon icon-orange">🔒</div>
        <h3>Change Password</h3>
      </div>
      <div class="form-body">
        <form method="POST">
          <div class="field">
            <label>Current Password</label>
            <input type="password" name="current_password" placeholder="Enter your current password" required>
          </div>
          <div class="field">
            <label>New Password</label>
            <input type="password" name="new_password" placeholder="Choose a strong new password" required>
          </div>
          <div class="field">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" placeholder="Repeat your new password" required>
          </div>
          <button type="submit" name="change_password" class="btn-submit btn-orange">
            Update Password
          </button>
        </form>
      </div>
    </div>

  </div>

</main>

<!-- TOAST -->
<div id="toast" class="toast <?php echo $type; ?>">
  <span class="toast-dot"></span>
  <?php echo htmlspecialchars($message); ?>
</div>

<script>
<?php if($message): ?>
const toast = document.getElementById("toast");
toast.classList.add("show");
setTimeout(() => { toast.classList.remove("show"); }, 3500);
<?php endif; ?>
</script>

</body>
</html>