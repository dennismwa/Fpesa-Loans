<?php
$page_title = 'My Applications';
require_once __DIR__ . '/layout.php';
$db = Database::connect();
$apps = $db->prepare("SELECT la.*, lt.name as loan_type FROM loan_applications la JOIN loan_types lt ON la.loan_type_id=lt.id WHERE la.user_id=? ORDER BY la.created_at DESC");
$apps->execute([$_u['id']]); $apps = $apps->fetchAll();
?>

<?php if (empty($apps)): ?>
<div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
  <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-4"><i data-lucide="file-text" class="w-8 h-8 text-primary"></i></div>
  <h3 class="font-bold text-lg mb-2 font-heading">No Applications</h3>
  <p class="text-sm text-gray-400 mb-6">You haven't applied for any loans yet.</p>
  <a href="/pages/apply-loan.php" class="btn-primary inline-flex items-center gap-1 px-6 py-3 rounded-xl text-sm font-bold"><i data-lucide="plus" class="w-4 h-4"></i> Apply Now</a>
</div>
<?php else: ?>
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="dtable w-full text-sm">
      <thead><tr>
        <th class="px-5 py-3 text-left">Reference</th><th class="px-5 py-3 text-left">Loan Type</th><th class="px-5 py-3 text-right">Amount</th><th class="px-5 py-3 text-right">Monthly</th><th class="px-5 py-3 text-center">Term</th><th class="px-5 py-3 text-center">Status</th><th class="px-5 py-3 text-left">Date</th><th class="px-5 py-3 text-center">Actions</th>
      </tr></thead>
      <tbody>
        <?php foreach ($apps as $a): ?>
        <tr class="border-t border-gray-50">
          <td class="px-5 py-3 font-mono text-xs font-medium"><?= e($a['reference']) ?></td>
          <td class="px-5 py-3 font-medium"><?= e($a['loan_type']) ?></td>
          <td class="px-5 py-3 text-right font-bold"><?= fmt_money($a['amount']) ?></td>
          <td class="px-5 py-3 text-right"><?= fmt_money($a['monthly_payment']) ?></td>
          <td class="px-5 py-3 text-center"><?= $a['term_months'] ?>m</td>
          <td class="px-5 py-3 text-center"><?= status_badge($a['status']) ?></td>
          <td class="px-5 py-3 text-xs text-gray-400"><?= fmt_date($a['created_at']) ?></td>
          <td class="px-5 py-3 text-center">
            <?php if ($a['status'] === 'pending' && !$a['fee_paid']): ?>
            <a href="/user/payments.php?pay_fee=<?= $a['id'] ?>" class="btn-primary px-3 py-1.5 rounded-lg text-xs font-bold">Pay Fee</a>
            <?php elseif ($a['admin_comment']): ?>
            <button onclick="alert('<?= addslashes(e($a['admin_comment'])) ?>')" class="text-xs text-primary font-bold hover:underline">View Note</button>
            <?php else: ?><span class="text-xs text-gray-300">—</span><?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
