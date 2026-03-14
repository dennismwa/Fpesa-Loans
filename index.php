<?php
require_once __DIR__ . '/config/helpers.php';
$page_title = get_setting('meta_title', 'Fast & Affordable Loans');
$page_description = get_setting('meta_description', '');
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$loan_types = [];
try { $db = Database::connect(); $loan_types = $db->query("SELECT * FROM loan_types WHERE is_active=1 ORDER BY sort_order")->fetchAll(); } catch(Exception $e) {}
$sn = site_name();
?>

<!-- ══════ HERO ══════ -->
<section class="relative min-h-screen flex items-center overflow-hidden bg-gray-50">
  <div class="blob w-[600px] h-[600px] bg-primary top-10 -left-48"></div>
  <div class="blob w-[400px] h-[400px] bg-secondary bottom-10 right-0 opacity-[.08]"></div>
  <div class="absolute inset-0 opacity-[.015]" style="background-image:radial-gradient(circle,<?= primary_color() ?> 1px,transparent 1px);background-size:28px 28px"></div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-20 relative z-10">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
      <!-- Left -->
      <div data-aos="fade-right">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-6">
          <i data-lucide="sparkles" class="w-4 h-4"></i> Trusted by 10,000+ Kenyans
        </div>
        <h1 class="text-4xl sm:text-5xl lg:text-[3.5rem] font-extrabold leading-[1.1] mb-6 font-heading">
          Get Instant<br><span class="grad-text">Loans</span> Anytime,<br>Anywhere
        </h1>
        <p class="text-lg text-gray-500 leading-relaxed mb-8 max-w-lg">
          Fast approval, flexible repayment, and competitive rates. Your financial goals are within reach with <?= e($sn) ?>.
        </p>
        <div class="flex flex-wrap gap-4 mb-10">
          <a href="/pages/apply-loan.php" class="btn-primary px-8 py-4 rounded-2xl text-sm font-bold inline-flex items-center gap-2 shadow-lg shadow-primary/20">
            Apply Now <i data-lucide="arrow-right" class="w-4 h-4"></i>
          </a>
          <a href="#calculator" class="btn-outline px-8 py-4 rounded-2xl text-sm font-bold inline-flex items-center gap-2">
            <i data-lucide="calculator" class="w-4 h-4"></i> Calculate Loan
          </a>
        </div>
        <div class="flex flex-wrap gap-8">
          <div><div class="text-2xl font-extrabold grad-text font-heading">98%</div><div class="text-xs text-gray-400 mt-0.5">Approval Rate</div></div>
          <div><div class="text-2xl font-extrabold grad-text font-heading">24hrs</div><div class="text-xs text-gray-400 mt-0.5">Fast Disbursement</div></div>
          <div><div class="text-2xl font-extrabold grad-text font-heading">4.9★</div><div class="text-xs text-gray-400 mt-0.5">Customer Rating</div></div>
        </div>
      </div>

      <!-- Right — Quick Estimate Card -->
      <div data-aos="fade-left" class="relative">
        <div class="relative z-10 bg-white rounded-3xl shadow-2xl shadow-primary/[.08] p-7 sm:p-8 max-w-md mx-auto border border-gray-100/80">
          <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-lg font-heading">Quick Loan Estimate</h3>
            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center"><i data-lucide="banknote" class="w-5 h-5 text-primary"></i></div>
          </div>
          <div class="space-y-5">
            <div>
              <label class="text-sm font-semibold text-gray-600 mb-2 block">Loan Amount</label>
              <input type="range" id="hAmt" min="5000" max="1000000" value="100000" step="5000" class="w-full" oninput="hCalc()">
              <div class="flex justify-between text-xs text-gray-400 mt-1"><span>KSH 5K</span><span class="font-bold text-primary text-base" id="hAmtV">KSH 100,000</span><span>KSH 1M</span></div>
            </div>
            <div>
              <label class="text-sm font-semibold text-gray-600 mb-2 block">Repayment Period</label>
              <input type="range" id="hMon" min="1" max="36" value="12" class="w-full" oninput="hCalc()">
              <div class="flex justify-between text-xs text-gray-400 mt-1"><span>1 month</span><span class="font-bold text-primary text-base" id="hMonV">12 months</span><span>36</span></div>
            </div>
            <div class="bg-primary/5 rounded-2xl p-5">
              <div class="text-sm text-gray-500 mb-1">Monthly Payment</div>
              <div class="text-3xl font-extrabold grad-text font-heading" id="hEMI">KSH 9,456</div>
              <div class="flex gap-6 mt-2 text-xs text-gray-400">
                <span>Rate: <strong class="text-gray-600">14%</strong></span>
                <span>Total: <strong class="text-gray-600" id="hTot">KSH 113,472</strong></span>
              </div>
            </div>
            <a href="/pages/apply-loan.php" class="block w-full text-center btn-primary py-3.5 rounded-xl text-sm font-bold shadow-sm">Apply for This Loan</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════ LOAN SERVICES ══════ -->
<section class="py-24 bg-white" id="services">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-16" data-aos="fade-up">
      <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4"><i data-lucide="layers" class="w-4 h-4"></i> Our Services</span>
      <h2 class="text-3xl sm:text-4xl font-extrabold mb-4 font-heading">Loan Solutions <span class="grad-text">Tailored For You</span></h2>
      <p class="text-gray-500 max-w-xl mx-auto">From personal needs to business growth, we have the perfect loan for every situation.</p>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
      <?php foreach($loan_types as $i=>$lt): ?>
      <div class="card-lift bg-white rounded-2xl border border-gray-100 p-6 group" data-aos="fade-up" data-aos-delay="<?= $i*70 ?>">
        <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center mb-5 text-primary">
          <i data-lucide="<?= $lt['icon'] ?>" class="w-6 h-6"></i>
        </div>
        <h3 class="font-bold text-lg mb-2 font-heading"><?= e($lt['name']) ?></h3>
        <p class="text-sm text-gray-500 leading-relaxed mb-4"><?= e(mb_strimwidth($lt['description'],0,95,'...')) ?></p>
        <div class="flex items-center justify-between text-xs text-gray-400 mb-4 pb-4 border-b border-gray-100">
          <span>From <?= fmt_money($lt['min_amount']) ?></span>
          <span><?= $lt['interest_rate'] ?>% p.a.</span>
        </div>
        <a href="/pages/loan-details.php?slug=<?= $lt['slug'] ?>" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:gap-2 transition-all">
          Learn More <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════ HOW IT WORKS ══════ -->
<section class="py-24 bg-gray-50 relative overflow-hidden" id="how-it-works">
  <div class="blob w-[500px] h-[500px] bg-primary -bottom-48 -left-48"></div>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-16" data-aos="fade-up">
      <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4"><i data-lucide="git-branch" class="w-4 h-4"></i> Simple Process</span>
      <h2 class="text-3xl sm:text-4xl font-extrabold mb-4 font-heading">How It <span class="grad-text">Works</span></h2>
      <p class="text-gray-500 max-w-xl mx-auto">Get your loan in 4 simple steps — fast, transparent, hassle-free.</p>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
      <?php
      $steps=[
        ['user-plus','Create Account','Register in under 2 minutes with your basic details.','from-primary to-emerald-400'],
        ['file-text','Apply for Loan','Choose your loan type and fill the application form.','from-emerald-400 to-teal-400'],
        ['check-circle','Get Approved','Our team reviews and approves within 24 hours.','from-teal-400 to-cyan-400'],
        ['banknote','Receive Funds','Once approved, funds are disbursed instantly.','from-cyan-400 to-blue-400'],
      ];
      foreach($steps as $i=>$s): ?>
      <div data-aos="fade-up" data-aos-delay="<?= $i*100 ?>" class="relative text-center">
        <div class="relative inline-flex mb-6">
          <div class="w-20 h-20 rounded-3xl bg-primary flex items-center justify-center shadow-lg"><i data-lucide="<?= $s[0] ?>" class="w-8 h-8 text-white"></i></div>
          <div class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-white shadow-md flex items-center justify-center font-bold text-sm text-primary font-heading"><?= $i+1 ?></div>
        </div>
        <h3 class="font-bold text-lg mb-2 font-heading"><?= $s[1] ?></h3>
        <p class="text-sm text-gray-500"><?= $s[2] ?></p>
        <?php if($i<3): ?><div class="hidden lg:block absolute top-10 left-full w-full"><div class="border-t-2 border-dashed border-primary/20 w-3/4 mx-auto"></div></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════ LOAN CALCULATOR ══════ -->
<section class="py-24 bg-white" id="calculator">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-16" data-aos="fade-up">
      <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4"><i data-lucide="calculator" class="w-4 h-4"></i> Calculator</span>
      <h2 class="text-3xl sm:text-4xl font-extrabold mb-4 font-heading">Plan Your <span class="grad-text">Repayment</span></h2>
    </div>
    <div class="max-w-4xl mx-auto" data-aos="fade-up">
      <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="grid md:grid-cols-2">
          <div class="p-8 lg:p-10 space-y-6">
            <div>
              <label class="text-sm font-bold text-gray-700 mb-2 flex justify-between">Loan Amount <span class="text-primary" id="cAmtL">KSH 100,000</span></label>
              <input type="range" id="cAmt" min="5000" max="5000000" value="100000" step="5000" class="w-full" oninput="cCalc()">
            </div>
            <div>
              <label class="text-sm font-bold text-gray-700 mb-2 flex justify-between">Interest Rate <span class="text-primary" id="cRatL">14%</span></label>
              <input type="range" id="cRat" min="1" max="30" value="14" step="0.5" class="w-full" oninput="cCalc()">
            </div>
            <div>
              <label class="text-sm font-bold text-gray-700 mb-2 flex justify-between">Loan Term <span class="text-primary" id="cTerL">12 months</span></label>
              <input type="range" id="cTer" min="1" max="60" value="12" class="w-full" oninput="cCalc()">
            </div>
          </div>
          <div class="bg-primary p-8 lg:p-10 text-white flex flex-col justify-center">
            <div class="text-sm text-white/60 mb-1">Monthly Installment</div>
            <div class="text-4xl font-extrabold mb-6 font-heading" id="cEMI">KSH 9,456</div>
            <div class="space-y-4">
              <div class="flex justify-between py-3 border-b border-white/10"><span class="text-white/60">Principal</span><span class="font-semibold" id="cP">KSH 100,000</span></div>
              <div class="flex justify-between py-3 border-b border-white/10"><span class="text-white/60">Total Interest</span><span class="font-semibold" id="cI">KSH 13,472</span></div>
              <div class="flex justify-between py-3"><span class="text-white/60">Total Repayment</span><span class="font-bold text-lg" id="cT">KSH 113,472</span></div>
            </div>
            <a href="/pages/apply-loan.php" class="mt-6 block text-center bg-white text-primary py-3.5 rounded-xl font-bold text-sm hover:shadow-lg transition">Apply for This Loan</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════ BENEFITS + LIPA MDOGO MDOGO ══════ -->
<section class="py-24 bg-gray-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid lg:grid-cols-2 gap-16 items-center">
      <div data-aos="fade-right">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4"><i data-lucide="award" class="w-4 h-4"></i> Why Choose Us</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold mb-6 font-heading">Benefits That <span class="grad-text">Set Us Apart</span></h2>
        <p class="text-gray-500 mb-8">We're redefining how Kenyans access financial services with technology-driven solutions.</p>
        <div class="space-y-5">
          <?php
          $bens=[['zap','Instant Approval','Get approved within 24 hours of applying.'],['shield','Bank-Level Security','256-bit encryption protects all your data.'],['percent','Low Interest Rates','Competitive rates starting from 10% per annum.'],['clock','Flexible Repayment','Choose terms from 1 to 60 months.'],['headphones','24/7 Support','Dedicated team ready to help anytime.']];
          foreach($bens as $j=>$b): ?>
          <div class="flex items-start gap-4" data-aos="fade-up" data-aos-delay="<?= $j*60 ?>">
            <div class="icon-box w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-primary"><i data-lucide="<?= $b[0] ?>" class="w-5 h-5"></i></div>
            <div><h4 class="font-bold mb-0.5 font-heading"><?= $b[1] ?></h4><p class="text-sm text-gray-500"><?= $b[2] ?></p></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Lipa Mdogo Mdogo -->
      <div data-aos="fade-left">
        <div class="bg-primary rounded-3xl p-8 text-white">
          <h3 class="text-2xl font-bold mb-4 font-heading">Lipa Mdogo Mdogo</h3>
          <p class="text-white/70 mb-6">Our installment model makes loan repayment easy. Pay small manageable amounts monthly without financial strain.</p>
          <div class="bg-white/10 rounded-2xl p-5 mb-4">
            <div class="text-sm text-white/60 mb-1">Example: KSH 100,000 Loan</div>
            <div class="text-2xl font-bold font-heading">KSH 9,456 <span class="text-sm font-normal text-white/60">/month</span></div>
            <div class="text-sm text-white/60 mt-1">for 12 months at 14% p.a.</div>
          </div>
          <div class="grid grid-cols-3 gap-3">
            <div class="bg-white/10 rounded-xl p-3 text-center"><div class="text-lg font-bold">12</div><div class="text-[11px] text-white/60">Installments</div></div>
            <div class="bg-white/10 rounded-xl p-3 text-center"><div class="text-lg font-bold">14%</div><div class="text-[11px] text-white/60">Interest</div></div>
            <div class="bg-white/10 rounded-xl p-3 text-center"><div class="text-lg font-bold">0</div><div class="text-[11px] text-white/60">Hidden Fees</div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════ TESTIMONIALS ══════ -->
<section class="py-24 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-16" data-aos="fade-up">
      <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4"><i data-lucide="message-circle" class="w-4 h-4"></i> Testimonials</span>
      <h2 class="text-3xl sm:text-4xl font-extrabold mb-4 font-heading">What Our <span class="grad-text">Clients Say</span></h2>
    </div>
    <div class="grid md:grid-cols-3 gap-6">
      <?php
      $tests=[
        ['Mary Wanjiku','Business Owner','Fpesa helped me expand my shop with a quick business loan. The process was seamless and the team was incredibly helpful throughout.'],
        ['John Kamau','Teacher','I used the salary advance to handle an emergency. Got funded the same day I applied! Highly recommend their services.'],
        ['Grace Achieng','Student','The school loan covered my tuition and I can pay it back in small installments. This service has been life-changing for me.'],
      ];
      foreach($tests as $k=>$t): ?>
      <div class="card-lift bg-white rounded-2xl border border-gray-100 p-7" data-aos="fade-up" data-aos-delay="<?= $k*100 ?>">
        <div class="flex gap-0.5 mb-4"><?php for($s=0;$s<5;$s++): ?><i data-lucide="star" class="w-4 h-4 text-secondary fill-secondary"></i><?php endfor; ?></div>
        <p class="text-gray-600 text-sm leading-relaxed mb-6">"<?= $t[2] ?>"</p>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm"><?= $t[0][0] ?></div>
          <div><div class="font-bold text-sm"><?= $t[0] ?></div><div class="text-xs text-gray-400"><?= $t[1] ?></div></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════ FAQ ══════ -->
<section class="py-24 bg-gray-50" id="faq">
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-16" data-aos="fade-up">
      <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4"><i data-lucide="help-circle" class="w-4 h-4"></i> FAQ</span>
      <h2 class="text-3xl sm:text-4xl font-extrabold mb-4 font-heading">Frequently Asked <span class="grad-text">Questions</span></h2>
    </div>
    <div class="space-y-3" data-aos="fade-up">
      <?php
      $faqs=[
        ['How do I apply for a loan?','Create an account, choose your loan type, fill the application form, pay the processing fee of '.fmt_money(app_fee()).', and submit. We review within 24 hours.'],
        ['What documents do I need?','Your National ID, proof of income (payslip or bank statement), and a valid phone number and email address.'],
        ['How long does approval take?','Most applications are reviewed within 24 hours. Once approved, funds are disbursed to your wallet immediately.'],
        ['What is the application fee?','The processing fee is '.fmt_money(app_fee()).'. This is a one-time non-refundable fee per application.'],
        ['Can I repay early?','Yes! You can make early repayments at any time without penalties. Early repayment reduces your total interest.'],
        ['What happens if I miss a payment?','Contact our team to arrange a revised plan. Late payments may incur additional charges as per our terms.'],
      ];
      foreach($faqs as $fi=>$fq): ?>
      <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <button onclick="toggleFaq(<?= $fi ?>)" class="w-full flex items-center justify-between p-5 sm:p-6 text-left font-bold hover:text-primary transition font-heading text-[15px]">
          <?= $fq[0] ?>
          <i data-lucide="chevron-down" class="w-5 h-5 flex-shrink-0 text-gray-400 transition-transform duration-300" id="faqIco<?= $fi ?>"></i>
        </button>
        <div id="faqB<?= $fi ?>" class="overflow-hidden transition-all duration-300" style="max-height:0">
          <p class="px-5 sm:px-6 pb-5 sm:pb-6 text-sm text-gray-500 leading-relaxed"><?= $fq[1] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════ CTA ══════ -->
<section class="py-24 relative overflow-hidden">
  <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image:url('/assets/images/breadcumb-services.png')"></div>
  <div class="absolute inset-0 bg-primary/85"></div>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center" data-aos="fade-up">
    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-6 font-heading">Ready to Get Started?</h2>
    <p class="text-white/70 text-lg mb-10 max-w-xl mx-auto">Join thousands of Kenyans who trust <?= e($sn) ?> for their financial needs.</p>
    <div class="flex flex-wrap justify-center gap-4">
      <a href="/pages/apply-loan.php" class="bg-white text-primary px-8 py-4 rounded-2xl font-bold text-sm hover:shadow-xl transition inline-flex items-center gap-2">Apply Now <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
      <a href="/pages/contact.php" class="border-2 border-white/30 text-white px-8 py-4 rounded-2xl font-bold text-sm hover:bg-white/10 transition">Contact Us</a>
    </div>
  </div>
</section>

<!-- ══════ SCRIPTS ══════ -->
<script>
function fm(n){return 'KSH '+Math.round(n).toLocaleString()}
function emi(p,r,n){var m=(r/100)/12;return m>0?p*m*Math.pow(1+m,n)/(Math.pow(1+m,n)-1):p/n}

// Hero calc
function hCalc(){
  var a=+document.getElementById('hAmt').value,n=+document.getElementById('hMon').value,e=emi(a,14,n),t=e*n;
  document.getElementById('hAmtV').textContent=fm(a);
  document.getElementById('hMonV').textContent=n+' months';
  document.getElementById('hEMI').textContent=fm(e);
  document.getElementById('hTot').textContent=fm(t);
}
hCalc();

// Main calc
function cCalc(){
  var a=+document.getElementById('cAmt').value,r=+document.getElementById('cRat').value,n=+document.getElementById('cTer').value;
  var e=emi(a,r,n),t=e*n;
  document.getElementById('cAmtL').textContent=fm(a);
  document.getElementById('cRatL').textContent=r+'%';
  document.getElementById('cTerL').textContent=n+' months';
  document.getElementById('cEMI').textContent=fm(e);
  document.getElementById('cP').textContent=fm(a);
  document.getElementById('cI').textContent=fm(t-a);
  document.getElementById('cT').textContent=fm(t);
}
cCalc();

// FAQ
function toggleFaq(i){
  var b=document.getElementById('faqB'+i),ico=document.getElementById('faqIco'+i);
  var open=b.style.maxHeight&&b.style.maxHeight!=='0px';
  document.querySelectorAll('[id^=faqB]').forEach(function(el){el.style.maxHeight='0px'});
  document.querySelectorAll('[id^=faqIco]').forEach(function(el){el.style.transform=''});
  if(!open){b.style.maxHeight=b.scrollHeight+'px';ico.style.transform='rotate(180deg)'}
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
