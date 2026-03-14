<?php
$_sn = site_name();
$_email = get_setting('contact_email','info@fpesa.co.ke');
$_phone = get_setting('contact_phone','+254 700 000 000');
$_addr = get_setting('contact_address','Nairobi, Kenya');

$_ltypes2 = [];
try { $_db2 = Database::connect(); $_ltypes2 = $_db2->query("SELECT name, slug FROM loan_types WHERE is_active=1 ORDER BY sort_order LIMIT 7")->fetchAll(); } catch(Exception $e) {}
?>

<footer class="relative bg-dark text-gray-300 overflow-hidden">
  <div class="blob w-[500px] h-[500px] bg-primary -top-60 -right-60"></div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-8 relative z-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-16">
      <!-- Brand -->
      <div>
        <div class="flex items-center gap-2.5 mb-5">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary to-emerald-400 flex items-center justify-center">
            <span class="text-white font-bold text-lg font-heading">F</span>
          </div>
          <span class="text-xl font-bold text-white font-heading"><?= e($_sn) ?></span>
        </div>
        <p class="text-sm leading-relaxed text-gray-400 mb-6"><?= e(get_setting('site_description','Fast, affordable loans for everyone.')) ?></p>
        <div class="flex gap-2.5">
          <?php foreach(['facebook','twitter','instagram','linkedin'] as $_s): $_su = get_setting($_s.'_url','#'); if($_su && $_su !== '#'): ?>
          <a href="<?= e($_su) ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-white/5 hover:bg-primary flex items-center justify-center text-gray-400 hover:text-white transition">
            <i data-lucide="<?= $_s ?>" class="w-4 h-4"></i>
          </a>
          <?php endif; endforeach; ?>
        </div>
      </div>

      <!-- Quick Links -->
      <div>
        <h4 class="text-white font-bold mb-5 font-heading">Quick Links</h4>
        <ul class="space-y-3 text-sm">
          <li><a href="/" class="hover:text-primary transition">Home</a></li>
          <li><a href="/pages/about.php" class="hover:text-primary transition">About Us</a></li>
          <li><a href="/pages/loans.php" class="hover:text-primary transition">Our Loans</a></li>
          <li><a href="/pages/loan-calculator.php" class="hover:text-primary transition">Loan Calculator</a></li>
          <li><a href="/pages/apply-loan.php" class="hover:text-primary transition">Apply for Loan</a></li>
          <li><a href="/pages/contact.php" class="hover:text-primary transition">Contact Us</a></li>
        </ul>
      </div>

      <!-- Loan Types -->
      <div>
        <h4 class="text-white font-bold mb-5 font-heading">Loan Services</h4>
        <ul class="space-y-3 text-sm">
          <?php foreach($_ltypes2 as $_lt2): ?>
          <li><a href="/pages/loan-details.php?slug=<?= $_lt2['slug'] ?>" class="hover:text-primary transition"><?= e($_lt2['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Contact -->
      <div>
        <h4 class="text-white font-bold mb-5 font-heading">Contact Us</h4>
        <ul class="space-y-4 text-sm">
          <li class="flex items-start gap-3"><i data-lucide="map-pin" class="w-4 h-4 mt-0.5 text-primary flex-shrink-0"></i><?= e($_addr) ?></li>
          <li class="flex items-center gap-3"><i data-lucide="phone" class="w-4 h-4 text-primary flex-shrink-0"></i><a href="tel:<?= e($_phone) ?>" class="hover:text-primary transition"><?= e($_phone) ?></a></li>
          <li class="flex items-center gap-3"><i data-lucide="mail" class="w-4 h-4 text-primary flex-shrink-0"></i><a href="mailto:<?= e($_email) ?>" class="hover:text-primary transition"><?= e($_email) ?></a></li>
        </ul>
      </div>
    </div>

    <div class="border-t border-white/10 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
      <p>&copy; <?= date('Y') ?> <?= e($_sn) ?>. All rights reserved.</p>
      <div class="flex gap-6">
        <a href="/pages/terms.php" class="hover:text-primary transition">Terms</a>
        <a href="/pages/privacy.php" class="hover:text-primary transition">Privacy</a>
      </div>
    </div>
  </div>
</footer>

<script>
AOS.init({duration:650,once:true,offset:40});
lucide.createIcons();

// Auto-dismiss flash messages after 6s
document.querySelectorAll('.flash-msg').forEach(function(el){
  setTimeout(function(){el.style.opacity='0';el.style.transform='translateY(-10px)';el.style.transition='all .4s';setTimeout(function(){el.remove()},400)},6000);
});

// PWA
if('serviceWorker' in navigator){navigator.serviceWorker.register('/pwa/service-worker.js').catch(function(){});}
</script>
</body>
</html>
