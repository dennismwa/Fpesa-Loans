<?php
$page_title = 'Payments';
require_once __DIR__ . '/layout.php';
$db = Database::connect();
$uid = $_u['id'];

$pay_fee = isset($_GET['pay_fee']) ? (int)$_GET['pay_fee'] : 0;

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $ptype = $_POST['payment_type'] ?? '';
    $method = e($_POST['payment_method'] ?? '');
    $txn_ref = e($_POST['transaction_ref'] ?? '');
    $amount = (float)($_POST['amount'] ?? 0);
    $app_id = (int)($_POST['application_id'] ?? 0);
    $loan_id = (int)($_POST['loan_id'] ?? 0);
    $inst_id = (int)($_POST['installment_id'] ?? 0);

    $screenshot = null;
    if (!empty($_FILES['screenshot']['name'])) {
        $screenshot = upload_file($_FILES['screenshot'], __DIR__ . '/../uploads/documents', ['jpg','jpeg','png','pdf']);
    }

    $ref = gen_ref('PAY');
    $db->prepare("INSERT INTO payments (reference,user_id,loan_id,installment_id,application_id,amount,payment_method,payment_type,transaction_ref,screenshot,status,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,'pending',NOW())")
        ->execute([$ref, $uid, $loan_id?:null, $inst_id?:null, $app_id?:null, $amount, $method, $ptype, $txn_ref, $screenshot]);

    if ($ptype === 'application_fee' && $app_id) {
        $db->prepare("UPDATE loan_applications SET fee_paid=1, fee_payment_ref=?, status='fee_paid' WHERE id=? AND user_id=?")->execute([$ref, $app_id, $uid]);
    }

    add_log('payment_submit', "Payment $ref - $amount", $uid);
    set_flash('success', 'Payment submitted! Reference: ' . $ref);
    header('Location: /user/payments.php');
    exit;
}

$methods = $db->query("SELECT * FROM payment_methods WHERE is_active=1 ORDER BY sort_order")->fetchAll();

// Fee application
$fee_app = null;
if ($pay_fee) {
    $st = $db->prepare("SELECT la.*, lt.name as loan_type FROM loan_applications la JOIN loan_types lt ON la.loan_type_id=lt.id WHERE la.id=? AND la.user_id=? AND la.fee_paid=0");
    $st->execute([$pay_fee, $uid]); $fee_app = $st->fetch();
}

$payments = $db->prepare("SELECT * FROM payments WHERE user_id=? ORDER BY created_at DESC LIMIT 50");
$payments->execute([$uid]); $payments = $payments->fetchAll();
?>

<?php if ($fee_app): ?>
<div class="bg-white rounded-2xl border border-gray-100 p-6 mb-8" data-aos="fade-up">
  <h3 class="font-bold text-lg mb-4 font-heading">Pay Application Fee</h3>
  <div class="bg-primary/5 rounded-xl p-4 mb-6">
    <p class="text-sm"><strong><?= e($fee_app['loan_type']) ?></strong> — <?= e($fee_app['reference']) ?></p>
    <p class="text-sm mt-1">Fee Amount: <strong class="text-primary text-lg"><?= fmt_money($fee_app['fee_amount']) ?></strong></p>
  </div>

  <?php if (!empty($methods)): ?>
  <h4 class="font-semibold text-sm mb-3">Select Payment Method:</h4>
  <div class="grid sm:grid-cols-2 gap-3 mb-4">
    <?php foreach ($methods as $m): ?>
    <div class="p-4 rounded-xl border border-gray-200 hover:border-primary/50 transition cursor-pointer" onclick="showMethod(<?= $m['id'] ?>)">
      <div class="font-semibold text-sm"><?= e($m['name']) ?></div>
      <div class="text-xs text-gray-400 capitalize"><?= str_replace('_',' ',$m['type']) ?></div>
      <?php if ($m['account_number']): ?><div class="text-xs font-mono mt-1 text-primary"><?= e($m['account_number']) ?></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php foreach ($methods as $m): ?>
  <div id="md<?= $m['id'] ?>" class="hidden bg-gray-50 rounded-xl p-4 mb-4 text-sm">
    <?php if ($m['account_number']): ?><p><strong>Number:</strong> <?= e($m['account_number']) ?></p><?php endif; ?>
    <?php if ($m['account_name']): ?><p><strong>Name:</strong> <?= e($m['account_name']) ?></p><?php endif; ?>
    <?php if ($m['instructions']): ?><p class="mt-1 text-gray-600 whitespace-pre-line"><?= e($m['instructions']) ?></p><?php endif; ?>
  </div>
  <?php endforeach; endif; ?>

  <form method="POST" enctype="multipart/form-data" class="space-y-4 mt-4">
    <?= csrf_field() ?>
    <input type="hidden" name="payment_type" value="application_fee">
    <input type="hidden" name="application_id" value="<?= $fee_app['id'] ?>">
    <input type="hidden" name="amount" value="<?= $fee_app['fee_amount'] ?>">
    <div class="grid sm:grid-cols-2 gap-4">
      <div><label class="text-sm font-semibold mb-1.5 block">Payment Method</label>
        <select name="payment_method" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm">
          <?php foreach ($methods as $m): ?><option value="<?= e($m['name']) ?>"><?= e($m['name']) ?></option><?php endforeach; ?>
        </select></div>
      <div><label class="text-sm font-semibold mb-1.5 block">M-Pesa / Transaction Code</label>
        <input type="text" name="transaction_ref" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm" placeholder="e.g. SJ12ABC456"></div>
    </div>
    <div><label class="text-sm font-semibold mb-1.5 block">Payment Screenshot (optional)</label>
      <input type="file" name="screenshot" accept="image/*,.pdf" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
    <button type="submit" class="btn-primary px-6 py-3 rounded-xl text-sm font-bold">Submit Payment</button>
  </form>
</div>
<script>
function showMethod(id){document.querySelectorAll('[id^=md]').forEach(e=>e.classList.add('hidden'));var el=document.getElementById('md'+id);if(el)el.classList.remove('hidden')}
</script>
<?php endif; ?>

<!-- Payment History -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden" data-aos="fade-up">
  <div class="p-5 border-b border-gray-100"><h3 class="font-bold font-heading">Payment History</h3></div>
  <?php if (empty($payments)): ?><p class="text-sm text-gray-400 text-center py-12">No payments yet</p>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="dtable w-full text-sm">
      <thead><tr><th class="px-5 py-3 text-left">Reference</th><th class="px-5 py-3 text-left">Type</th><th class="px-5 py-3 text-left">Method</th><th class="px-5 py-3 text-right">Amount</th><th class="px-5 py-3 text-center">Status</th><th class="px-5 py-3 text-left">Date</th></tr></thead>
      <tbody>
        <?php foreach ($payments as $p): ?>
        <tr class="border-t border-gray-50">
          <td class="px-5 py-3 font-mono text-xs"><?= e($p['reference']) ?></td>
          <td class="px-5 py-3 capitalize text-xs"><?= str_replace('_',' ',$p['payment_type']) ?></td>
          <td class="px-5 py-3 text-xs"><?= e($p['payment_method']) ?></td>
          <td class="px-5 py-3 text-right font-bold"><?= fmt_money($p['amount']) ?></td>
          <td class="px-5 py-3 text-center"><?= status_badge($p['status']) ?></td>
          <td class="px-5 py-3 text-xs text-gray-400"><?= fmt_datetime($p['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
