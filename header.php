<?php
$pageTitle = $pageTitle ?? 'Hospital Management System';
$activePage = $activePage ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | MediCare HMS</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <a class="brand" href="index.php"><span class="brand-mark">+</span><span>MediCare <small>Hospital System</small></span></a>
    <nav>
      <a class="<?= $activePage === 'dashboard' ? 'active' : '' ?>" href="index.php">▦ <span>Dashboard</span></a>
      <a class="<?= $activePage === 'patients' ? 'active' : '' ?>" href="patients.php">♡ <span>Patients</span></a>
      <a class="<?= $activePage === 'doctors' ? 'active' : '' ?>" href="doctors.php">✚ <span>Doctors</span></a>
      <a class="<?= $activePage === 'appointments' ? 'active' : '' ?>" href="appointments.php">▣ <span>Appointments</span></a>
      <a class="<?= $activePage === 'bills' ? 'active' : '' ?>" href="bills.php">＄ <span>Billing</span></a>
    </nav>
    <div class="sidebar-note"><strong>Hospital Management</strong><p>Manage everyday hospital records in one place.</p></div>
  </aside>
  <main class="main-content">
    <header class="topbar"><div><span class="eyebrow">ADMINISTRATION</span><h1><?= e($pageTitle) ?></h1></div><div class="profile"><span class="profile-dot">N</span><span><strong>Administrator</strong><small>Project demo</small></span></div></header>
