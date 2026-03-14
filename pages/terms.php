<?php
require_once __DIR__.'/../config/helpers.php';$page_title='Terms & Conditions';
require_once __DIR__.'/../includes/header.php';require_once __DIR__.'/../includes/navbar.php';$sn=site_name();
?>
<section class="pt-28 pb-20"><div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
  <h1 class="text-3xl font-extrabold mb-8 font-heading">Terms & <span class="grad-text">Conditions</span></h1>
  <div class="prose prose-sm max-w-none text-gray-600 space-y-6">
    <p>By using <?=e($sn)?> services, you agree to these terms.</p>
    <h2 class="text-lg font-bold text-gray-800 font-heading">1. Eligibility</h2><p>You must be 18+, a Kenyan citizen/resident with valid National ID. Employment or verifiable income required.</p>
    <h2 class="text-lg font-bold text-gray-800 font-heading">2. Loan Application</h2><p>All applications subject to review. Non-refundable processing fee of <?=fmt_money(app_fee())?> per application. Submission does not guarantee approval.</p>
    <h2 class="text-lg font-bold text-gray-800 font-heading">3. Interest & Repayment</h2><p>Interest rates vary by type and are displayed before application. Repayment in equal monthly installments (EMI). Late payments may incur charges.</p>
    <h2 class="text-lg font-bold text-gray-800 font-heading">4. Loan Agreement</h2><p>Upon approval, a loan agreement is generated. You must download, sign, and upload the signed copy. Disbursement occurs after verification.</p>
    <h2 class="text-lg font-bold text-gray-800 font-heading">5. Privacy</h2><p>Personal information stored securely and used only for loan processing. We do not share data with third parties without consent.</p>
    <h2 class="text-lg font-bold text-gray-800 font-heading">6. Default</h2><p>Failure to repay may result in penalties, negative credit reporting, and legal action.</p>
    <p class="text-xs text-gray-400">Last updated: <?=date('F Y')?></p>
  </div>
</div></section>
<?php require_once __DIR__.'/../includes/footer.php';?>
