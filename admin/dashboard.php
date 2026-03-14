<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/layout.php';
$db = Database::connect();

$s = [
  ['Total Users', $db->query("SELECT COUNT(*) FROM users")->fetchColumn(), 'users', 'from-blue-500 to-cyan-400'],
  ['Active Loans', $db->query("SELECT COUNT(*) FROM loans WHERE status='active'")->fetchColumn(), 'landmark', 'from-primary to-emerald-400'],
  ['Pending Apps', $db->query("SELECT COUNT(*) FROM loan_applications WHERE status IN ('pending','fee_paid')")->fetchColumn(), 'clock', 'from-amber-500 to-orange-400'],
  ['Total Disbursed', fmt_money($db->query("SELECT COALESCE(SUM(principal),0) FROM loans")->fetchColumn()), 'banknote', 'from-purple-500 to-violet-400'],
  ['Total Repaid', fmt_money($db->query("SELECT COALESCE(SUM(amount_paid),0) FROM loans")->fetchColumn()), 'check-circle', 'from-emerald-500 to-green-400'],
  ['Fee Revenue', fmt_money($db->query("SELECT COALESCE(SUM(fee_amount),0) FROM loan_applications WHERE fee_paid=1")->fetchColumn()), 'trending-up', 'from-secondary to-amber-400'],
];

$recApps = $db->query("SELECT la.*, u.full_name, lt.name as loan_type FROM loan_applications la JOIN users u ON la.user_id=u.id JOIN loan_types lt ON la.loan_type_id=lt.id ORDER BY la.created_at DESC LIMIT 8")->fetchAll();
$recPay = $db->query("SELECT p.*, u.full_name FROM payments p JOIN users u ON p.user_id=u.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll();
?>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
<?php foreach ($s as $i=>$st): ?>
<div class="bg-white rounded-2xl border border-gray-100 p-5 card-lift">
  <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center mb-3"><i data-lucide="<?= $st[2] ?>" class="w-5 h-5 text-white"></i></div>
  <div class="text-xl font-bold font-heading"><?= $st[1] ?></div>
  <div class="text-xs text-gray-400 mt-1"><?= $st[0] ?></div>
</div>
<?php endforeach; ?>
</div>

<div class="grid lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between"><h3 class="font-bold font-heading">Recent Applications</h3><a href="/admin/applications.php" class="text-xs text-primary font-bold">View All</a></div>
    <div class="overflow-x-auto">
      <table class="dtable w-full text-sm"><thead><tr><th class="px-4 py-3 text-left">Applicant</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-right">Amount</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-center">Action</th></tr></thead>
      <tbody><?php foreach ($recApps as $r): ?>
      <tr class="border-t border-gray-50">
        <td class="px-4 py-3"><div class="font-medium text-sm"><?= e($r['full_name']) ?></div><div class="text-[11px] text-gray-400"><?= e($r['reference']) ?></div></td>
        <td class="px-4 py-3 text-sm"><?= e($r['loan_type']) ?></td>
        <td class="px-4 py-3 text-right font-bold text-sm"><?= fmt_money($r['amount']) ?></td>
        <td class="px-4 py-3 text-center"><?= status_badge($r['status']) ?></td>
        <td class="px-4 py-3 text-center"><a href="/admin/applications.php?view=<?= $r['id'] ?>" class="text-xs text-primary font-bold hover:underline">Review</a></td>
      </tr>
      <?php endforeach; ?></tbody></table>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-gray-100 p-5">
    <div class="flex items-center justify-between mb-4"><h3 class="font-bold font-heading text-sm">Recent Payments</h3><a href="/admin/payments.php" class="text-xs text-primary font-bold">All</a></div>
    <div class="space-y-3"><?php foreach ($recPay as $p): ?>
    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50">
      <div><div class="text-sm font-medium"><?= e($p['full_name']) ?></div><div class="text-[11px] text-gray-400"><?= time_ago($p['created_at']) ?></div></div>
      <div class="text-right"><div class="text-sm font-bold"><?= fmt_money($p['amount']) ?></div><?= status_badge($p['status']) ?></div>
    </div>
    <?php endforeach; ?></div>
  </div>
</div>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
