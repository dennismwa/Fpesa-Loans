<?php
$page_title='Manage Payments';require_once __DIR__.'/layout.php';$db=Database::connect();
if($_SERVER['REQUEST_METHOD']==='POST'&&verify_csrf()){
  $act=$_POST['action']??'';$pid=(int)($_POST['payment_id']??0);
  if(in_array($act,['confirm','reject'])&&$pid){
    $st=$act==='confirm'?'confirmed':'rejected';
    $db->prepare("UPDATE payments SET status=?,confirmed_by=?,confirmed_at=NOW() WHERE id=?")->execute([$st,$_adm['id'],$pid]);
    if($act==='confirm'){$pay=$db->prepare("SELECT * FROM payments WHERE id=?");$pay->execute([$pid]);$pay=$pay->fetch();
      if($pay&&$pay['payment_type']==='repayment'&&$pay['installment_id']){$db->prepare("UPDATE loan_installments SET status='paid',amount_paid=emi,paid_at=NOW() WHERE id=?")->execute([$pay['installment_id']]);if($pay['loan_id'])$db->prepare("UPDATE loans SET amount_paid=amount_paid+?,balance=balance-? WHERE id=?")->execute([$pay['amount'],$pay['amount'],$pay['loan_id']]);}
      if($pay)notify($pay['user_id'],'Payment Confirmed','Payment '.fmt_money($pay['amount']).' confirmed.','success');
    }set_flash('success','Payment '.$st.'.');
  }header('Location:/admin/payments.php');exit;
}
$sf=$_GET['status']??'';$w=$sf?"WHERE p.status=?":"";$prm=$sf?[$sf]:[];
$tot=$db->prepare("SELECT COUNT(*) FROM payments p $w");$tot->execute($prm);$tot=$tot->fetchColumn();
$pg=paginate($tot,20,(int)($_GET['page']??1));
$rows=$db->prepare("SELECT p.*,u.full_name FROM payments p JOIN users u ON p.user_id=u.id $w ORDER BY p.created_at DESC LIMIT {$pg['per']} OFFSET {$pg['offset']}");$rows->execute($prm);$pays=$rows->fetchAll();
?>
<div class="flex flex-wrap gap-2 mb-6"><?php foreach(['=>All','pending'=>'Pending','confirmed'=>'Confirmed','rejected'=>'Rejected'] as $k=>$v):$k=($k==='=>All')?'':$k;?>
<a href="/admin/payments.php?status=<?=$k?>" class="px-4 py-2 rounded-xl text-sm font-medium border <?=$sf===$k?'bg-primary text-white border-primary':'bg-white border-gray-200 hover:border-primary'?> transition"><?=$v?></a><?php endforeach;?></div>
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden"><div class="overflow-x-auto"><table class="dtable w-full text-sm"><thead><tr><th class="px-4 py-3 text-left">Ref</th><th class="px-4 py-3 text-left">User</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-left">Method</th><th class="px-4 py-3 text-right">Amount</th><th class="px-4 py-3 text-left">Txn</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-left">Date</th><th class="px-4 py-3 text-center">Actions</th></tr></thead><tbody>
<?php foreach($pays as $p):?>
<tr class="border-t border-gray-50"><td class="px-4 py-3 font-mono text-xs"><?=e($p['reference'])?></td><td class="px-4 py-3 text-sm font-medium"><?=e($p['full_name'])?></td><td class="px-4 py-3 text-xs capitalize"><?=str_replace('_',' ',$p['payment_type'])?></td><td class="px-4 py-3 text-xs"><?=e($p['payment_method'])?></td><td class="px-4 py-3 text-right font-bold"><?=fmt_money($p['amount'])?></td><td class="px-4 py-3 text-xs"><?=e($p['transaction_ref']?:'—')?></td><td class="px-4 py-3 text-center"><?=status_badge($p['status'])?></td><td class="px-4 py-3 text-xs text-gray-400"><?=fmt_datetime($p['created_at'])?></td>
<td class="px-4 py-3 text-center"><div class="flex items-center justify-center gap-1">
  <?php if($p['screenshot']):?><a href="/uploads/documents/<?=e($p['screenshot'])?>" target="_blank" class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-500" title="Screenshot"><i data-lucide="image" class="w-4 h-4"></i></a><?php endif;?>
  <?php if($p['status']==='pending'):?>
  <form method="POST" class="inline" onsubmit="return confirm('Confirm?')"><?=csrf_field()?><input type="hidden" name="action" value="confirm"><input type="hidden" name="payment_id" value="<?=$p['id']?>"><button class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-500"><i data-lucide="check-circle" class="w-4 h-4"></i></button></form>
  <form method="POST" class="inline" onsubmit="return confirm('Reject?')"><?=csrf_field()?><input type="hidden" name="action" value="reject"><input type="hidden" name="payment_id" value="<?=$p['id']?>"><button class="p-1.5 rounded-lg hover:bg-red-50 text-red-500"><i data-lucide="x-circle" class="w-4 h-4"></i></button></form>
  <?php endif;?></div></td></tr>
<?php endforeach;?></tbody></table></div></div>
<?=render_pagination($pg,'/admin/payments.php'.($sf?"?status=$sf":''))?>
<?php require_once __DIR__.'/layout_footer.php';?>
