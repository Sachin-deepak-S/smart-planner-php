<?php
session_start();
if(isset($_SESSION['user_id'])){
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Planner | Notes & Event Manager</title>

<meta name="description" content="Smart Planner helps students manage notes, events, reminders and email notifications easily.">
<meta name="keywords" content="student planner, notes manager, event reminder app, productivity tool">
<meta name="author" content="Sachin Deepak">
<meta name="robots" content="index, follow">

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
  --radius:    16px;
  --shadow:    0 8px 32px rgba(0,0,0,0.4);
}

html { scroll-behavior: smooth; }

body {
  background: var(--bg);
  color: var(--text);
  font-family: var(--font-body);
  min-height: 100vh;
  overflow-x: hidden;
}

/* Noise texture overlay */
body::before {
  content: '';
  position: fixed;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
  pointer-events: none;
  z-index: 0;
  opacity: .4;
}

/* Ambient glow blobs */
.glow-blob {
  position: fixed;
  border-radius: 50%;
  filter: blur(120px);
  pointer-events: none;
  z-index: 0;
  opacity: 0.12;
}

.glow-blob-1 {
  width: 600px;
  height: 600px;
  background: var(--accent);
  top: -200px;
  right: -100px;
}

.glow-blob-2 {
  width: 400px;
  height: 400px;
  background: var(--accent2);
  bottom: 200px;
  left: -100px;
}

.glow-blob-3 {
  width: 300px;
  height: 300px;
  background: var(--accent3);
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

/* ─── NAVBAR ───────────────────────────────────── */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 22px 60px;
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(13,15,20,0.8);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-bottom: 1px solid var(--border);
  animation: fadeDown .4s ease both;
}

.nav-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
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
  box-shadow: 0 0 20px rgba(124,109,250,0.4);
  flex-shrink: 0;
}

.logo-text {
  font-family: var(--font-head);
  font-size: 18px;
  font-weight: 800;
  letter-spacing: -0.3px;
  color: var(--text);
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 8px;
}

.nav-link {
  padding: 9px 18px;
  border-radius: 100px;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  color: #8b9bb4;
  transition: all .2s ease;
}

.nav-link:hover {
  color: var(--text);
  background: var(--surface2);
}

.nav-link-cta {
  background: var(--accent);
  color: white !important;
  font-family: var(--font-head);
  font-weight: 700;
  box-shadow: 0 4px 16px rgba(124,109,250,0.35);
}

.nav-link-cta:hover {
  background: #6d5fe8 !important;
  transform: translateY(-1px);
  box-shadow: 0 8px 24px rgba(124,109,250,0.45) !important;
}

/* ─── HERO ─────────────────────────────────────── */
.hero {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 110px 20px 90px;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 100px;
  padding: 7px 16px;
  font-size: 12.5px;
  color: var(--muted);
  margin-bottom: 28px;
  animation: fadeUp .4s ease .1s both;
}

.hero-badge-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--accent4);
  box-shadow: 0 0 8px rgba(163,230,53,0.6);
  animation: pulse 2s ease infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.6; transform: scale(0.8); }
}

.hero h1 {
  font-family: var(--font-head);
  font-size: clamp(38px, 6vw, 68px);
  font-weight: 800;
  letter-spacing: -1.5px;
  line-height: 1.08;
  margin-bottom: 22px;
  animation: fadeUp .4s ease .2s both;
}

.hero h1 .accent-word {
  background: linear-gradient(135deg, var(--accent), #a78bfa, var(--accent3));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero p {
  font-size: 17px;
  line-height: 1.7;
  color: var(--muted);
  max-width: 560px;
  margin-bottom: 40px;
  animation: fadeUp .4s ease .3s both;
}

.hero-buttons {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: center;
  animation: fadeUp .4s ease .4s both;
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 14px 32px;
  background: var(--accent);
  color: white;
  border-radius: 100px;
  text-decoration: none;
  font-family: var(--font-head);
  font-size: 15px;
  font-weight: 700;
  transition: all .2s ease;
  box-shadow: 0 4px 20px rgba(124,109,250,0.4);
}

.btn-primary:hover {
  background: #6d5fe8;
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(124,109,250,0.5);
}

.btn-secondary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 14px 28px;
  background: transparent;
  border: 1px solid var(--border);
  color: #8b9bb4;
  border-radius: 100px;
  text-decoration: none;
  font-size: 15px;
  font-weight: 500;
  transition: all .2s ease;
}

.btn-secondary:hover {
  border-color: rgba(255,255,255,0.12);
  color: var(--text);
  background: var(--surface);
}

/* Hero stats strip */
.hero-stats {
  display: flex;
  align-items: center;
  gap: 40px;
  margin-top: 64px;
  padding-top: 40px;
  border-top: 1px solid var(--border);
  animation: fadeUp .4s ease .55s both;
}

.stat {
  text-align: center;
}

.stat-num {
  font-family: var(--font-head);
  font-size: 28px;
  font-weight: 800;
  letter-spacing: -0.5px;
  color: var(--text);
}

.stat-label {
  font-size: 12px;
  color: var(--muted);
  margin-top: 3px;
}

.stat-divider {
  width: 1px;
  height: 40px;
  background: var(--border);
}

/* ─── FEATURES SECTION ─────────────────────────── */
.section {
  position: relative;
  z-index: 1;
  padding: 90px 60px;
}

.section-label {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--accent);
  margin-bottom: 16px;
}

.section-label::before {
  content: '';
  width: 20px;
  height: 2px;
  background: var(--accent);
  border-radius: 2px;
}

.section-title {
  font-family: var(--font-head);
  font-size: clamp(28px, 3vw, 40px);
  font-weight: 800;
  letter-spacing: -0.8px;
  margin-bottom: 12px;
}

.section-sub {
  font-size: 15px;
  color: var(--muted);
  max-width: 480px;
  line-height: 1.6;
  margin-bottom: 56px;
}

/* Feature grid */
.feature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  max-width: 1100px;
  margin: 0 auto;
}

.feature-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 28px;
  transition: all .25s ease;
  position: relative;
  overflow: hidden;
}

.feature-card::before {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: var(--radius);
  background: linear-gradient(135deg, rgba(124,109,250,0.04), transparent);
  opacity: 0;
  transition: opacity .25s ease;
}

.feature-card:hover {
  border-color: rgba(124,109,250,0.2);
  transform: translateY(-3px);
  box-shadow: 0 16px 40px rgba(0,0,0,0.3);
}

.feature-card:hover::before {
  opacity: 1;
}

.feature-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  margin-bottom: 18px;
  flex-shrink: 0;
}

.fi-purple { background: rgba(124,109,250,0.12); }
.fi-orange { background: rgba(249,115,22,0.12); }
.fi-cyan   { background: rgba(34,211,238,0.12); }
.fi-green  { background: rgba(163,230,53,0.12); }

.feature-card h3 {
  font-family: var(--font-head);
  font-size: 16px;
  font-weight: 700;
  margin-bottom: 10px;
  letter-spacing: -0.2px;
}

.feature-card p {
  font-size: 14px;
  color: var(--muted);
  line-height: 1.65;
}

/* ─── HOW IT WORKS ─────────────────────────────── */
.how-section {
  position: relative;
  z-index: 1;
  padding: 90px 60px;
  background: var(--surface);
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
}

.steps-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 24px;
  max-width: 1100px;
  margin: 0 auto;
}

.step {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.step-num {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: linear-gradient(135deg, var(--accent), #a78bfa);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-head);
  font-size: 15px;
  font-weight: 800;
  color: white;
  box-shadow: 0 4px 16px rgba(124,109,250,0.35);
  flex-shrink: 0;
}

.step h4 {
  font-family: var(--font-head);
  font-size: 15px;
  font-weight: 700;
  letter-spacing: -0.1px;
}

.step p {
  font-size: 13.5px;
  color: var(--muted);
  line-height: 1.6;
}

/* ─── CTA BANNER ───────────────────────────────── */
.cta-section {
  position: relative;
  z-index: 1;
  padding: 90px 60px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  overflow: hidden;
}

.cta-card {
  background: var(--surface);
  border: 1px solid rgba(124,109,250,0.2);
  border-radius: 24px;
  padding: 64px 48px;
  max-width: 640px;
  width: 100%;
  position: relative;
  overflow: hidden;
  box-shadow: 0 0 60px rgba(124,109,250,0.1);
}

.cta-card::before {
  content: '';
  position: absolute;
  top: -60px;
  right: -60px;
  width: 200px;
  height: 200px;
  background: radial-gradient(circle, rgba(124,109,250,0.15), transparent 70%);
  pointer-events: none;
}

.cta-card::after {
  content: '';
  position: absolute;
  bottom: -60px;
  left: -60px;
  width: 200px;
  height: 200px;
  background: radial-gradient(circle, rgba(249,115,22,0.1), transparent 70%);
  pointer-events: none;
}

.cta-card h2 {
  font-family: var(--font-head);
  font-size: clamp(24px, 3vw, 36px);
  font-weight: 800;
  letter-spacing: -0.8px;
  margin-bottom: 14px;
}

.cta-card p {
  color: var(--muted);
  font-size: 15px;
  line-height: 1.65;
  margin-bottom: 36px;
}

/* ─── FOOTER ───────────────────────────────────── */
.footer {
  position: relative;
  z-index: 1;
  background: var(--surface);
  border-top: 1px solid var(--border);
}

.footer-top {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 48px;
  padding: 56px 60px 48px;
  max-width: 1200px;
  margin: 0 auto;
}

/* Brand col */
.footer-brand {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.footer-logo {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  width: fit-content;
}

.footer-logo-icon {
  width: 34px;
  height: 34px;
  border-radius: 9px;
  background: linear-gradient(135deg, var(--accent), #a78bfa);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  box-shadow: 0 0 16px rgba(124,109,250,0.3);
}

.footer-logo-text {
  font-family: var(--font-head);
  font-size: 16px;
  font-weight: 800;
  color: var(--text);
}

.footer-brand-desc {
  font-size: 13.5px;
  color: var(--muted);
  line-height: 1.65;
  max-width: 340px;
}

.footer-nav-links {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-top: 4px;
}

.footer-nav-links a {
  font-size: 12.5px;
  color: var(--muted);
  text-decoration: none;
  padding: 5px 12px;
  border: 1px solid var(--border);
  border-radius: 100px;
  transition: all .2s;
}

.footer-nav-links a:hover {
  color: var(--text);
  border-color: rgba(255,255,255,0.12);
  background: var(--surface2);
}

/* Dev card */
.footer-dev-card {
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 28px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.footer-dev-header {
  display: flex;
  align-items: center;
  gap: 14px;
}

.footer-dev-avatar {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: linear-gradient(135deg, var(--accent), #a78bfa);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-head);
  font-weight: 800;
  font-size: 22px;
  box-shadow: 0 4px 16px rgba(124,109,250,0.35);
  flex-shrink: 0;
}

.footer-dev-name {
  font-family: var(--font-head);
  font-size: 16px;
  font-weight: 800;
  letter-spacing: -0.2px;
}

.footer-dev-role {
  font-size: 12.5px;
  color: var(--muted);
  margin-top: 3px;
}

.footer-contacts {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.footer-contact-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13.5px;
  color: #8b9bb4;
  text-decoration: none;
  transition: color .2s;
}

.footer-contact-item:hover { color: var(--text); }

.contact-icon {
  width: 30px;
  height: 30px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  flex-shrink: 0;
}

.footer-socials {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.social-btn {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 8px 14px;
  border-radius: 100px;
  font-size: 12.5px;
  font-weight: 500;
  text-decoration: none;
  border: 1px solid var(--border);
  transition: all .2s ease;
}

.social-linkedin { color: #0a66c2; }
.social-linkedin:hover { background: rgba(10,102,194,0.1); border-color: rgba(10,102,194,0.3); color: #3b9fd8; }

.social-github { color: #8b9bb4; }
.social-github:hover { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.12); color: var(--text); }

.social-x { color: #8b9bb4; }
.social-x:hover { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.12); color: var(--text); }

/* Bottom bar */
.footer-bottom {
  border-top: 1px solid var(--border);
  padding: 18px 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  font-size: 13px;
  color: var(--muted);
  max-width: 100%;
}

.footer-bottom strong { color: var(--text); font-weight: 600; }

/* ─── ANIMATIONS ───────────────────────────────── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes fadeDown {
  from { opacity: 0; transform: translateY(-12px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Scroll reveal */
.reveal {
  opacity: 0;
  transform: translateY(24px);
  transition: opacity .5s ease, transform .5s ease;
}

.reveal.visible {
  opacity: 1;
  transform: translateY(0);
}

/* ─── SCROLLBAR ────────────────────────────────── */
::-webkit-scrollbar { width: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 4px; }
</style>
</head>

<body>

<!-- Ambient glow blobs -->
<div class="glow-blob glow-blob-1"></div>
<div class="glow-blob glow-blob-2"></div>
<div class="glow-blob glow-blob-3"></div>

<!-- ═══════════════ NAVBAR ═══════════════════════ -->
<nav class="navbar">
  <a href="#" class="nav-logo">
    <div class="logo-icon">🚀</div>
    <span class="logo-text">Planner</span>
  </a>
  <div class="nav-links">
    <a href="#features" class="nav-link">Features</a>
    <a href="#how" class="nav-link">How it Works</a>
    <a href="auth/login.php" class="nav-link">Login</a>
    <a href="auth/register.php" class="nav-link nav-link-cta">Get Started</a>
  </div>
</nav>

<!-- ═══════════════ HERO ══════════════════════════ -->
<section class="hero">
  <div class="hero-badge">
    <span class="hero-badge-dot"></span>
    Now with email reminders & recurring events
  </div>

  <h1>Organize Your Life<br><span class="accent-word">Smarter</span></h1>

  <p>
    Smart Planner helps students and professionals manage notes,
    track important events, set reminders, and receive email
    notifications — all in one beautiful place.
  </p>

  <div class="hero-buttons">
    <a href="auth/register.php" class="btn-primary">
      🚀 Get Started Free
    </a>
    <a href="auth/login.php" class="btn-secondary">
      Sign In →
    </a>
  </div>

  <div class="hero-stats">
    <div class="stat">
      <div class="stat-num">📝</div>
      <div class="stat-label">Notes Manager</div>
    </div>
    <div class="stat-divider"></div>
    <div class="stat">
      <div class="stat-num">📅</div>
      <div class="stat-label">Event Scheduler</div>
    </div>
    <div class="stat-divider"></div>
    <div class="stat">
      <div class="stat-num">🔔</div>
      <div class="stat-label">Smart Reminders</div>
    </div>
    <div class="stat-divider"></div>
    <div class="stat">
      <div class="stat-num">📧</div>
      <div class="stat-label">Email Alerts</div>
    </div>
  </div>
</section>

<!-- ═══════════════ FEATURES ══════════════════════ -->
<section class="section" id="features">
  <div style="max-width:1100px; margin:0 auto;">
    <div class="section-label reveal">Features</div>
    <h2 class="section-title reveal">Everything you need to<br>stay organized</h2>
    <p class="section-sub reveal">A complete productivity toolkit designed for students and professionals who want to stay on top of their day.</p>

    <div class="feature-grid">
      <div class="feature-card reveal">
        <div class="feature-icon fi-purple">📝</div>
        <h3>Notes Management</h3>
        <p>Create, organize, and mark important notes for quick access. Never lose track of your thoughts again.</p>
      </div>

      <div class="feature-card reveal">
        <div class="feature-icon fi-orange">📅</div>
        <h3>Event Scheduling</h3>
        <p>Add events with recurring options like yearly repeats and fine-grained reminder settings.</p>
      </div>

      <div class="feature-card reveal">
        <div class="feature-icon fi-cyan">🔔</div>
        <h3>Smart Notifications</h3>
        <p>Get in-app reminders one day before important events so you're always prepared.</p>
      </div>

      <div class="feature-card reveal">
        <div class="feature-icon fi-green">📧</div>
        <h3>Email Alerts</h3>
        <p>Receive automated email notifications for upcoming events directly to your inbox.</p>
      </div>

      <div class="feature-card reveal">
        <div class="feature-icon fi-purple">📆</div>
        <h3>Calendar View</h3>
        <p>Visualize all your events in a clean monthly calendar for a birds-eye view of your schedule.</p>
      </div>

      <div class="feature-card reveal">
        <div class="feature-icon fi-orange">🔒</div>
        <h3>Secure Account</h3>
        <p>Your data is protected with hashed passwords and session-based authentication.</p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ HOW IT WORKS ══════════════════ -->
<section class="how-section" id="how">
  <div style="max-width:1100px; margin:0 auto;">
    <div class="section-label reveal">How it Works</div>
    <h2 class="section-title reveal">Up and running in minutes</h2>
    <p class="section-sub reveal">No complicated setup. Just sign up and start organizing.</p>

    <div class="steps-grid">
      <div class="step reveal">
        <div class="step-num">1</div>
        <h4>Create an Account</h4>
        <p>Sign up for free with your name and email. No credit card required.</p>
      </div>

      <div class="step reveal">
        <div class="step-num">2</div>
        <h4>Add Your Notes</h4>
        <p>Jot down ideas, tasks, and thoughts. Mark the critical ones as important.</p>
      </div>

      <div class="step reveal">
        <div class="step-num">3</div>
        <h4>Schedule Events</h4>
        <p>Set dates, times, and repeating rules for any upcoming event.</p>
      </div>

      <div class="step reveal">
        <div class="step-num">4</div>
        <h4>Get Reminded</h4>
        <p>Receive smart notifications and email alerts before your events arrive.</p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ CTA ═══════════════════════════ -->
<section class="cta-section">
  <div class="cta-card reveal">
    <h2>Ready to get organized?</h2>
    <p>Join Smart Planner today and take control of your notes, events, and reminders — completely free.</p>
    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
      <a href="auth/register.php" class="btn-primary">🚀 Start for Free</a>
      <a href="auth/login.php" class="btn-secondary">Sign In →</a>
    </div>
  </div>
</section>

<!-- ═══════════════ FOOTER ════════════════════════ -->
<footer class="footer">

  <!-- Top row -->
  <div class="footer-top">

    <!-- Brand -->
    <div class="footer-brand">
      <a href="#" class="footer-logo">
        <div class="footer-logo-icon">🚀</div>
        <span class="footer-logo-text">Planner</span>
      </a>
      <p class="footer-brand-desc">Smart Planner — a productivity tool for students and professionals to manage notes, events, and reminders in one place.</p>
      <div class="footer-nav-links">
        <a href="auth/login.php">Login</a>
        <a href="auth/register.php">Register</a>
        <a href="#features">Features</a>
        <a href="#how">How it Works</a>
      </div>
    </div>

    <!-- Developer card -->
    <div class="footer-dev-card">
      <div class="footer-dev-header">
        <div class="footer-dev-avatar">S</div>
        <div>
          <div class="footer-dev-name">Sachin Deepak S</div>
          <div class="footer-dev-role">Full Stack Developer</div>
        </div>
      </div>

      <div class="footer-contacts">
        <a href="tel:+917904455034" class="footer-contact-item">
          <span class="contact-icon">📞</span>
          <span>+91 7904455034</span>
        </a>
        <a href="mailto:dgvdddgv80@gmail.com" class="footer-contact-item">
          <span class="contact-icon">✉️</span>
          <span>dgvdddgv80@gmail.com</span>
        </a>
      </div>

      <div class="footer-socials">
        <a href="https://www.linkedin.com/in/sachin-deepak-s" target="_blank" rel="noopener" class="social-btn social-linkedin" title="LinkedIn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
          LinkedIn
        </a>
        <a href="https://github.com/Sachin-deepak-S" target="_blank" rel="noopener" class="social-btn social-github" title="GitHub">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
          GitHub
        </a>
        <a href="https://x.com/s_sachin35745" target="_blank" rel="noopener" class="social-btn social-x" title="X / Twitter">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          X / Twitter
        </a>
      </div>
    </div>

  </div>

  <!-- Bottom bar -->
  <div class="footer-bottom">
    <span>© <?php echo date("Y"); ?> Smart Planner. All rights reserved.</span>
    <span>Built with ❤️ by <strong>Sachin Deepak S</strong></span>
  </div>

</footer>

<script>
// Scroll reveal
const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry, i) => {
    if (entry.isIntersecting) {
      // stagger children in same parent
      const siblings = entry.target.parentElement.querySelectorAll('.reveal');
      siblings.forEach((el, idx) => {
        setTimeout(() => el.classList.add('visible'), idx * 80);
      });
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// Smooth scroll for nav links
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const target = document.querySelector(a.getAttribute('href'));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});
</script>

</body>
</html>