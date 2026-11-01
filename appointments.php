<?php
require __DIR__ . '/config.php';
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $patientId=(int)($_POST['patient_id']??0); $doctorId=(int)($_POST['doctor_id']??0);
    $dateInput=trim($_POST['appt_date']??''); $reason=trim($_POST['reason']??'');
    $action=$_POST['action']??'create';
    if ($action==='status') {
        $apptId=(int)($_POST['appt_id']??0); $status=$_POST['status']??'';
        if (!in_array($status,['Booked','Completed','Cancelled'],true)) $error='Invalid appointment status.';
        else { $stmt=$pdo->prepare('UPDATE Appointment SET status=? WHERE appt_id=?'); $stmt->execute([$status,$apptId]); redirect('appointments.php?updated=1'); }
    } else {
        $date = $dateInput ? str_replace('T',' ',$dateInput) . (strlen($dateInput)===16 ? ':00' : '') : '';
        if (!$patientId || !$doctorId || !$date) $error='Patient, doctor, and appointment date/time are required.';
        elseif (strtotime($date) === false || strtotime($date) < time()) $error='Choose a valid date and time in the future.';
        else {
            try { $stmt=$pdo->prepare("INSERT INTO Appointment (patient_id,doctor_id,appt_date,reason) VALUES (?,?,?,?)"); $stmt->execute([$patientId,$doctorId,date('Y-m-d H:i:s',strtotime($date)),$reason?:null]); redirect('appointments.php?saved=1'); }
            catch(PDOException $ex) { if ($ex->getCode()==='23000') $error='That doctor already has an appointment at this exact time, or a selected record is invalid.'; else $error='Could not save the appointment. Check the database settings.'; }
        }
    }
}
$patients=$pdo->query('SELECT patient_id,name FROM Patient ORDER BY name')->fetchAll();
$doctors=$pdo->query('SELECT doctor_id,name,specialization FROM Doctor ORDER BY name')->fetchAll();
$appointments=$pdo->query('SELECT a.*,p.name AS patient_name,d.name AS doctor_name FROM Appointment a JOIN Patient p ON p.patient_id=a.patient_id JOIN Doctor d ON d.doctor_id=a.doctor_id ORDER BY a.appt_date DESC')->fetchAll();
$pageTitle='Appointments'; $activePage='appointments'; require __DIR__.'/header.php';
?>
<?php if(isset($_GET['saved'])): ?><div class="alert success">Appointment booked successfully.</div><?php endif; ?><?php if(isset($_GET['updated'])): ?><div class="alert success">Appointment status updated.</div><?php endif; ?><?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<div class="content-grid"><section class="panel form-panel"><div class="panel-heading"><div><span class="eyebrow">SCHEDULING</span><h2>Book appointment</h2></div></div><?php if(!$patients||!$doctors): ?><div class="empty-state">Add at least one patient and one doctor before booking an appointment.</div><?php else: ?><form method="post" class="form-stack"><input type="hidden" name="action" value="create"><label>Patient<select name="patient_id" required><?php foreach($patients as $p): ?><option value="<?= (int)$p['patient_id'] ?>"><?= e($p['name']) ?></option><?php endforeach; ?></select></label><label>Doctor<select name="doctor_id" required><?php foreach($doctors as $d): ?><option value="<?= (int)$d['doctor_id'] ?>"><?= e($d['name'].' — '.$d['specialization']) ?></option><?php endforeach; ?></select></label><label>Date and time<input type="datetime-local" name="appt_date" required value="<?= e($_POST['appt_date']??'') ?>"></label><label>Reason for visit<textarea name="reason" rows="3" maxlength="255" placeholder="Optional notes"><?= e($_POST['reason']??'') ?></textarea></label><button class="button" type="submit">Book appointment</button></form><?php endif; ?></section>
<section class="panel"><div class="panel-heading"><div><span class="eyebrow">SCHEDULE</span><h2>All appointments <span class="count-pill"><?= count($appointments) ?></span></h2></div></div><div class="table-wrap"><table><thead><tr><th>Patient / doctor</th><th>Date &amp; time</th><th>Status</th><th>Update status</th></tr></thead><tbody><?php foreach($appointments as $a): ?><tr><td><strong><?= e($a['patient_name']) ?></strong><small class="cell-sub"><?= e($a['doctor_name']) ?> · #<?= (int)$a['appt_id'] ?></small></td><td><?= e(date('d M Y, h:i A',strtotime($a['appt_date']))) ?></td><td><span class="status status-<?= strtolower(e($a['status'])) ?>"><?= e($a['status']) ?></span></td><td><form method="post" class="inline-form"><input type="hidden" name="action" value="status"><input type="hidden" name="appt_id" value="<?= (int)$a['appt_id'] ?>"><select name="status" aria-label="Status for appointment <?= (int)$a['appt_id'] ?>"><?php foreach(['Booked','Completed','Cancelled'] as $s): ?><option <?= $a['status']===$s?'selected':'' ?>><?= e($s) ?></option><?php endforeach; ?></select><button class="button button-small" type="submit">Save</button></form></td></tr><?php endforeach; ?></tbody></table></div><p class="helper-text">Mark an appointment Completed to automatically create its pending bill (₹500) using the database trigger.</p></section></div>
<?php require __DIR__.'/footer.php'; ?>
