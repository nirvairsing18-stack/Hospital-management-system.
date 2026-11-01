<?php
require __DIR__ . '/config.php';
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $billId=(int)($_POST['bill_id']??0);
    $stmt=$pdo->prepare("UPDATE Bill SET pay_status='Paid', paid_at=NOW() WHERE bill_id=? AND pay_status='Pending'");
    $stmt->execute([$billId]);
    redirect('bills.php?paid=1');
}
$bills=$pdo->query("SELECT b.*,a.appt_date,p.name AS patient_name,d.name AS doctor_name FROM Bill b JOIN Appointment a ON a.appt_id=b.appt_id JOIN Patient p ON p.patient_id=a.patient_id JOIN Doctor d ON d.doctor_id=a.doctor_id ORDER BY b.created_at DESC")->fetchAll();
$pendingTotal=(float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM Bill WHERE pay_status='Pending'")->fetchColumn();
$pageTitle='Billing'; $activePage='bills'; require __DIR__.'/header.php';
?>
<?php if(isset($_GET['paid'])): ?><div class="alert success">Bill payment status updated.</div><?php endif; ?>
<section class="billing-summary"><div><span class="eyebrow">OUTSTANDING AMOUNT</span><strong>₹<?= number_format($pendingTotal,2) ?></strong><p>Total amount currently pending</p></div><div class="billing-symbol">＄</div></section>
<section class="panel"><div class="panel-heading"><div><span class="eyebrow">PAYMENTS</span><h2>Bill records <span class="count-pill"><?= count($bills) ?></span></h2></div></div><?php if(!$bills): ?><div class="empty-state">No bills yet. When an appointment is marked Completed, the database trigger creates a bill automatically.</div><?php else: ?><div class="table-wrap"><table><thead><tr><th>Bill</th><th>Patient / doctor</th><th>Appointment date</th><th>Amount</th><th>Payment status</th><th>Action</th></tr></thead><tbody><?php foreach($bills as $b): ?><tr><td><strong>#<?= (int)$b['bill_id'] ?></strong><small class="cell-sub">Appointment #<?= (int)$b['appt_id'] ?></small></td><td><strong><?= e($b['patient_name']) ?></strong><small class="cell-sub"><?= e($b['doctor_name']) ?></small></td><td><?= e(date('d M Y',strtotime($b['appt_date']))) ?></td><td>₹<?= number_format((float)$b['amount'],2) ?></td><td><span class="status status-<?= strtolower(e($b['pay_status'])) ?>"><?= e($b['pay_status']) ?></span></td><td><?php if($b['pay_status']==='Pending'): ?><form method="post"><input type="hidden" name="bill_id" value="<?= (int)$b['bill_id'] ?>"><button class="button button-small" type="submit">Mark paid</button></form><?php else: ?><span class="muted">Paid <?= $b['paid_at'] ? e(date('d M Y',strtotime($b['paid_at']))) : '' ?></span><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require __DIR__.'/footer.php'; ?>
