<?php
$page_title='Loan Applications';
require_once __DIR__.'/layout.php';
$db=Database::connect();

if($_SERVER['REQUEST_METHOD']==='POST'&&verify_csrf()){
  $act=$_POST['action']??'';$aid=(int)($_POST['app_id']??0);$cmt=trim($_POST['admin_comment']??'');
  if($act==='approve'&&$aid){
    $app=$db->prepare("SELECT * FROM loan_applications WHERE id=?");$app->execute([$aid]);$app=$app->fetch();
    if($app&&$app['status']!=='approved'){
      $db->prepare("UPDATE loan_applications SET status='approved',admin_comment=?,reviewed_by=?,reviewed_at=NOW() WHERE id=?")->execute([$cmt,$_adm['id'],$aid]);
      $ln=gen_ref('LN');
      $db->prepare("INSERT INTO loans (loan_number,application_id,user_id,loan_type_id,principal,interest_rate,term_months,monthly_payment,total_repayment,total_interest,balance,status,disbursed_at,created_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,'active',NOW(),NOW())")
        ->execute([$ln,$aid,$app['user_id'],$app['loan_type_id'],$app['amount'],$app['interest_rate'],$app['term_months'],$app['monthly_payment'],$app['total_repayment'],$app['total_interest'],$app['total_repayment']]);
      $lid=$db->lastInsertId();
      $sch=generate_schedule($app['amount'],$app['interest_rate'],$app['term_months'],date('Y-m-d'));
      foreach($sch as $s){$db->prepare("INSERT INTO loan_installments(loan_id,user_id,installment_no,due_date,principal,interest,emi,balance,status,created_at)VALUES(?,?,?,?,?,?,?,?,'pending',NOW())")->execute([$lid,$app['user_id'],$s['no'],$s['due_date'],$s['principal'],$s['interest'],$s['emi'],$s['balance']]);}
      if(!empty($sch))$db->prepare("UPDATE loans SET next_due_date=? WHERE id=?")->execute([$sch[0]['due_date'],$lid]);
      wallet_txn($app['user_id'],'credit',$app['amount'],'Loan disbursement: '.$ln);
      $db->prepare("INSERT INTO documents(user_id,loan_id,application_id,type,title,filename,file_path,uploaded_by,created_at)VALUES(?,?,?,'agreement','Loan Agreement','agreement.pdf','generated','system',NOW())")->execute([$app['user_id'],$lid,$aid]);
      notify($app['user_id'],'Loan Approved!','Your application '.$app['reference'].' approved. '.fmt_money($app['amount']).' disbursed.','success');
      add_log('loan_approved',$app['reference'].' -> '.$ln,$_adm['id']);
      set_flash('success','Approved! Loan '.$ln.' created.');
    }
  }elseif($act==='reject'&&$aid){
    $db->prepare("UPDATE loan_applications SET status='rejected',admin_comment=?,reviewed_by=?,reviewed_at=NOW() WHERE id=?")->execute([$cmt,$_adm['id'],$aid]);
    $r=$db->prepare("SELECT user_id,reference FROM loan_applications WHERE id=?");$r->execute([$aid]);$r=$r->fetch();
    if($r)notify($r['user_id'],'Application Rejected','Your application '.$r['reference'].' was rejected. '.$cmt,'error');
    set_flash('info','Rejected.');
  }elseif($act==='delete'&&$aid){$db->prepare("DELETE FROM loan_applications WHERE id=?")->execute([$aid]);set_flash('success','Deleted.');}
  header('Location: /admin/applications.php');exit;
}

$vid=isset($_GET['view'])?(int)$_GET['view']:0;
if($vid):
  $app=$db->prepare("SELECT la.*,u.full_name,u.email,u.phone,u.national_id,u.employment_status as uemp,u.monthly_income as uinc,lt.name as loan_type FROM loan_applications la JOIN users u ON la.user_id=u.id JOIN loan_types lt ON la.loan_type_id=lt.id WHERE la.id=?");$app->execute([$vid]);$app=$app->fetch();
  if(!$app){set_flash('error','Not found.');header('Location: /admin/applications.php');exit;}
?>
<a href="/admin/applications.php" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary mb-6"><i data-lucide="arrow-left" class="w-4 h-4"></i> Back</a>
<div class="grid lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2 space-y-6">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
      <div class="flex items-center justify-between mb-6"><div><h2 class="text-xl font-bold font-heading"><?=e($app['loan_type'])?></h2><p class="text-sm text-gray-400"><?=e($app['reference'])?></p></div><?=status_badge($app['status'])?></div>
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        <?php foreach([['Amount',fmt_money($app['amount'])],['Rate',$app['interest_rate'].'%'],['Term',$app['term_months'].'m'],['EMI',fmt_money($app['monthly_payment'])],['Total',fmt_money($app['total_repayment'])],['Interest',fmt_money($app['total_interest'])],['Fee Paid',$app['fee_paid']?'Yes':'No'],['Purpose',e($app['purpose']?:'—')],['Applied',fmt_datetime($app['created_at'])]] as $f):?>
        <div class="p-3 rounded-xl bg-gray-50"><div class="text-[11px] text-gray-400 mb-1"><?=$f[0]?></div><div class="text-sm font-semibold"><?=$f[1]?></div></div>
        <?php endforeach;?>
      </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
      <h3 class="font-bold mb-4 font-heading">Applicant</h3>
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
        <?php foreach([['Name',$app['full_name']],['Email',$app['email']],['Phone',$app['phone']],['ID',$app['national_id']?:'—'],['Employment',$app['employment_status']?:$app['uemp']?:'—'],['Income',fmt_money($app['monthly_income']?:$app['uinc'])]] as $f):?>
        <div><span class="text-gray-400 block text-xs"><?=$f[0]?></span><strong><?=e($f[1])?></strong></div>
        <?php endforeach;?>
      </div>
    </div>
  </div>
  <div class="space-y-6">
    <?php if(in_array($app['status'],['pending','fee_paid','under_review'])):?>
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
      <h3 class="font-bold mb-4 font-heading">Review</h3>
      <form method="POST" class="space-y-4"><?=csrf_field()?><input type="hidden" name="app_id" value="<?=$app['id']?>">
        <div><label class="text-sm font-semibold mb-1 block">Comment</label><textarea name="admin_comment" rows="3" class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm" placeholder="Optional..."><?=e($app['admin_comment']??'')?></textarea></div>
        <div class="flex gap-3">
          <button name="action" value="approve" class="flex-1 py-3 rounded-xl bg-emerald-500 text-white text-sm font-bold hover:bg-emerald-600" onclick="return confirm('Approve & disburse?')">Approve</button>
          <button name="action" value="reject" class="flex-1 py-3 rounded-xl bg-red-500 text-white text-sm font-bold hover:bg-red-600" onclick="return confirm('Reject?')">Reject</button>
        </div>
      </form>
    </div>
    <?php endif;?>
    <?php if($app['admin_comment']):?>
    <div class="bg-white rounded-2xl border border-gray-100 p-6"><h3 class="font-bold mb-2 text-sm font-heading">Admin Note</h3><p class="text-sm text-gray-600"><?=e($app['admin_comment'])?></p><?php if($app['reviewed_at']):?><p class="text-xs text-gray-400 mt-2">Reviewed: <?=fmt_datetime($app['reviewed_at'])?></p><?php endif;?></div>
    <?php endif;?>
  </div>
</div>
<?php else:
  $sf=$_GET['status']??'';$w=$sf?"WHERE la.status=?":"";$prm=$sf?[$sf]:[];
  $tot=$db->prepare("SELECT COUNT(*) FROM loan_applications la $w");$tot->execute($prm);$tot=$tot->fetchColumn();
  $pg=paginate($tot,15,(int)($_GET['page']??1));
  $rows=$db->prepare("SELECT la.*,u.full_name,lt.name as loan_type FROM loan_applications la JOIN users u ON la.user_id=u.id JOIN loan_types lt ON la.loan_type_id=lt.id $w ORDER BY la.created_at DESC LIMIT {$pg['per']} OFFSET {$pg['offset']}");$rows->execute($prm);$apps=$rows->fetchAll();
?>
<div class="flex flex-wrap gap-2 mb-6">
  <?php foreach(['=>All','pending'=>'Pending','fee_paid'=>'Fee Paid','approved'=>'Approved','rejected'=>'Rejected'] as $k=>$v):$k=($k==='=>All')?'':$k;?>
  <a href="/admin/applications.php?status=<?=$k?>" class="px-4 py-2 rounded-xl text-sm font-medium border <?=$sf===$k?'bg-primary text-white border-primary':'bg-white border-gray-200 hover:border-primary'?> transition"><?=$v?></a>
  <?php endforeach;?>
</div>
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden"><div class="overflow-x-auto"><table class="dtable w-full text-sm"><thead><tr><th class="px-4 py-3 text-left">Ref</th><th class="px-4 py-3 text-left">Applicant</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-right">Amount</th><th class="px-4 py-3 text-center">Fee</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-left">Date</th><th class="px-4 py-3 text-center">Actions</th></tr></thead><tbody>
<?php foreach($apps as $a):?>
<tr class="border-t border-gray-50">
  <td class="px-4 py-3 font-mono text-xs"><?=e($a['reference'])?></td><td class="px-4 py-3 font-medium text-sm"><?=e($a['full_name'])?></td><td class="px-4 py-3 text-sm"><?=e($a['loan_type'])?></td><td class="px-4 py-3 text-right font-bold"><?=fmt_money($a['amount'])?></td>
  <td class="px-4 py-3 text-center"><?=$a['fee_paid']?'<span class="text-emerald-500">✓</span>':'<span class="text-gray-300">✗</span>'?></td>
  <td class="px-4 py-3 text-center"><?=status_badge($a['status'])?></td><td class="px-4 py-3 text-xs text-gray-400"><?=fmt_date($a['created_at'])?></td>
  <td class="px-4 py-3 text-center"><div class="flex items-center justify-center gap-1">
    <a href="/admin/applications.php?view=<?=$a['id']?>" class="p-1.5 rounded-lg hover:bg-primary/10 text-primary"><i data-lucide="eye" class="w-4 h-4"></i></a>
    <form method="POST" class="inline" onsubmit="return confirm('Delete?')"><?=csrf_field()?><input type="hidden" name="action" value="delete"><input type="hidden" name="app_id" value="<?=$a['id']?>"><button class="p-1.5 rounded-lg hover:bg-red-50 text-red-400"><i data-lucide="trash-2" class="w-4 h-4"></i></button></form>
  </div></td>
</tr>
<?php endforeach;?></tbody></table></div></div>
<?=render_pagination($pg,'/admin/applications.php'.($sf?"?status=$sf":''))?>
<?php endif;?>
<?php require_once __DIR__.'/layout_footer.php';?>
