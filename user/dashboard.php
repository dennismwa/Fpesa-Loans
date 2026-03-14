<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/layout.php';
$db = Database::connect();
$uid = $_u['id'];

$active_loans = $db->prepare("SELECT COUNT(*) FROM loans WHERE user_id=? AND status='active'"); $active_loans->execute([$uid]); $active_loans = $active_loans->fetchColumn();
$total_borrowed = $db->prepare("SELECT COALESCE(SUM(principal),0) FROM loans WHERE user_id=?"); $total_borrowed->execute([$uid]); $total_borrowed = $total_borrowed->fetchColumn();
$total_paid = $db->prepare("SELECT COALESCE(SUM(amount_paid),0) FROM loans WHERE user_id=?"); $total_paid->execute([$uid]); $total_paid = $total_paid->fetchColumn();

$upcoming = $db->prepare("SELECT li.*, l.loan_number, lt.name as loan_type FROM loan_installments li JOIN loans l ON li.loan_id=l.id JOIN loan_types lt ON l.loan_type_id=lt.id WHERE li.user_id=? AND li.status='pending' ORDER BY li.due_date LIMIT 5");
$upcoming->execute([$uid]); $upcoming = $upcoming->fetchAll();

$recent_apps = $db->prepare("SELECT la.*, lt.name as loan_type FROM loan_applications la JOIN loan_types lt ON la.loan_type_id=lt.id WHERE la.user_id=? ORDER BY la.created_at DESC LIMIT 5");
$recent_apps->execute([$uid]); $recent_apps = $recent_apps->fetchAll();
?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
<?php
$stats = [
  ['Active Loans', $active_loans, 'landmark', 'from-primary to-emerald-400'],
  ['Total Borrowed', fmt_money($total_borrowed), 'trending-up', 'from-blue-500 to-cyan-400'],
  ['Total Repaid', fmt_money($total_paid), 'check-circle', 'from-emerald-500 to-green-400'],
  ['Wallet', fmt_money($_wbal), 'wallet', 'from-secondary to-amber-400'],
];
foreach ($stats as $i => $s): ?>
<div class="bg-white rounded-2xl border border-gray-100 p-5 card-lift" data-aos="fade-up" data-aos-delay="<?= $i*80 ?>">
  <div class="w-10 h-10 rounded-xl bg-gradient-to-br <?= $s[3] ?> flex items-center justify-center mb-3"><i data-lucide="<?= $s[2] ?>" class="w-5 h-5 text-white"></i></div>
  <div class="text-xl font-bold font-heading"><?= $s[1] ?></div>
  <div class="text-xs text-gray-400 mt-1"><?= $s[0] ?></div>
</div>
<?php endforeach; ?>
</div>

<div class="grid lg:grid-cols-2 gap-6">
  <!-- Upcoming Payments -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6" data-aos="fade-up">
    <div class="flex items-center justify-between mb-5"><h3 class="font-bold font-heading">Upcoming Payments</h3><a href="/user/my-loans.php" class="text-xs text-primary font-bold">View All</a></div>
    <?php if (empty($upcoming)): ?><p class="text-sm text-gray-400 text-center py-8">No upcoming payments</p>
    <?php else: ?><div class="space-y-3">
    <?php foreach ($upcoming as $inst): ?>
    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-primary/[.03] transition">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center"><i data-lucide="calendar" class="w-4 h-4 text-primary"></i></div>
        <div><div class="text-sm font-semibold"><?= e($inst['loan_type']) ?></div><div class="text-xs text-gray-400">Due <?= fmt_date($inst['due_date']) ?></div></div>
      </div>
      <div class="text-right"><div class="text-sm font-bold"><?= fmt_money($inst['emi']) ?></div><?= status_badge($inst['status']) ?></div>
    </div>
    <?php endforeach; ?></div><?php endif; ?>
  </div>

  <!-- Recent Applications -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6" data-aos="fade-up" data-aos-delay="100">
    <div class="flex items-center justify-between mb-5"><h3 class="font-bold font-heading">Recent Applications</h3><a href="/user/loan-status.php" class="text-xs text-primary font-bold">View All</a></div>
    <?php if (empty($recent_apps)): ?>
    <div class="text-center py-8"><p class="text-sm text-gray-400 mb-3">No applications yet</p><a href="/pages/apply-loan.php" class="btn-primary inline-flex items-center gap-1 px-4 py-2 rounded-lg text-xs font-bold"><i data-lucide="plus" class="w-3.5 h-3.5"></i> Apply Now</a></div>
    <?php else: ?><div class="space-y-3">
    <?php foreach ($recent_apps as $app): ?>
    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-primary/[.03] transition">
      <div><div class="text-sm font-semibold"><?= e($app['loan_type']) ?></div><div class="text-xs text-gray-400"><?= e($app['reference']) ?> &bull; <?= fmt_date($app['created_at']) ?></div></div>
      <div class="text-right"><div class="text-sm font-bold"><?= fmt_money($app['amount']) ?></div><?= status_badge($app['status']) ?></div>
    </div>
    <?php endforeach; ?></div><?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
