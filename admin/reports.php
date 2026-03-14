<?php
$page_title='Reports';require_once __DIR__.'/layout.php';$db=Database::connect();
$td=fmt_money($db->query("SELECT COALESCE(SUM(principal),0) FROM loans")->fetchColumn());
$tr=fmt_money($db->query("SELECT COALESCE(SUM(amount_paid),0) FROM loans")->fetchColumn());
$tb=fmt_money($db->query("SELECT COALESCE(SUM(balance),0) FROM loans WHERE status='active'")->fetchColumn());
$tf=fmt_money($db->query("SELECT COALESCE(SUM(fee_amount),0) FROM loan_applications WHERE fee_paid=1")->fetchColumn());
$tu=$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$ta=$db->query("SELECT COUNT(*) FROM loan_applications")->fetchColumn();
$aa=$db->query("SELECT COUNT(*) FROM loan_applications WHERE status='approved'")->fetchColumn();
$al=$db->query("SELECT COUNT(*) FROM loans WHERE status='active'")->fetchColumn();
$cl=$db->query("SELECT COUNT(*) FROM loans WHERE status='completed'")->fetchColumn();
$ts=$db->query("SELECT lt.name,COUNT(l.id)as c,COALESCE(SUM(l.principal),0)as t FROM loan_types lt LEFT JOIN loans l ON lt.id=l.loan_type_id GROUP BY lt.id,lt.name ORDER BY t DESC")->fetchAll();

if(isset($_GET['export'])){
  header('Content-Type:text/csv');header('Content-Disposition:attachment;filename="fpesa_'.$_GET['export'].'_'.date('Ymd').'.csv"');$o=fopen('php://output','w');
  if($_GET['export']==='loans'){fputcsv($o,['Loan#','Name','Type','Principal','Rate','Term','EMI','Total','Paid','Balance','Status','Date']);
    $r=$db->query("SELECT l.*,u.full_name,lt.name as lt FROM loans l JOIN users u ON l.user_id=u.id JOIN loan_types lt ON l.loan_type_id=lt.id ORDER BY l.created_at DESC");while($x=$r->fetch())fputcsv($o,[$x['loan_number'],$x['full_name'],$x['lt'],$x['principal'],$x['interest_rate'],$x['term_months'],$x['monthly_payment'],$x['total_repayment'],$x['amount_paid'],$x['balance'],$x['status'],$x['created_at']]);
  }elseif($_GET['export']==='payments'){fputcsv($o,['Ref','User','Type','Method','Amount','TxnRef','Status','Date']);
    $r=$db->query("SELECT p.*,u.full_name FROM payments p JOIN users u ON p.user_id=u.id ORDER BY p.created_at DESC");while($x=$r->fetch())fputcsv($o,[$x['reference'],$x['full_name'],$x['payment_type'],$x['payment_method'],$x['amount'],$x['transaction_ref'],$x['status'],$x['created_at']]);
  }elseif($_GET['export']==='users'){fputcsv($o,['Name','Email','Phone','ID','Employment','Income','Active','Joined']);
    $r=$db->query("SELECT * FROM users ORDER BY created_at DESC");while($x=$r->fetch())fputcsv($o,[$x['full_name'],$x['email'],$x['phone'],$x['national_id'],$x['employment_status'],$x['monthly_income'],$x['is_active'],$x['created_at']]);
  }fclose($o);exit;
}
?>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
<?php foreach([['Total Disbursed',$td,'banknote','from-primary to-emerald-400'],['Total Repaid',$tr,'check-circle','from-emerald-500 to-green-400'],['Outstanding',$tb,'alert-circle','from-amber-500 to-orange-400'],['Fee Revenue',$tf,'trending-up','from-purple-500 to-violet-400']] as $c):?>
<div class="bg-white rounded-2xl border border-gray-100 p-5"><div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center mb-3"><i data-lucide="<?=$c[2]?>" class="w-5 h-5 text-white"></i></div><div class="text-xl font-bold font-heading"><?=$c[1]?></div><div class="text-xs text-gray-400"><?=$c[0]?></div></div>
<?php endforeach;?>
</div>
<div class="grid lg:grid-cols-2 gap-6 mb-8">
  <div class="bg-white rounded-2xl border border-gray-100 p-6"><h3 class="font-bold mb-5 font-heading">Key Metrics</h3><div class="space-y-3">
    <?php foreach([['Total Users',$tu],['Applications',$ta],['Approved',$aa],['Active Loans',$al],['Completed',$cl],['Approval Rate',$ta>0?round($aa/$ta*100,1).'%':'0%']] as $m):?>
    <div class="flex justify-between py-2 border-b border-gray-50 text-sm"><span class="text-gray-500"><?=$m[0]?></span><span class="font-bold"><?=$m[1]?></span></div><?php endforeach;?></div></div>
  <div class="bg-white rounded-2xl border border-gray-100 p-6"><h3 class="font-bold mb-5 font-heading">By Loan Type</h3><div class="space-y-3">
    <?php $ttd=(float)str_replace(['KSH',' ',','],'',$td);foreach($ts as $x):$pct=$ttd>0?round($x['t']/$ttd*100):0;?>
    <div><div class="flex justify-between text-sm mb-1"><span><?=e($x['name'])?> <span class="text-gray-400">(<?=$x['c']?>)</span></span><span class="font-bold"><?=fmt_money($x['t'])?></span></div>
    <div class="w-full bg-gray-100 rounded-full h-2"><div class="bg-primary h-2 rounded-full" style="width:<?=max(2,$pct)?>%"></div></div></div><?php endforeach;?></div></div>
</div>
<div class="bg-white rounded-2xl border border-gray-100 p-6"><h3 class="font-bold mb-5 font-heading">Export Reports</h3><div class="flex flex-wrap gap-3">
  <a href="/admin/reports.php?export=loans" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary/10 text-primary text-sm font-bold hover:bg-primary/20"><i data-lucide="download" class="w-4 h-4"></i> Loans CSV</a>
  <a href="/admin/reports.php?export=payments" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-50 text-blue-600 text-sm font-bold hover:bg-blue-100"><i data-lucide="download" class="w-4 h-4"></i> Payments CSV</a>
  <a href="/admin/reports.php?export=users" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-purple-50 text-purple-600 text-sm font-bold hover:bg-purple-100"><i data-lucide="download" class="w-4 h-4"></i> Users CSV</a>
</div></div>
<?php require_once __DIR__.'/layout_footer.php';?>
