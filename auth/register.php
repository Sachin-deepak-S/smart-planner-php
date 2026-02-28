<?php
include "../config/database.php";

$error   = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name             = mysqli_real_escape_string($conn, $_POST['name']);
    $email            = mysqli_real_escape_string($conn, $_POST['email']);
    $password_raw     = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password_raw !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $password = password_hash($password_raw, PASSWORD_DEFAULT);
        $check    = "SELECT * FROM users WHERE email='$email'";
        $result   = mysqli_query($conn, $check);

        if (mysqli_num_rows($result) > 0) {
            $error = "An account with that email already exists.";
        } else {
            $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
            if (mysqli_query($conn, $sql)) {
                $success = true;
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register — Planner</title>
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
  --accent4:   #a3e635;
  --text:      #e8eaf0;
  --muted:     #6b7280;
  --font-head: 'Syne', sans-serif;
  --font-body: 'DM Sans', sans-serif;
  --radius:    16px;
  --shadow:    0 8px 32px rgba(0,0,0,0.4);
}

html { scroll-behavior: smooth; }

body {
  background: var(--bg);
  color: var(--text);
  font-family: var(--font-body);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  position: relative;
  overflow: hidden;
}

/* Noise texture */
body::before {
  content: '';
  position: fixed;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
  pointer-events: none;
  z-index: 0;
  opacity: .5;
}

/* Ambient glow blobs */
body::after {
  content: '';
  position: fixed;
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, rgba(124,109,250,0.1) 0%, transparent 70%);
  top: -120px;
  right: -120px;
  pointer-events: none;
  z-index: 0;
}

.glow-blob {
  position: fixed;
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(163,230,53,0.06) 0%, transparent 70%);
  bottom: -100px;
  left: -100px;
  pointer-events: none;
  z-index: 0;
}

/* ─── CARD ─────────────────────────────────────── */
.card {
  position: relative;
  z-index: 1;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 24px;
  padding: 40px 36px;
  width: 100%;
  max-width: 440px;
  box-shadow: 0 32px 64px rgba(0,0,0,0.5);
  animation: fadeUp .5s cubic-bezier(0.23,1,0.32,1) both;
}

/* Brand */
.brand {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 32px;
}

.brand-icon {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--accent), #a78bfa);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  box-shadow: 0 0 20px rgba(124,109,250,0.4);
  flex-shrink: 0;
}

.brand-name {
  font-family: var(--font-head);
  font-size: 20px;
  font-weight: 800;
  letter-spacing: -0.3px;
}

/* Heading */
.card-heading {
  margin-bottom: 28px;
}

.card-heading h1 {
  font-family: var(--font-head);
  font-size: 24px;
  font-weight: 800;
  letter-spacing: -0.4px;
  margin-bottom: 5px;
}

.card-heading p {
  color: var(--muted);
  font-size: 13.5px;
}

/* ─── ALERT BOXES ──────────────────────────────── */
.alert {
  display: flex;
  align-items: center;
  gap: 9px;
  border-radius: 10px;
  padding: 11px 14px;
  font-size: 13.5px;
  margin-bottom: 20px;
}

.alert-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  flex-shrink: 0;
}

.alert.error {
  background: rgba(239,68,68,0.08);
  border: 1px solid rgba(239,68,68,0.2);
  color: #f87171;
}

.alert.error .alert-dot {
  background: #ef4444;
  box-shadow: 0 0 8px rgba(239,68,68,0.5);
}

.alert.success {
  background: rgba(34,197,94,0.08);
  border: 1px solid rgba(34,197,94,0.2);
  color: #4ade80;
}

.alert.success .alert-dot {
  background: #22c55e;
  box-shadow: 0 0 8px rgba(34,197,94,0.5);
}

/* ─── FIELDS ───────────────────────────────────── */
.field {
  margin-bottom: 16px;
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
input[type="email"],
input[type="password"] {
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

input::placeholder { color: var(--muted); }

input:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(124,109,250,0.15);
}

/* Password wrapper */
.password-wrap {
  position: relative;
}

.password-wrap input {
  padding-right: 44px;
}

.toggle-pw {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  font-size: 16px;
  opacity: 0.5;
  transition: opacity .2s;
  user-select: none;
  line-height: 1;
}

.toggle-pw:hover { opacity: 1; }

/* Password strength bar */
.strength-bar {
  display: flex;
  gap: 4px;
  margin-top: 8px;
}

.strength-seg {
  flex: 1;
  height: 3px;
  border-radius: 10px;
  background: var(--surface2);
  transition: background .3s;
}

.strength-label {
  font-size: 11px;
  color: var(--muted);
  margin-top: 5px;
  text-align: right;
  min-height: 14px;
  transition: color .3s;
}

/* ─── SUBMIT ───────────────────────────────────── */
.btn-submit {
  width: 100%;
  padding: 13px 24px;
  background: var(--accent);
  color: white;
  border: none;
  border-radius: 100px;
  font-family: var(--font-head);
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all .2s ease;
  box-shadow: 0 4px 18px rgba(124,109,250,0.35);
  margin-top: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-submit:hover {
  background: #6d5fe8;
  transform: translateY(-1px);
  box-shadow: 0 10px 28px rgba(124,109,250,0.45);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.spinner {
  display: none;
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin .7s linear infinite;
  flex-shrink: 0;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* ─── FOOTER ───────────────────────────────────── */
.divider {
  height: 1px;
  background: var(--border);
  margin: 24px 0;
}

.card-footer {
  text-align: center;
  font-size: 13.5px;
  color: var(--muted);
}

.card-footer a {
  color: var(--accent);
  text-decoration: none;
  font-weight: 500;
  transition: opacity .2s;
}

.card-footer a:hover { opacity: 0.8; }

/* ─── ANIMATIONS ───────────────────────────────── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(24px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ─── SCROLLBAR ────────────────────────────────── */
::-webkit-scrollbar { width: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 4px; }
</style>
</head>

<body>

<div class="glow-blob"></div>

<div class="card">

  <!-- Brand -->
  <div class="brand">
    <div class="brand-icon">🚀</div>
    <span class="brand-name">Planner</span>
  </div>

  <!-- Heading -->
  <div class="card-heading">
    <h1>Create account</h1>
    <p>Get started — it only takes a moment</p>
  </div>

  <!-- Alerts -->
  <?php if($error): ?>
  <div class="alert error">
    <span class="alert-dot"></span>
    <?php echo htmlspecialchars($error); ?>
  </div>
  <?php endif; ?>

  <?php if($success): ?>
  <div class="alert success">
    <span class="alert-dot"></span>
    Account created! Redirecting to login…
  </div>
  <?php endif; ?>

  <!-- Form -->
  <form method="POST" id="registerForm">

    <div class="field">
      <label>Full Name</label>
      <input type="text" name="name" placeholder="Your full name" required
             value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
    </div>

    <div class="field">
      <label>Email Address</label>
      <input type="email" name="email" placeholder="you@example.com" required
             value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
    </div>

    <div class="field">
      <label>Password</label>
      <div class="password-wrap">
        <input type="password" name="password" id="passwordField"
               placeholder="Create a strong password" required
               oninput="checkStrength(this.value)">
        <span class="toggle-pw" onclick="togglePassword('passwordField', this)">👁</span>
      </div>
      <div class="strength-bar">
        <div class="strength-seg" id="seg1"></div>
        <div class="strength-seg" id="seg2"></div>
        <div class="strength-seg" id="seg3"></div>
        <div class="strength-seg" id="seg4"></div>
      </div>
      <div class="strength-label" id="strengthLabel"></div>
    </div>

    <div class="field">
      <label>Confirm Password</label>
      <div class="password-wrap">
        <input type="password" name="confirm_password" id="confirmField"
               placeholder="Repeat your password" required>
        <span class="toggle-pw" onclick="togglePassword('confirmField', this)">👁</span>
      </div>
    </div>

    <button type="submit" class="btn-submit" id="registerBtn">
      Create Account
      <span class="spinner" id="spinner"></span>
    </button>

  </form>

  <div class="divider"></div>

  <div class="card-footer">
    Already have an account? <a href="login.php">Sign in</a>
  </div>

</div>

<script>
// Toggle password visibility
function togglePassword(id, icon) {
  const field = document.getElementById(id);
  if (field.type === "password") {
    field.type = "text";
    icon.textContent = "🙈";
  } else {
    field.type = "password";
    icon.textContent = "👁";
  }
}

// Password strength meter
function checkStrength(val) {
  const segs   = [1,2,3,4].map(i => document.getElementById("seg" + i));
  const label  = document.getElementById("strengthLabel");
  const colors = ["#ef4444","#f97316","#eab308","#a3e635"];
  const labels = ["Weak","Fair","Good","Strong"];

  let score = 0;
  if (val.length >= 8)              score++;
  if (/[A-Z]/.test(val))           score++;
  if (/[0-9]/.test(val))           score++;
  if (/[^A-Za-z0-9]/.test(val))    score++;

  segs.forEach((seg, i) => {
    seg.style.background = i < score ? colors[score - 1] : "var(--surface2)";
  });

  label.textContent  = val.length ? labels[score - 1] || "" : "";
  label.style.color  = val.length ? colors[score - 1] : "var(--muted)";
}

// Spinner on submit
document.getElementById("registerForm").addEventListener("submit", function() {
  document.getElementById("spinner").style.display = "inline-block";
  document.getElementById("registerBtn").disabled  = true;
});

// Auto-redirect on success
<?php if($success): ?>
setTimeout(() => { window.location.href = "login.php"; }, 2500);
<?php endif; ?>
</script>

</body>
</html>