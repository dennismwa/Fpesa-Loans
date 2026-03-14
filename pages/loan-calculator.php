<?php
require_once __DIR__.'/../config/helpers.php';$page_title='Loan Calculator';require_once __DIR__.'/../includes/header.php';require_once __DIR__.'/../includes/navbar.php';
$db=Database::connect();$types=$db->query("SELECT id,name,interest_rate FROM loan_types WHERE is_active=1 ORDER BY sort_order")->fetchAll();
?>
<section class="pt-28 pb-20 bg-gradient-to-br from-gray-50 via-white to-emerald-50/20 min-h-screen">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
  <div class="text-center mb-10" data-aos="fade-up"><h1 class="text-3xl sm:text-4xl font-extrabold mb-4 font-heading">Loan <span class="grad-text">Calculator</span></h1><p class="text-gray-500">Plan your repayment before applying.</p></div>
  <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden" data-aos="fade-up">
    <div class="grid md:grid-cols-2">
      <div class="p-8 lg:p-10 space-y-6">
        <div><label class="text-sm font-bold mb-2 block">Loan Type</label><select id="fT" class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm" onchange="fU()"><?php foreach($types as $t):?><option value="<?=$t['interest_rate']?>"><?=e($t['name'])?> (<?=$t['interest_rate']?>%)</option><?php endforeach;?></select></div>
        <div><label class="text-sm font-bold mb-2 flex justify-between">Amount <span class="text-primary" id="fAL">KSH 100,000</span></label><input type="range" id="fA" min="5000" max="5000000" value="100000" step="5000" class="w-full" oninput="fU()"></div>
        <div><label class="text-sm font-bold mb-2 flex justify-between">Term <span class="text-primary" id="fTL">12 months</span></label><input type="range" id="fTe" min="1" max="60" value="12" class="w-full" oninput="fU()"></div>
      </div>
      <div class="bg-gradient-to-br from-primary to-emerald-600 p-8 lg:p-10 text-white flex flex-col justify-center">
        <div class="text-sm text-white/60 mb-1">Monthly Installment</div><div class="text-4xl font-extrabold mb-6 font-heading" id="fE">—</div>
        <div class="space-y-4"><div class="flex justify-between py-3 border-b border-white/10"><span class="text-white/60">Principal</span><span class="font-semibold" id="fP">—</span></div><div class="flex justify-between py-3 border-b border-white/10"><span class="text-white/60">Total Interest</span><span class="font-semibold" id="fI">—</span></div><div class="flex justify-between py-3"><span class="text-white/60">Total Repayment</span><span class="font-bold text-lg" id="fTo">—</span></div></div>
        <a href="/pages/apply-loan.php" class="mt-6 block text-center bg-white text-primary py-3.5 rounded-xl font-bold text-sm hover:shadow-lg transition">Apply for This Loan</a>
      </div></div></div>
</div></section>
<script>
function fU(){var a=+document.getElementById('fA').value,r=+document.getElementById('fT').value,n=+document.getElementById('fTe').value,m=(r/100)/12,e=m>0?a*m*Math.pow(1+m,n)/(Math.pow(1+m,n)-1):a/n,t=e*n,f=function(x){return'KSH '+Math.round(x).toLocaleString()};document.getElementById('fAL').textContent=f(a);document.getElementById('fTL').textContent=n+' months';document.getElementById('fE').textContent=f(e);document.getElementById('fP').textContent=f(a);document.getElementById('fI').textContent=f(t-a);document.getElementById('fTo').textContent=f(t)}fU();
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
