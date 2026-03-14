<?php
require_once __DIR__.'/../config/helpers.php';$slug=$_GET['slug']??'';if(!$slug){header('Location: /pages/loans.php');exit;}
$db=Database::connect();$st=$db->prepare("SELECT * FROM loan_types WHERE slug=? AND is_active=1");$st->execute([$slug]);$lt=$st->fetch();
if(!$lt){header('Location: /pages/loans.php');exit;}
$page_title=$lt['name'];$page_description=$lt['description'];
require_once __DIR__.'/../includes/header.php';require_once __DIR__.'/../includes/navbar.php';
?>
<section class="pt-28 pb-20 bg-gray-50">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
  <div class="grid lg:grid-cols-2 gap-12 items-start">
    <div data-aos="fade-right">
      <a href="/pages/loans.php" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary mb-4"><i data-lucide="arrow-left" class="w-4 h-4"></i> All Loans</a>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-semibold mb-4"><i data-lucide="<?=$lt['icon']?>" class="w-3.5 h-3.5"></i> <?=e($lt['name'])?></div>
      <h1 class="text-3xl sm:text-4xl font-extrabold mb-4 font-heading"><?=e($lt['name'])?></h1>
      <p class="text-gray-500 leading-relaxed mb-8"><?=e($lt['description'])?></p>
      <div class="grid grid-cols-2 gap-4 mb-8">
        <div class="p-4 rounded-2xl bg-white border border-gray-100"><div class="text-xs text-gray-400 mb-1">Range</div><div class="text-sm font-bold"><?=fmt_money($lt['min_amount'])?> — <?=fmt_money($lt['max_amount'])?></div></div>
        <div class="p-4 rounded-2xl bg-white border border-gray-100"><div class="text-xs text-gray-400 mb-1">Rate</div><div class="text-sm font-bold"><?=$lt['interest_rate']?>% p.a.</div></div>
        <div class="p-4 rounded-2xl bg-white border border-gray-100"><div class="text-xs text-gray-400 mb-1">Term</div><div class="text-sm font-bold"><?=$lt['min_term']?>-<?=$lt['max_term']?> months</div></div>
        <div class="p-4 rounded-2xl bg-white border border-gray-100"><div class="text-xs text-gray-400 mb-1">Fee</div><div class="text-sm font-bold"><?=fmt_money(app_fee())?></div></div>
      </div>
      <a href="/pages/apply-loan.php?type=<?=$lt['slug']?>" class="btn-primary inline-flex items-center gap-2 px-8 py-4 rounded-2xl text-sm font-bold shadow-lg shadow-primary/20">Apply Now <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
    </div>
    <div data-aos="fade-left">
      <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        <h3 class="font-bold text-lg mb-5 font-heading">Loan Calculator</h3>
        <div class="space-y-5">
          <div><label class="text-sm font-bold mb-2 flex justify-between">Amount <span class="text-primary" id="dAL"><?=fmt_money($lt['min_amount'])?></span></label><input type="range" id="dA" min="<?=$lt['min_amount']?>" max="<?=$lt['max_amount']?>" value="<?=$lt['min_amount']?>" step="1000" class="w-full" oninput="dC()"></div>
          <div><label class="text-sm font-bold mb-2 flex justify-between">Term <span class="text-primary" id="dTL"><?=$lt['min_term']?> months</span></label><input type="range" id="dT" min="<?=$lt['min_term']?>" max="<?=$lt['max_term']?>" value="<?=$lt['min_term']?>" class="w-full" oninput="dC()"></div>
          <div class="bg-primary/5 rounded-2xl p-5 text-center">
            <div class="text-sm text-gray-500 mb-1">Monthly Payment</div><div class="text-3xl font-extrabold grad-text font-heading" id="dE">—</div>
            <div class="flex justify-center gap-6 mt-3 text-xs text-gray-500"><span>Interest: <strong id="dI">—</strong></span><span>Total: <strong id="dTo">—</strong></span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div></section>
<script>
function dC(){var a=+document.getElementById('dA').value,t=+document.getElementById('dT').value,r=<?=$lt['interest_rate']?>,m=(r/100)/12,e=m>0?a*m*Math.pow(1+m,t)/(Math.pow(1+m,t)-1):a/t,tot=e*t,f=function(n){return'KSH '+Math.round(n).toLocaleString()};document.getElementById('dAL').textContent=f(a);document.getElementById('dTL').textContent=t+' months';document.getElementById('dE').textContent=f(e);document.getElementById('dI').textContent=f(tot-a);document.getElementById('dTo').textContent=f(tot)}dC();
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
