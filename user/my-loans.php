<?php
$page_title = 'My Loans';
require_once __DIR__ . '/layout.php';
$db = Database::connect();
$uid = $_u['id'];

$view_id = isset($_GET['view']) ? (int)$_GET['view'] : 0;

if ($view_id):
    $loan = $db->prepare("SELECT l.*, lt.name as loan_type FROM loans l JOIN loan_types lt ON l.loan_type_id=lt.id WHERE l.id=? AND l.user_id=?");
    $loan->execute([$view_id, $uid]); $loan = $loan->fetch();
    if (!$loan) { set_flash('error','Loan not found.'); header('Location: /user/my-loans.php'); exit; }
    $insts = $db->prepare("SELECT * FROM loan_installments WHERE loan_id=? ORDER BY installment_no"); $insts->execute([$loan['id']]); $insts = $insts->fetchAll();
    $progress = $loan['total_repayment'] > 0 ? round(($loan['amount_paid'] / $loan['total_repayment']) * 100) : 0;
?>

<a href="/user/my-loans.php" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary mb-6"><i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Loans</a>

<div class="grid lg:grid-cols-3 gap-6 mb-8">
  <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-6">
      <div><h2 class="text-xl font-bold font-heading"><?= e($loan['loan_type']) ?></h2><p class="text-sm text-gray-400">#<?= e($loan['loan_number']) ?></p></div>
      <div class="flex items-center gap-2"><?= status_badge($loan['status']) ?>
        <button onclick="window.print()" class="no-print p-2 rounded-lg hover:bg-gray-100 text-gray-400" title="Print"><i data-lucide="printer" class="w-4 h-4"></i></button>
      </div>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
      <?php foreach ([['Principal',fmt_money($loan['principal'])],['Rate',$loan['interest_rate'].'%'],['EMI',fmt_money($loan['monthly_payment'])],['Term',$loan['term_months'].'m'],['Total',fmt_money($loan['total_repayment'])],['Paid',fmt_money($loan['amount_paid'])],['Balance',fmt_money($loan['balance'])],['Disbursed',$loan['disbursed_at']?fmt_date($loan['disbursed_at']):'N/A']] as $d): ?>
      <div class="p-3 rounded-xl bg-gray-50"><div class="text-[11px] text-gray-400 mb-1"><?= $d[0] ?></div><div class="text-sm font-bold"><?= $d[1] ?></div></div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="bg-primary rounded-2xl p-6 text-white flex flex-col items-center justify-center">
    <h3 class="font-bold mb-4 font-heading">Repayment Progress</h3>
    <div class="relative w-28 h-28 mb-3">
      <svg class="w-28 h-28 -rotate-90" viewBox="0 0 120 120"><circle cx="60" cy="60" r="50" fill="none" stroke="rgba(255,255,255,.15)" stroke-width="10"/><circle cx="60" cy="60" r="50" fill="none" stroke="white" stroke-width="10" stroke-linecap="round" stroke-dasharray="<?= 2*M_PI*50 ?>" stroke-dashoffset="<?= 2*M_PI*50*(1-$progress/100) ?>"/></svg>
      <div class="absolute inset-0 flex items-center justify-center"><span class="text-2xl font-bold"><?= $progress ?>%</span></div>
    </div>
    <div class="text-sm text-white/70 text-center"><?= fmt_money($loan['amount_paid']) ?> of <?= fmt_money($loan['total_repayment']) ?></div>
  </div>
</div>

<!-- Schedule -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
  <div class="p-5 border-b border-gray-100"><h3 class="font-bold font-heading">Repayment Schedule</h3></div>
  <div class="overflow-x-auto">
    <table class="dtable w-full text-sm">
      <thead><tr><th class="px-5 py-3 text-left">#</th><th class="px-5 py-3 text-left">Due Date</th><th class="px-5 py-3 text-right">Principal</th><th class="px-5 py-3 text-right">Interest</th><th class="px-5 py-3 text-right">EMI</th><th class="px-5 py-3 text-right">Balance</th><th class="px-5 py-3 text-center">Status</th></tr></thead>
      <tbody>
        <?php foreach ($insts as $inst): ?>
        <tr class="border-t border-gray-50">
          <td class="px-5 py-3 font-medium"><?= $inst['installment_no'] ?></td>
          <td class="px-5 py-3"><?= fmt_date($inst['due_date']) ?></td>
          <td class="px-5 py-3 text-right"><?= fmt_money($inst['principal']) ?></td>
          <td class="px-5 py-3 text-right"><?= fmt_money($inst['interest']) ?></td>
          <td class="px-5 py-3 text-right font-semibold"><?= fmt_money($inst['emi']) ?></td>
          <td class="px-5 py-3 text-right"><?= fmt_money($inst['balance']) ?></td>
          <td class="px-5 py-3 text-center"><?= status_badge($inst['status']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php else:
    $loans = $db->prepare("SELECT l.*, lt.name as loan_type FROM loans l JOIN loan_types lt ON l.loan_type_id=lt.id WHERE l.user_id=? ORDER BY l.created_at DESC");
    $loans->execute([$uid]); $loans = $loans->fetchAll();

    if (empty($loans)):
?>
<div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
  <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-4"><i data-lucide="landmark" class="w-8 h-8 text-primary"></i></div>
  <h3 class="font-bold text-lg mb-2 font-heading">No Loans Yet</h3>
  <p class="text-sm text-gray-400 mb-6">Apply for your first loan and get funded fast.</p>
  <a href="/pages/apply-loan.php" class="btn-primary inline-flex items-center gap-1 px-6 py-3 rounded-xl text-sm font-bold"><i data-lucide="plus" class="w-4 h-4"></i> Apply Now</a>
</div>
<?php else: ?>
<div class="grid sm:grid-cols-2 gap-4">
  <?php foreach ($loans as $l):
    $prog = $l['total_repayment'] > 0 ? round(($l['amount_paid'] / $l['total_repayment']) * 100) : 0;
  ?>
  <div class="bg-white rounded-2xl border border-gray-100 p-6 card-lift">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center"><i data-lucide="landmark" class="w-5 h-5 text-primary"></i></div>
        <div><div class="font-bold text-sm"><?= e($l['loan_type']) ?></div><div class="text-xs text-gray-400">#<?= e($l['loan_number']) ?></div></div>
      </div>
      <?= status_badge($l['status']) ?>
    </div>
    <div class="grid grid-cols-3 gap-2 mb-4">
      <div class="p-2 rounded-lg bg-gray-50 text-center"><div class="text-[10px] text-gray-400">Principal</div><div class="text-xs font-bold"><?= fmt_money($l['principal']) ?></div></div>
      <div class="p-2 rounded-lg bg-gray-50 text-center"><div class="text-[10px] text-gray-400">Balance</div><div class="text-xs font-bold"><?= fmt_money($l['balance']) ?></div></div>
      <div class="p-2 rounded-lg bg-gray-50 text-center"><div class="text-[10px] text-gray-400">EMI</div><div class="text-xs font-bold"><?= fmt_money($l['monthly_payment']) ?></div></div>
    </div>
    <div class="w-full bg-gray-100 rounded-full h-2 mb-2"><div class="bg-primary h-2 rounded-full transition-all" style="width:<?= $prog ?>%"></div></div>
    <div class="flex justify-between text-xs text-gray-400 mb-4"><span><?= $prog ?>% repaid</span><span><?= fmt_money($l['amount_paid']) ?> / <?= fmt_money($l['total_repayment']) ?></span></div>
    <a href="/user/my-loans.php?view=<?= $l['id'] ?>" class="block text-center py-2.5 rounded-xl border border-primary/20 text-primary text-sm font-bold hover:bg-primary/5 transition">View Details</a>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; endif; ?>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
