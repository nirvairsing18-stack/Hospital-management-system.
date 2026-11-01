<?php
require __DIR__ . '/config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? ''); $specialization = trim($_POST['specialization'] ?? '');
    $phone = trim($_POST['phone'] ?? ''); $deptId = (int)($_POST['dept_id'] ?? 0);
    if ($name === '' || $specialization === '') $error = 'Doctor name and specialization are required.';
    else {
        $check = $pdo->prepare('SELECT COUNT(*) FROM Department WHERE dept_id=?'); $check->execute([$deptId]);
        if (!$check->fetchColumn()) $error = 'Please choose a valid department.';
        else { $stmt=$pdo->prepare('INSERT INTO Doctor (name,specialization,phone,dept_id) VALUES (?,?,?,?)'); $stmt->execute([$name,$specialization,$phone ?: null,$deptId]); redirect('doctors.php?saved=1'); }
    }
}
$departments = $pdo->query('SELECT * FROM Department ORDER BY dept_name')->fetchAll();
$doctors = $pdo->query('SELECT d.*, dep.dept_name FROM Doctor d LEFT JOIN Department dep ON dep.dept_id=d.dept_id ORDER BY d.doctor_id DESC')->fetchAll();
$pageTitle='Doctors'; $activePage='doctors'; require __DIR__.'/header.php';
?>
<?php if(isset($_GET['saved'])): ?><div class="alert success">Doctor record added successfully.</div><?php endif; ?><?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<div class="content-grid"><section class="panel form-panel"><div class="panel-heading"><div><span class="eyebrow">MEDICAL STAFF</span><h2>Add doctor</h2></div></div><form method="post" class="form-stack"><label>Doctor name<input name="name" maxlength="100" required placeholder="e.g. Dr. A. Sharma" value="<?= e($_POST['name']??'') ?>"></label><label>Specialization<input name="specialization" maxlength="100" required placeholder="e.g. Cardiologist" value="<?= e($_POST['specialization']??'') ?>"></label><label>Phone number<input name="phone" maxlength="20" placeholder="Contact number" value="<?= e($_POST['phone']??'') ?>"></label><label>Department<select name="dept_id" required><?php foreach($departments as $dep): ?><option value="<?= (int)$dep['dept_id'] ?>" <?= ((int)($_POST['dept_id']??0)===(int)$dep['dept_id'])?'selected':'' ?>><?= e($dep['dept_name']) ?></option><?php endforeach; ?></select></label><button class="button" type="submit">Save doctor</button></form></section>
<section class="panel"><div class="panel-heading"><div><span class="eyebrow">DATABASE</span><h2>Doctors <span class="count-pill"><?= count($doctors) ?></span></h2></div></div><div class="table-wrap"><table><thead><tr><th>Doctor</th><th>Specialization</th><th>Department</th><th>Phone</th></tr></thead><tbody><?php foreach($doctors as $d): ?><tr><td><strong><?= e($d['name']) ?></strong><small class="cell-sub">ID #<?= (int)$d['doctor_id'] ?></small></td><td><?= e($d['specialization']) ?></td><td><?= e($d['dept_name']??'Unassigned') ?></td><td><?= e($d['phone']?:'—') ?></td></tr><?php endforeach; ?></tbody></table></div></section></div>
<?php require __DIR__.'/footer.php'; ?>
