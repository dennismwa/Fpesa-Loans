<?php
require_once __DIR__.'/../config/helpers.php';
if(!is_logged_in()){$_SESSION['redirect_after_login']='/pages/apply-loan.php';set_flash('info','Please sign in to apply.');header('Location: /auth/login.php');exit;}
$user=current_user();$db=Database::connect();
$loan_types=$db->query("SELECT * FROM loan_types WHERE is_active=1 ORDER BY sort_order")->fetchAll();
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'&&verify_csrf()){
  $tid=(int)($_POST['loan_type_id']??0);$amt=(float)($_POST['amount']??0);$term=(int)($_POST['term_months']??0);
  $purpose=trim($_POST['purpose']??'');$emp=trim($_POST['employment_status']??'');$inc=(float)($_POST['monthly_income']??0);$nid=trim($_POST['national_id']??'');
  $lt=$db->prepare("SELECT * FROM loan_types WHERE id=? AND is_active=1");$lt->execute([$tid]);$lt=$lt->fetch();
  if(!$lt)$err='Invalid loan type.';
  elseif($amt<$lt['min_amount']||$amt>$lt['max_amount'])$err='Amount must be between '.fmt_money($lt['min_amount']).' and '.fmt_money($lt['max_amount']);
  elseif($term<$lt['min_term']||$term>$lt['max_term'])$err='Term must be '.$lt['min_term'].'-'.$lt['max_term'].' months.';
  else{
    $calc=calculate_emi($amt,$lt['interest_rate'],$term);$ref=gen_ref('APP');$fee=app_fee();
    $db->prepare("UPDATE users SET national_id=COALESCE(NULLIF(?,''),national_id),employment_status=COALESCE(NULLIF(?,''),employment_status),monthly_income=? WHERE id=?")->execute([$nid,$emp,$inc,$user['id']]);
    $db->prepare("INSERT INTO loan_applications(reference,user_id,loan_type_id,amount,term_months,interest_rate,monthly_payment,total_repayment,total_interest,purpose,employment_status,monthly_income,status,fee_amount,created_at)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,'pending',?,NOW())")
      ->execute([$ref,$user['id'],$tid,$amt,$term,$lt['interest_rate'],$calc['emi'],$calc['total_payment'],$calc['total_interest'],$purpose,$emp,$inc,$fee]);
    $appid=$db->lastInsertId();
    notify($user['id'],'Application Submitted','Ref: '.$ref.'. Pay processing fee of '.fmt_money($fee).' to proceed.','info');
    add_log('loan_apply',"$ref - ".fmt_money($amt),$user['id']);
    set_flash('success','Application submitted! Ref: '.$ref);
    header('Location: /user/payments.php?pay_fee='.$appid);exit;
  }
}
$presel=$_GET['type']??'';$page_title='Apply for a Loan';$page_description='Apply for fast loans online.';
require_once __DIR__.'/../includes/header.php';require_once __DIR__.'/../includes/navbar.php';
?>
<section class="pt-28 pb-20 bg-gray-50 min-h-screen">
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
  <div class="text-center mb-10" data-aos="fade-up"><h1 class="text-3xl font-extrabold mb-2 font-heading">Apply for a <span class="grad-text">Loan</span></h1><p class="text-gray-500">Processing fee: <strong class="text-primary"><?=fmt_money(app_fee())?></strong></p></div>
  <?php if($err):?><div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-r-lg mb-6 text-sm"><?=e($err)?></div><?php endif;?>
  <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8" data-aos="fade-up">
    <form method="POST" class="space-y-6"><?=csrf_field()?>
      <div><label class="text-sm font-bold text-gray-700 mb-3 block">Select Loan Type</label>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3"><?php foreach($loan_types as $lt):?>
        <label class="cursor-pointer"><input type="radio" name="loan_type_id" value="<?=$lt['id']?>" class="peer hidden" data-min="<?=$lt['min_amount']?>" data-max="<?=$lt['max_amount']?>" data-rate="<?=$lt['interest_rate']?>" data-mint="<?=$lt['min_term']?>" data-maxt="<?=$lt['max_term']?>" <?=$presel==$lt['slug']?'checked':''?> required onchange="uLT(this)">
          <div class="p-3 rounded-xl border-2 border-gray-100 peer-checked:border-primary peer-checked:bg-primary/5 text-center transition hover:border-primary/30">
            <i data-lucide="<?=$lt['icon']?>" class="w-5 h-5 mx-auto mb-1 text-gray-400"></i><div class="text-xs font-bold"><?=e($lt['name'])?></div><div class="text-[10px] text-gray-400"><?=$lt['interest_rate']?>%</div>
          </div></label><?php endforeach;?></div></div>
      <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="text-sm font-bold text-gray-700 mb-2 flex justify-between">Amount <span class="text-primary text-xs" id="aRng"></span></label><input type="number" name="amount" id="lAmt" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm" placeholder="Enter amount" oninput="reCalc()"></div>
        <div><label class="text-sm font-bold text-gray-700 mb-2 flex justify-between">Term (months) <span class="text-primary text-xs" id="tRng"></span></label><input type="number" name="term_months" id="lTer" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm" placeholder="Months" oninput="reCalc()"></div>
      </div>
      <div id="cPrev" class="bg-primary/5 rounded-2xl p-5 hidden"><div class="grid grid-cols-3 gap-4 text-center">
        <div><div class="text-xs text-gray-500">Monthly EMI</div><div class="text-lg font-bold grad-text" id="pEMI">—</div></div>
        <div><div class="text-xs text-gray-500">Total Interest</div><div class="text-lg font-bold" id="pInt">—</div></div>
        <div><div class="text-xs text-gray-500">Total Repayment</div><div class="text-lg font-bold" id="pTot">—</div></div>
      </div></div>
      <div class="border-t border-gray-100 pt-6"><h3 class="font-bold mb-4 font-heading">Personal Details</h3>
        <div class="grid sm:grid-cols-2 gap-4">
          <div><label class="text-sm font-semibold mb-1 block">National ID</label><input type="text" name="national_id" value="<?=e($user['national_id']??'')?>" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm"></div>
          <div><label class="text-sm font-semibold mb-1 block">Employment</label><select name="employment_status" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm"><option value="">Select</option><?php foreach(['Employed','Self-Employed','Business Owner','Student','Unemployed'] as $es):?><option value="<?=$es?>" <?=($user['employment_status']??'')===$es?'selected':''?>><?=$es?></option><?php endforeach;?></select></div>
          <div><label class="text-sm font-semibold mb-1 block">Monthly Income (KSH)</label><input type="number" name="monthly_income" value="<?=$user['monthly_income']??''?>" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm"></div>
          <div><label class="text-sm font-semibold mb-1 block">Purpose</label><input type="text" name="purpose" class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm" placeholder="e.g. Business expansion"></div>
        </div></div>
      <button type="submit" class="btn-primary w-full py-4 rounded-xl text-sm font-bold flex items-center justify-center gap-2">Submit Application <i data-lucide="arrow-right" class="w-4 h-4"></i></button>
    </form>
  </div>
</div></section>
<script>
var cRate=14;function fm(n){return'KSH '+Math.round(n).toLocaleString()}
function eCalc(p,r,n){var m=(r/100)/12;return m>0?p*m*Math.pow(1+m,n)/(Math.pow(1+m,n)-1):p/n}
function uLT(el){document.getElementById('aRng').textContent=fm(el.dataset.min)+' - '+fm(el.dataset.max);document.getElementById('tRng').textContent=el.dataset.mint+'-'+el.dataset.maxt+'m';cRate=parseFloat(el.dataset.rate);document.getElementById('lAmt').min=el.dataset.min;document.getElementById('lAmt').max=el.dataset.max;document.getElementById('lTer').min=el.dataset.mint;document.getElementById('lTer').max=el.dataset.maxt;reCalc()}
function reCalc(){var a=parseFloat(document.getElementById('lAmt').value)||0,t=parseInt(document.getElementById('lTer').value)||0,p=document.getElementById('cPrev');if(a>0&&t>0){var e=eCalc(a,cRate,t),tot=e*t;document.getElementById('pEMI').textContent=fm(e);document.getElementById('pInt').textContent=fm(tot-a);document.getElementById('pTot').textContent=fm(tot);p.classList.remove('hidden')}else p.classList.add('hidden')}
var ck=document.querySelector('input[name=loan_type_id]:checked');if(ck)uLT(ck);
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
