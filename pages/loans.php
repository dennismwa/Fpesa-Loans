<?php
require_once __DIR__.'/../config/helpers.php';$page_title='Our Loan Services';$page_description='Explore our loan products.';
require_once __DIR__.'/../includes/header.php';require_once __DIR__.'/../includes/navbar.php';
$db=Database::connect();$types=$db->query("SELECT * FROM loan_types WHERE is_active=1 ORDER BY sort_order")->fetchAll();
?>
<section class="pt-28 pb-20 bg-gray-50">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  <div class="text-center mb-16" data-aos="fade-up"><h1 class="text-3xl sm:text-4xl font-extrabold mb-4 font-heading">Our <span class="grad-text">Loan Products</span></h1><p class="text-gray-500 max-w-xl mx-auto">Find the perfect loan for every need.</p></div>
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6"><?php foreach($types as $i=>$lt):?>
  <div class="card-lift bg-white rounded-2xl border border-gray-100 p-7" data-aos="fade-up" data-aos-delay="<?=$i*80?>">
    <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center mb-5 text-primary"><i data-lucide="<?=$lt['icon']?>" class="w-6 h-6"></i></div>
    <h3 class="font-bold text-xl mb-2 font-heading"><?=e($lt['name'])?></h3>
    <p class="text-sm text-gray-500 leading-relaxed mb-5"><?=e($lt['description'])?></p>
    <div class="space-y-2 text-sm mb-6">
      <div class="flex justify-between py-1 border-b border-gray-50"><span class="text-gray-400">Amount</span><span class="font-bold"><?=fmt_money($lt['min_amount'])?> — <?=fmt_money($lt['max_amount'])?></span></div>
      <div class="flex justify-between py-1 border-b border-gray-50"><span class="text-gray-400">Rate</span><span class="font-bold"><?=$lt['interest_rate']?>% p.a.</span></div>
      <div class="flex justify-between py-1"><span class="text-gray-400">Term</span><span class="font-bold"><?=$lt['min_term']?>-<?=$lt['max_term']?> months</span></div>
    </div>
    <div class="flex gap-2">
      <a href="/pages/loan-details.php?slug=<?=$lt['slug']?>" class="flex-1 text-center py-2.5 rounded-xl border border-primary/20 text-primary text-sm font-bold hover:bg-primary/5 transition">Details</a>
      <a href="/pages/apply-loan.php?type=<?=$lt['slug']?>" class="flex-1 text-center btn-primary py-2.5 rounded-xl text-sm font-bold">Apply</a>
    </div>
  </div><?php endforeach;?></div>
</div></section>
<?php require_once __DIR__.'/../includes/footer.php';?>
