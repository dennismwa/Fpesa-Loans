<?php
$page_title = 'Wallet';
require_once __DIR__ . '/layout.php';
$db = Database::connect();
$txns = $db->prepare("SELECT * FROM wallet_transactions WHERE user_id=? ORDER BY created_at DESC LIMIT 50");
$txns->execute([$_u['id']]); $txns = $txns->fetchAll();
?>

<div class="bg-primary rounded-3xl p-8 text-white mb-8" data-aos="fade-up">
  <div class="flex items-center justify-between">
    <div>
      <div class="text-sm text-white/60 mb-1">Available Balance</div>
      <div class="text-4xl font-extrabold font-heading"><?= fmt_money($_wbal) ?></div>
      <div class="text-sm text-white/60 mt-2"><?= e($_u['full_name']) ?></div>
    </div>
    <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center"><i data-lucide="wallet" class="w-8 h-8"></i></div>
  </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
  <div class="p-5 border-b border-gray-100"><h3 class="font-bold font-heading">Transaction History</h3></div>
  <?php if (empty($txns)): ?><p class="text-sm text-gray-400 text-center py-12">No transactions yet</p>
  <?php else: ?><div class="divide-y divide-gray-50">
    <?php foreach ($txns as $t): ?>
    <div class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center <?= $t['type']==='credit'?'bg-emerald-100':'bg-red-100' ?>">
          <i data-lucide="<?= $t['type']==='credit'?'arrow-down-left':'arrow-up-right' ?>" class="w-4 h-4 <?= $t['type']==='credit'?'text-emerald-600':'text-red-600' ?>"></i>
        </div>
        <div><div class="text-sm font-medium"><?= e($t['description']) ?></div><div class="text-xs text-gray-400"><?= fmt_datetime($t['created_at']) ?></div></div>
      </div>
      <div class="text-sm font-bold <?= $t['type']==='credit'?'text-emerald-600':'text-red-600' ?>"><?= $t['type']==='credit'?'+':'-' ?><?= fmt_money($t['amount']) ?></div>
    </div>
    <?php endforeach; ?></div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
