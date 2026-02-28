<?php
session_start();
include "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql    = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            header("Location: ../index.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Planner</title>
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
  background: radial-gradient(circle, rgba(124,109,250,0.12) 0%, transparent 70%);
  top: -100px;
  left: -100px;
  pointer-events: none;
  z-index: 0;
}

.glow-blob {
  position: fixed;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(249,115,22,0.07) 0%, transparent 70%);
  bottom: -80px;
  right: -80px;
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
  max-width: 420px;
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

input[type="email"],
input[type="password"],
input[type="text"] {
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

/* ─── ERROR INLINE ─────────────────────────────── */
.error-box {
  display: flex;
  align-items: center;
  gap: 9px;
  background: rgba(239,68,68,0.08);
  border: 1px solid rgba(239,68,68,0.2);
  border-radius: 10px;
  padding: 11px 14px;
  font-size: 13.5px;
  color: #f87171;
  margin-bottom: 20px;
}

.error-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #ef4444;
  box-shadow: 0 0 8px rgba(239,68,68,0.5);
  flex-shrink: 0;
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
  position: relative;
  margin-top: 6px;
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

/* ─── FOOTER LINK ──────────────────────────────── */
.card-footer {
  text-align: center;
  margin-top: 24px;
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

/* ─── DIVIDER ──────────────────────────────────── */
.divider {
  height: 1px;
  background: var(--border);
  margin: 24px 0;
}

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
    <h1>Welcome back</h1>
    <p>Sign in to your account to continue</p>
  </div>

  <!-- Error -->
  <?php if($error): ?>
  <div class="error-box">
    <span class="error-dot"></span>
    <?php echo htmlspecialchars($error); ?>
  </div>
  <?php endif; ?>

  <!-- Form -->
  <form method="POST" id="loginForm">

    <div class="field">
      <label>Email Address</label>
      <input type="email" name="email" placeholder="you@example.com" required
             value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
    </div>

    <div class="field">
      <label>Password</label>
      <div class="password-wrap">
        <input type="password" name="password" id="passwordField" placeholder="Enter your password" required>
        <span class="toggle-pw" onclick="togglePassword()" title="Show/hide password">👁</span>
      </div>
    </div>

    <button type="submit" class="btn-submit" id="loginBtn">
      Sign In
      <span class="spinner" id="spinner"></span>
    </button>

  </form>

  <div class="divider"></div>

  <div class="card-footer">
    Don't have an account? <a href="register.php">Create one</a>
  </div>

</div>

<script>
function togglePassword() {
  const field = document.getElementById("passwordField");
  const icon  = document.querySelector(".toggle-pw");
  if (field.type === "password") {
    field.type = "text";
    icon.textContent = "🙈";
  } else {
    field.type = "password";
    icon.textContent = "👁";
  }
}

document.getElementById("loginForm").addEventListener("submit", function() {
  document.getElementById("spinner").style.display = "inline-block";
  document.getElementById("loginBtn").disabled = true;
});
</script>

</body>
</html>