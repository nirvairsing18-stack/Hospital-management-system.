<?php
require __DIR__ . '/config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $ageRaw = trim($_POST['age'] ?? '');
    $gender = $_POST['gender'] ?? 'Other';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    if ($name === '') $error = 'Patient name is required.';
    elseif ($ageRaw !== '' && (!ctype_digit($ageRaw) || (int)$ageRaw < 1 || (int)$ageRaw > 120)) $error = 'Age must be between 1 and 120.';
    elseif (!in_array($gender, ['Male','Female','Other'], true)) $error = 'Choose a valid gender.';
    else {
        $stmt = $pdo->prepare('INSERT INTO Patient (name, age, gender, phone, address) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $ageRaw === '' ? null : (int)$ageRaw, $gender, $phone ?: null, $address ?: null]);
        redirect('patients.php?saved=1');
    }
}
$patients = $pdo->query('SELECT * FROM Patient ORDER BY patient_id DESC')->fetchAll();
$pageTitle = 'Patients'; $activePage = 'patients'; require __DIR__ . '/header.php';
?>
<?php if (isset($_GET['saved'])): ?><div class="alert success">Patient record added successfully.</div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<div class="content-grid"><section class="panel form-panel"><div class="panel-heading"><div><span class="eyebrow">PATIENT RECORDS</span><h2>Add patient</h2></div></div>
<form method="post" class="form-stack"><label>Full name<input name="name" maxlength="100" required value="<?= e($_POST['name'] ?? '') ?>" placeholder="Enter patient's name"></label><div class="form-row"><label>Age<input type="number" name="age" min="1" max="120" value="<?= e($_POST['age'] ?? '') ?>" placeholder="e.g. 34"></label><label>Gender<select name="gender"><option>Other</option><option <?= (($_POST['gender'] ?? '')==='Male')?'selected':'' ?>>Male</option><option <?= (($_POST['gender'] ?? '')==='Female')?'selected':'' ?>>Female</option></select></label></div><label>Phone number<input name="phone" maxlength="20" value="<?= e($_POST['phone'] ?? '') ?>" placeholder="Contact number"></label><label>Address<textarea name="address" rows="3" maxlength="255" placeholder="City or address"><?= e($_POST['address'] ?? '') ?></textarea></label><button class="button" type="submit">Save patient</button></form></section>
<section class="panel"><div class="panel-heading"><div><span class="eyebrow">DATABASE</span><h2>Registered patients <span class="count-pill"><?= count($patients) ?></span></h2></div></div><div class="table-wrap"><table><thead><tr><th>Name</th><th>Age / gender</th><th>Phone</th><th>Address</th></tr></thead><tbody><?php foreach($patients as $p): ?><tr><td><strong><?= e($p['name']) ?></strong><small class="cell-sub">ID #<?= (int)$p['patient_id'] ?></small></td><td><?= e($p['age'] ?? '—') ?> / <?= e($p['gender']) ?></td><td><?= e($p['phone'] ?: '—') ?></td><td><?= e($p['address'] ?: '—') ?></td></tr><?php endforeach; ?></tbody></table></div></section></div>
<?php require __DIR__ . '/footer.php'; ?>
