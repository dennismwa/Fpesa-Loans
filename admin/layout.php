<?php
require_once __DIR__ . '/../config/helpers.php';
require_admin();
$_adm = current_admin();
if (!$_adm) { header('Location: /admin/login.php'); exit; }
$_pg = basename($_SERVER['SCRIPT_NAME'], '.php');
$db = Database::connect();
$_pendApps = $db->query("SELECT COUNT(*) FROM loan_applications WHERE status IN ('pending','fee_paid')")->fetchColumn();
$_pendPay = $db->query("SELECT COUNT(*) FROM payments WHERE status='pending'")->fetchColumn();

if (!isset($page_title)) $page_title = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex min-h-screen bg-gray-50">
  <aside id="aSB" class="fixed lg:sticky top-0 left-0 h-screen w-[260px] bg-dark text-gray-400 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col overflow-y-auto">
    <div class="p-5 border-b border-white/5">
      <a href="/admin/dashboard.php" class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center"><span class="text-white font-bold font-heading">F</span></div>
        <div><span class="text-base font-bold text-white font-heading"><?= e(site_name()) ?></span><div class="text-[9px] text-gray-600 uppercase tracking-[.15em]">Admin Panel</div></div>
      </a>
    </div>
    <nav class="flex-1 p-3 space-y-0.5">
      <div class="text-[9px] uppercase tracking-[.15em] text-gray-600 px-4 py-2 mt-1">Main</div>
      <?php
      $nav = [
        ['dashboard','layout-dashboard','Dashboard',0],
        ['users','users','Users',0],
        ['loan-types','layers','Loan Types',0],
        ['applications','file-text','Applications',$_pendApps],
        ['loans','landmark','Loans',0],
        ['payments','credit-card','Payments',$_pendPay],
        ['wallets','wallet','Wallets',0],
      ];
      foreach ($nav as $n):
        $act = $_pg===$n[0] ? 'active' : '';
      ?>
      <a href="/admin/<?= $n[0] ?>.php" class="slink-dark flex items-center justify-between px-4 py-2.5 rounded-r-lg text-sm font-medium <?= $act ?>">
        <span class="flex items-center gap-3"><i data-lucide="<?= $n[1] ?>" class="w-[16px] h-[16px]"></i><?= $n[2] ?></span>
        <?php if ($n[3]>0): ?><span class="text-[9px] bg-red-500 text-white px-1.5 py-0.5 rounded-full font-bold"><?= $n[3] ?></span><?php endif; ?>
      </a>
      <?php endforeach; ?>

      <div class="text-[9px] uppercase tracking-[.15em] text-gray-600 px-4 py-2 mt-3">System</div>
      <?php
      $sys = [['payment-methods','settings-2','Pay Methods'],['documents','file','Documents'],['settings','sliders','Settings'],['reports','bar-chart-3','Reports']];
      foreach ($sys as $n): $act = $_pg===$n[0]?'active':'';
      ?>
      <a href="/admin/<?= $n[0] ?>.php" class="slink-dark flex items-center gap-3 px-4 py-2.5 rounded-r-lg text-sm font-medium <?= $act ?>">
        <i data-lucide="<?= $n[1] ?>" class="w-[16px] h-[16px]"></i><?= $n[2] ?>
      </a>
      <?php endforeach; ?>
    </nav>
    <div class="p-3 border-t border-white/5">
      <div class="flex items-center gap-3 px-4 py-2">
        <div class="w-8 h-8 rounded-full bg-primary/30 flex items-center justify-center text-primary text-xs font-bold"><?= strtoupper($_adm['name'][0]) ?></div>
        <div class="min-w-0 flex-1"><div class="text-xs font-medium text-white truncate"><?= e($_adm['name']) ?></div><div class="text-[10px] text-gray-600"><?= ucfirst($_adm['role']) ?></div></div>
      </div>
      <a href="/admin/logout.php" class="flex items-center gap-3 px-4 py-2 text-sm text-red-400 hover:bg-red-500/10 rounded-lg transition mt-1"><i data-lucide="log-out" class="w-4 h-4"></i> Sign Out</a>
    </div>
  </aside>
  <div id="aOV" class="fixed inset-0 bg-black/30 z-30 hidden lg:hidden" onclick="toggleASB()"></div>

  <main class="flex-1 min-w-0">
    <header class="sticky top-0 z-20 bg-white/90 backdrop-blur-xl border-b border-gray-100">
      <div class="flex items-center justify-between px-4 sm:px-6 h-14">
        <div class="flex items-center gap-3">
          <button onclick="toggleASB()" class="lg:hidden p-2 rounded-lg hover:bg-gray-100"><i data-lucide="menu" class="w-5 h-5"></i></button>
          <h1 class="text-base font-bold font-heading"><?= e($page_title) ?></h1>
        </div>
        <a href="/" target="_blank" class="text-xs text-gray-400 hover:text-primary flex items-center gap-1"><i data-lucide="external-link" class="w-3 h-3"></i> View Site</a>
      </div>
    </header>
    <div class="p-4 sm:p-6 lg:p-8"><?= render_flash() ?>
