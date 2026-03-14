<?php
require_once __DIR__.'/../config/helpers.php';$page_title='About Us';$page_description='Learn about '.site_name();
require_once __DIR__.'/../includes/header.php';require_once __DIR__.'/../includes/navbar.php';$sn=site_name();
?>
<section class="pt-28 pb-20 bg-gradient-to-br from-gray-50 via-white to-emerald-50/20">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
  <div class="text-center mb-16" data-aos="fade-up"><h1 class="text-3xl sm:text-4xl font-extrabold mb-4 font-heading">About <span class="grad-text"><?=e($sn)?></span></h1><p class="text-gray-500 max-w-2xl mx-auto">We're on a mission to make financial services accessible, transparent, and affordable for every Kenyan.</p></div>
  <div class="grid md:grid-cols-2 gap-12 items-center mb-20" data-aos="fade-up">
    <div><h2 class="text-2xl font-bold mb-4 font-heading">Our Mission</h2><p class="text-gray-500 leading-relaxed mb-4"><?=e($sn)?> was founded with a simple belief: everyone deserves access to fair, transparent financial services. We leverage technology to make loan applications faster and completely hassle-free.</p><p class="text-gray-500 leading-relaxed">Whether you need a personal loan, business funding, or emergency cash, we help you achieve your goals with competitive rates and flexible terms.</p></div>
    <div class="bg-gradient-to-br from-primary to-emerald-600 rounded-3xl p-8 text-white"><div class="grid grid-cols-2 gap-6">
      <div class="text-center"><div class="text-3xl font-bold font-heading">10K+</div><div class="text-sm text-white/60">Happy Customers</div></div>
      <div class="text-center"><div class="text-3xl font-bold font-heading">500M+</div><div class="text-sm text-white/60">Loans Disbursed</div></div>
      <div class="text-center"><div class="text-3xl font-bold font-heading">98%</div><div class="text-sm text-white/60">Approval Rate</div></div>
      <div class="text-center"><div class="text-3xl font-bold font-heading">24hrs</div><div class="text-sm text-white/60">Avg Processing</div></div>
    </div></div>
  </div>
  <div class="grid sm:grid-cols-3 gap-8" data-aos="fade-up"><?php
  $vals=[['shield-check','Trust & Transparency','Clear terms, no hidden fees, honest communication.'],['zap','Speed & Efficiency','Digital-first for faster applications and instant disbursements.'],['heart','Customer First','Every decision guided by what is best for our customers.']];
  foreach($vals as $v):?><div class="text-center p-8 bg-white rounded-2xl border border-gray-100 card-lift"><div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4 text-primary"><i data-lucide="<?=$v[0]?>" class="w-6 h-6"></i></div><h3 class="font-bold mb-2 font-heading"><?=$v[1]?></h3><p class="text-sm text-gray-500"><?=$v[2]?></p></div><?php endforeach;?></div>
</div></section>
<?php require_once __DIR__.'/../includes/footer.php';?>
