<?php
require __DIR__ . '/config.php';
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
$stats = [
  'patients' => (int)$pdo->query('SELECT COUNT(*) FROM Patient')->fetchColumn(),
  'doctors' => (int)$pdo->query('SELECT COUNT(*) FROM Doctor')->fetchColumn(),
  'appointments' => (int)$pdo->query('SELECT COUNT(*) FROM Appointment')->fetchColumn(),
  'pending' => (int)$pdo->query("SELECT COUNT(*) FROM Bill WHERE pay_status='Pending'")->fetchColumn(),
];
$stmt = $pdo->query("SELECT a.appt_id, a.appt_date, a.status, p.name AS patient_name, d.name AS doctor_name
 FROM Appointment a JOIN Patient p ON p.patient_id=a.patient_id JOIN Doctor d ON d.doctor_id=a.doctor_id
 ORDER BY a.appt_date DESC LIMIT 6");
$recentAppointments = $stmt->fetchAll();
require __DIR__ . '/header.php';
?>
<section class="welcome-card"><div><span class="eyebrow light">HOSPITAL OVERVIEW</span><h2>Good care starts with<br>organized records.</h2><p>Keep track of patients, medical staff, appointments, and billing.</p><a class="button button-light" href="appointments.php">Manage appointments <span>→</span></a></div><div class="welcome-art" aria-hidden="true"><div class="medical-cross">+</div><div class="art-circle circle-one"></div><div class="art-circle circle-two"></div></div></section>
<section class="stats-grid">
  <article class="stat-card"><span class="stat-icon blue">♡</span><span class="stat-label">Total patients</span><strong><?= $stats['patients'] ?></strong><small>Registered patient records</small></article>
  <article class="stat-card"><span class="stat-icon purple">✚</span><span class="stat-label">Doctors</span><strong><?= $stats['doctors'] ?></strong><small>Medical professionals</small></article>
  <article class="stat-card"><span class="stat-icon orange">▣</span><span class="stat-label">Appointments</span><strong><?= $stats['appointments'] ?></strong><small>All scheduled visits</small></article>
  <article class="stat-card"><span class="stat-icon green">＄</span><span class="stat-label">Pending bills</span><strong><?= $stats['pending'] ?></strong><small>Awaiting payment</small></article>
</section>
<section class="panel"><div class="panel-heading"><div><span class="eyebrow">SCHEDULE</span><h2>Recent appointments</h2></div><a class="text-link" href="appointments.php">View all →</a></div>
<?php if (!$recentAppointments): ?><div class="empty-state">No appointments found. Add your first appointment from the Appointments page.</div><?php else: ?>
<div class="table-wrap"><table><thead><tr><th>Patient</th><th>Doctor</th><th>Date &amp; time</th><th>Status</th></tr></thead><tbody>
<?php foreach ($recentAppointments as $a): ?><tr><td><strong><?= e($a['patient_name']) ?></strong><small class="cell-sub">Appointment #<?= (int)$a['appt_id'] ?></small></td><td><?= e($a['doctor_name']) ?></td><td><?= e(date('d M Y, h:i A', strtotime($a['appt_date']))) ?></td><td><span class="status status-<?= strtolower(e($a['status'])) ?>"><?= e($a['status']) ?></span></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></section>
<?php require __DIR__ . '/footer.php'; ?>
