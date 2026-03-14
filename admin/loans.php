<?php
$page_title='Manage Loans';require_once __DIR__.'/layout.php';$db=Database::connect();
if($_SERVER['REQUEST_METHOD']==='POST'&&verify_csrf()){
  $act=$_POST['action']??'';$lid=(int)($_POST['loan_id']??0);
  if($act==='close'&&$lid){$db->prepare("UPDATE loans SET status='closed',completed_at=NOW() WHERE id=?")->execute([$lid]);set_flash('success','Loan closed.');}
  elseif($act==='mark_paid'){
    $iid=(int)$_POST['inst_id'];$inst=$db->prepare("SELECT * FROM loan_installments WHERE id=?");$inst->execute([$iid]);$inst=$inst->fetch();
    if($inst){$db->prepare("UPDATE loan_installments SET status='paid',amount_paid=emi,paid_at=NOW() WHERE id=?")->execute([$iid]);
      $db->prepare("UPDATE loans SET amount_paid=amount_paid+?,balance=balance-? WHERE id=?")->execute([$inst['emi'],$inst['emi'],$inst['loan_id']]);
      $rem=$db->prepare("SELECT COUNT(*) FROM loan_installments WHERE loan_id=? AND status!='paid'");$rem->execute([$inst['loan_id']]);
      if($rem->fetchColumn()==0)$db->prepare("UPDATE loans SET status='completed',completed_at=NOW() WHERE id=?")->execute([$inst['loan_id']]);
      set_flash('success','Marked as paid.');}
  }
  header('Location:'.$_SERVER['REQUEST_URI']);exit;
}
$vid=isset($_GET['view'])?(int)$_GET['view']:0;
if($vid):
  $loan=$db->prepare("SELECT l.*,u.full_name,u.email,lt.name as lt FROM loans l JOIN users u ON l.user_id=u.id JOIN loan_types lt ON l.loan_type_id=lt.id WHERE l.id=?");$loan->execute([$vid]);$loan=$loan->fetch();
  if(!$loan){set_flash('error','Not found.');header('Location:/admin/loans.php');exit;}
  $insts=$db->prepare("SELECT * FROM loan_installments WHERE loan_id=? ORDER BY installment_no");$insts->execute([$vid]);$insts=$insts->fetchAll();
?>
<a href="/admin/loans.php" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary mb-6"><i data-lucide="arrow-left" class="w-4 h-4"></i> Back</a>
<div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
  <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div><h2 class="text-xl font-bold font-heading"><?=e($loan['lt'])?> — <?=e($loan['loan_number'])?></h2><p class="text-sm text-gray-400"><?=e($loan['full_name'])?> (<?=e($loan['email'])?>)</p></div>
    <div class="flex items-center gap-2"><?=status_badge($loan['status'])?>
      <?php if($loan['status']==='active'):?><form method="POST" onsubmit="return confirm('Close?')"><?=csrf_field()?><input type="hidden" name="action" value="close"><input type="hidden" name="loan_id" value="<?=$loan['id']?>"><button class="px-3 py-1.5 rounded-lg border border-red-200 text-red-600 text-xs font-bold hover:bg-red-50">Close</button></form><?php endif;?>
      <button onclick="window.print()" class="no-print px-3 py-1.5 rounded-lg border text-xs font-bold hover:bg-gray-50"><i data-lucide="printer" class="w-3.5 h-3.5 inline"></i> Print</button>
    </div>
  </div>
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    <?php foreach([['Principal',fmt_money($loan['principal'])],['Rate',$loan['interest_rate'].'%'],['Term',$loan['term_months'].'m'],['EMI',fmt_money($loan['monthly_payment'])],['Total',fmt_money($loan['total_repayment'])],['Paid',fmt_money($loan['amount_paid'])],['Balance',fmt_money($loan['balance'])],['Disbursed',fmt_date($loan['disbursed_at']??$loan['created_at'])]] as $d):?>
    <div class="p-3 rounded-xl bg-gray-50"><div class="text-[11px] text-gray-400 mb-1"><?=$d[0]?></div><div class="text-sm font-bold"><?=$d[1]?></div></div>
    <?php endforeach;?>
  </div>
</div>
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
  <div class="p-5 border-b border-gray-100"><h3 class="font-bold font-heading">Installments</h3></div>
  <div class="overflow-x-auto"><table class="dtable w-full text-sm"><thead><tr><th class="px-4 py-3 text-left">#</th><th class="px-4 py-3 text-left">Due</th><th class="px-4 py-3 text-right">EMI</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-center">Action</th></tr></thead><tbody>
  <?php foreach($insts as $inst):?>
  <tr class="border-t border-gray-50"><td class="px-4 py-3"><?=$inst['installment_no']?></td><td class="px-4 py-3"><?=fmt_date($inst['due_date'])?></td><td class="px-4 py-3 text-right font-bold"><?=fmt_money($inst['emi'])?></td><td class="px-4 py-3 text-center"><?=status_badge($inst['status'])?></td>
  <td class="px-4 py-3 text-center"><?php if($inst['status']!=='paid'):?>
    <form method="POST" class="inline" onsubmit="return confirm('Mark #<?=$inst['installment_no']?> paid?')"><?=csrf_field()?><input type="hidden" name="action" value="mark_paid"><input type="hidden" name="inst_id" value="<?=$inst['id']?>"><input type="hidden" name="loan_id" value="<?=$loan['id']?>"><button class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-xs font-bold hover:bg-emerald-100">Mark Paid</button></form>
  <?php else:?><span class="text-xs text-gray-400"><?=$inst['paid_at']?fmt_date($inst['paid_at']):'—'?></span><?php endif;?></td></tr>
  <?php endforeach;?></tbody></table></div>
</div>
<?php else:
  $loans=$db->query("SELECT l.*,u.full_name,lt.name as lt FROM loans l JOIN users u ON l.user_id=u.id JOIN loan_types lt ON l.loan_type_id=lt.id ORDER BY l.created_at DESC")->fetchAll();
?>
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden"><div class="overflow-x-auto"><table class="dtable w-full text-sm"><thead><tr><th class="px-4 py-3 text-left">Loan#</th><th class="px-4 py-3 text-left">Borrower</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-right">Principal</th><th class="px-4 py-3 text-right">Balance</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-center">View</th></tr></thead><tbody>
<?php foreach($loans as $l):?>
<tr class="border-t border-gray-50"><td class="px-4 py-3 font-mono text-xs"><?=e($l['loan_number'])?></td><td class="px-4 py-3 font-medium text-sm"><?=e($l['full_name'])?></td><td class="px-4 py-3 text-sm"><?=e($l['lt'])?></td><td class="px-4 py-3 text-right font-bold"><?=fmt_money($l['principal'])?></td><td class="px-4 py-3 text-right"><?=fmt_money($l['balance'])?></td><td class="px-4 py-3 text-center"><?=status_badge($l['status'])?></td>
<td class="px-4 py-3 text-center"><a href="/admin/loans.php?view=<?=$l['id']?>" class="p-1.5 rounded-lg hover:bg-primary/10 text-primary inline-block"><i data-lucide="eye" class="w-4 h-4"></i></a></td></tr>
<?php endforeach;?></tbody></table></div></div>
<?php endif;?>
<?php require_once __DIR__.'/layout_footer.php';?>
