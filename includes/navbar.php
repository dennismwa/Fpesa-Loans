<?php
$_sn = site_name();
$_logo = get_setting('logo','');
$_cur = basename($_SERVER['SCRIPT_NAME']);
$_isHome = ($_cur === 'index.php' && dirname($_SERVER['SCRIPT_NAME']) === '/') || $_cur === 'index.php' && strpos($_SERVER['REQUEST_URI'],'?') === false && $_SERVER['REQUEST_URI'] === '/';

// Get loan types for dropdown
$_ltypes = [];
try { $__db = Database::connect(); $_ltypes = $__db->query("SELECT name, slug, icon FROM loan_types WHERE is_active=1 ORDER BY sort_order")->fetchAll(); } catch(Exception $e) {}
?>

<nav id="topNav" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 <?= $_isHome ? '' : 'nav-solid' ?>">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16 lg:h-20">
      <!-- Logo -->
      <a href="/" class="flex items-center gap-2.5 flex-shrink-0">
        <?php if ($_logo): ?>
          <img src="/uploads/logos/<?= e($_logo) ?>" alt="<?= e($_sn) ?>" class="h-9">
        <?php else: ?>
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary to-emerald-400 flex items-center justify-center shadow-sm">
            <span class="text-white font-bold text-lg font-heading">F</span>
          </div>
          <span class="text-xl font-bold font-heading"><span class="grad-text"><?= e($_sn) ?></span></span>
        <?php endif; ?>
      </a>

      <!-- Desktop Menu -->
      <div class="hidden lg:flex items-center gap-1">
        <a href="/" class="px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/5 hover:text-primary transition">Home</a>

        <!-- Loans Dropdown -->
        <div class="relative group">
          <button class="px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/5 hover:text-primary transition inline-flex items-center gap-1">
            Loans <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform group-hover:rotate-180"></i>
          </button>
          <div class="absolute top-full left-0 pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 translate-y-1 group-hover:translate-y-0">
            <div class="glass rounded-2xl shadow-2xl p-2.5 w-60 border border-gray-100/60">
              <?php foreach ($_ltypes as $_lt): ?>
              <a href="/pages/loan-details.php?slug=<?= $_lt['slug'] ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-primary/5 transition group/i">
                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover/i:bg-primary group-hover/i:text-white transition">
                  <i data-lucide="<?= $_lt['icon'] ?>" class="w-4 h-4"></i>
                </div>
                <span class="text-sm font-medium"><?= e($_lt['name']) ?></span>
              </a>
              <?php endforeach; ?>
              <div class="border-t border-gray-100 mt-1 pt-1">
                <a href="/pages/loans.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-primary/5 transition text-sm font-medium text-primary">
                  View All Loans <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
              </div>
            </div>
          </div>
        </div>

        <a href="/pages/loan-calculator.php" class="px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/5 hover:text-primary transition">Calculator</a>
        <a href="/pages/about.php" class="px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/5 hover:text-primary transition">About</a>
        <a href="/pages/contact.php" class="px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/5 hover:text-primary transition">Contact</a>
      </div>

      <!-- CTA -->
      <div class="hidden lg:flex items-center gap-3">
        <?php if (is_logged_in()): ?>
          <a href="/user/dashboard.php" class="btn-outline px-5 py-2.5 rounded-xl text-sm font-bold">Dashboard</a>
        <?php else: ?>
          <a href="/auth/login.php" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:text-primary transition">Sign In</a>
          <a href="/pages/apply-loan.php" class="btn-primary px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm">Apply Now</a>
        <?php endif; ?>
      </div>

      <!-- Mobile toggle -->
      <button id="mobBtn" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition" aria-label="Menu">
        <i data-lucide="menu" class="w-6 h-6"></i>
      </button>
    </div>
  </div>
</nav>

<!-- Mobile Menu -->
<div id="mobMenu" class="mob-menu fixed inset-y-0 right-0 w-80 max-w-[85vw] bg-white shadow-2xl z-[60] overflow-y-auto">
  <div class="p-6">
    <div class="flex items-center justify-between mb-8">
      <span class="text-lg font-bold grad-text font-heading"><?= e($_sn) ?></span>
      <button id="mobClose" class="p-2 rounded-lg hover:bg-gray-100"><i data-lucide="x" class="w-5 h-5"></i></button>
    </div>
    <nav class="space-y-1">
      <a href="/" class="block px-4 py-3 rounded-xl hover:bg-primary/5 font-medium transition">Home</a>
      <div class="px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Loan Types</div>
      <?php foreach ($_ltypes as $_lt): ?>
      <a href="/pages/loan-details.php?slug=<?= $_lt['slug'] ?>" class="block px-4 py-2.5 rounded-xl hover:bg-primary/5 text-sm transition ml-2"><?= e($_lt['name']) ?></a>
      <?php endforeach; ?>
      <a href="/pages/loan-calculator.php" class="block px-4 py-3 rounded-xl hover:bg-primary/5 font-medium transition">Loan Calculator</a>
      <a href="/pages/about.php" class="block px-4 py-3 rounded-xl hover:bg-primary/5 font-medium transition">About Us</a>
      <a href="/pages/contact.php" class="block px-4 py-3 rounded-xl hover:bg-primary/5 font-medium transition">Contact</a>
    </nav>
    <div class="mt-8 space-y-3">
      <?php if (is_logged_in()): ?>
        <a href="/user/dashboard.php" class="block w-full text-center btn-primary px-5 py-3 rounded-xl text-sm font-bold">My Dashboard</a>
        <a href="/auth/logout.php" class="block w-full text-center px-5 py-3 rounded-xl text-sm font-bold border-2 border-gray-200 hover:border-red-300 text-red-500 transition">Sign Out</a>
      <?php else: ?>
        <a href="/auth/login.php" class="block w-full text-center px-5 py-3 rounded-xl text-sm font-bold border-2 border-gray-200 hover:border-primary transition">Sign In</a>
        <a href="/pages/apply-loan.php" class="block w-full text-center btn-primary px-5 py-3 rounded-xl text-sm font-bold">Apply Now</a>
      <?php endif; ?>
    </div>
  </div>
</div>
<div id="mobOverlay" class="fixed inset-0 bg-black/30 z-[55] hidden" onclick="closeMob()"></div>

<script>
function openMob(){document.getElementById('mobMenu').classList.add('open');document.getElementById('mobOverlay').classList.remove('hidden');document.body.style.overflow='hidden'}
function closeMob(){document.getElementById('mobMenu').classList.remove('open');document.getElementById('mobOverlay').classList.add('hidden');document.body.style.overflow=''}
document.getElementById('mobBtn').onclick=openMob;
document.getElementById('mobClose').onclick=closeMob;

// Nav scroll
window.addEventListener('scroll',function(){
  var n=document.getElementById('topNav');
  if(window.scrollY>30) n.classList.add('nav-solid');
  else if(<?= $_isHome?'true':'false' ?>) n.classList.remove('nav-solid');
});
</script>
