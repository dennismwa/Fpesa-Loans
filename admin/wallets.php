<?php
$page_title='Wallet Management';require_once __DIR__.'/layout.php';$db=Database::connect();
if($_SERVER['REQUEST_METHOD']==='POST'&&verify_csrf()){
  $uid=(int)($_POST['user_id']??0);$type=$_POST['txn_type']??'credit';$amt=(float)($_POST['amount']??0);$desc=trim($_POST['description']??'Admin adjustment');
  if($uid&&$amt>0){wallet_txn($uid,$type,$amt,$desc);set_flash('success',ucfirst($type).' '.fmt_money($amt));}
  header('Location:/admin/wallets.php');exit;
}
$wallets=$db->query("SELECT w.*,u.full_name,u.email,u.phone FROM wallets w JOIN users u ON w.user_id=u.id ORDER BY w.balance DESC")->fetchAll();
?>
<div id="adjM" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center hidden"><div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
  <h3 class="font-bold text-lg mb-4 font-heading">Adjust Balance</h3>
  <form method="POST" class="space-y-4"><?=csrf_field()?><input type="hidden" name="user_id" id="aUID">
    <p class="text-sm text-gray-500">User: <strong id="aUN"></strong></p>
    <div><label class="text-sm font-semibold mb-1 block">Type</label><select name="txn_type" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"><option value="credit">Credit (Add)</option><option value="debit">Debit (Subtract)</option></select></div>
    <div><label class="text-sm font-semibold mb-1 block">Amount</label><input type="number" name="amount" step="0.01" min="1" required class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
    <div><label class="text-sm font-semibold mb-1 block">Description</label><input type="text" name="description" value="Admin adjustment" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
    <div class="flex gap-3"><button class="btn-primary flex-1 py-2.5 rounded-xl text-sm font-bold">Submit</button><button type="button" onclick="document.getElementById('adjM').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border text-sm font-bold hover:bg-gray-50">Cancel</button></div>
  </form></div></div>
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden"><div class="overflow-x-auto"><table class="dtable w-full text-sm"><thead><tr><th class="px-4 py-3 text-left">User</th><th class="px-4 py-3 text-left">Email</th><th class="px-4 py-3 text-right">Balance</th><th class="px-4 py-3 text-center">Action</th></tr></thead><tbody>
<?php foreach($wallets as $w):?>
<tr class="border-t border-gray-50"><td class="px-4 py-3 font-medium"><?=e($w['full_name'])?></td><td class="px-4 py-3 text-xs text-gray-400"><?=e($w['email'])?></td><td class="px-4 py-3 text-right font-bold <?=$w['balance']>0?'text-emerald-600':'text-gray-400'?>"><?=fmt_money($w['balance'])?></td>
<td class="px-4 py-3 text-center"><button onclick="document.getElementById('aUID').value=<?=$w['user_id']?>;document.getElementById('aUN').textContent='<?=addslashes(e($w['full_name']))?>';document.getElementById('adjM').classList.remove('hidden')" class="px-3 py-1.5 rounded-lg bg-primary/10 text-primary text-xs font-bold hover:bg-primary/20">Adjust</button></td></tr>
<?php endforeach;?></tbody></table></div></div>
<?php require_once __DIR__.'/layout_footer.php';?>
