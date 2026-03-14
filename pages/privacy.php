<?php
require_once __DIR__.'/../config/helpers.php';$page_title='Privacy Policy';
require_once __DIR__.'/../includes/header.php';require_once __DIR__.'/../includes/navbar.php';$sn=site_name();
?>
<section class="pt-28 pb-20"><div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
  <h1 class="text-3xl font-extrabold mb-8 font-heading">Privacy <span class="grad-text">Policy</span></h1>
  <div class="prose prose-sm max-w-none text-gray-600 space-y-6">
    <p><?=e($sn)?> is committed to protecting your privacy.</p>
    <h2 class="text-lg font-bold text-gray-800 font-heading">Information We Collect</h2><p>Name, email, phone, National ID, employment details, and financial information provided during registration and loan application.</p>
    <h2 class="text-lg font-bold text-gray-800 font-heading">How We Use It</h2><p>Process loan applications, manage accounts, communicate updates, generate agreements, and comply with legal requirements.</p>
    <h2 class="text-lg font-bold text-gray-800 font-heading">Security</h2><p>Industry-standard measures including encrypted transmission, secure password hashing, and protected database access.</p>
    <h2 class="text-lg font-bold text-gray-800 font-heading">Your Rights</h2><p>You can access, correct, or request deletion of your data. Contact <?=e(get_setting('contact_email','info@fpesa.co.ke'))?> for requests.</p>
    <p class="text-xs text-gray-400">Last updated: <?=date('F Y')?></p>
  </div>
</div></section>
<?php require_once __DIR__.'/../includes/footer.php';?>
