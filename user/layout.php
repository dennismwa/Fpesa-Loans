<?php
require_once __DIR__ . '/../config/helpers.php';
require_login();
$_u = current_user();
if (!$_u) { session_destroy(); header('Location: /auth/login.php'); exit; }

$db = Database::connect();
$_wbal = get_wallet_balance($_u['id']);
$_ncount = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0"); $_ncount->execute([$_u['id']]); $_ncount = $_ncount->fetchColumn();
$_pg = basename($_SERVER['SCRIPT_NAME'], '.php');

if (!isset($page_title)) $page_title = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex min-h-screen bg-gray-50">
  <!-- Sidebar -->
  <aside id="uSB" class="fixed lg:sticky top-0 left-0 h-screen w-[270px] bg-white border-r border-gray-100 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col overflow-y-auto">
    <div class="p-5 border-b border-gray-100">
      <a href="/" class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center"><span class="text-white font-bold font-heading">F</span></div>
        <span class="text-lg font-bold grad-text font-heading"><?= e(site_name()) ?></span>
      </a>
    </div>
    <div class="p-4 border-b border-gray-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm"><?= strtoupper($_u['full_name'][0]) ?></div>
        <div class="min-w-0"><div class="font-semibold text-sm truncate"><?= e($_u['full_name']) ?></div><div class="text-xs text-gray-400 truncate"><?= e($_u['email']) ?></div></div>
      </div>
    </div>
    <nav class="flex-1 p-3 space-y-0.5">
      <?php
      $uLinks = [
        ['dashboard','layout-dashboard','Dashboard'],
        ['my-loans','landmark','My Loans'],
        ['loan-status','clock','Applications'],
        ['wallet','wallet','Wallet'],
        ['payments','credit-card','Payments'],
        ['documents','file-text','Documents'],
        ['profile','settings','Profile'],
      ];
      foreach ($uLinks as $ul):
        $act = ($_pg === $ul[0]) ? 'active' : '';
      ?>
      <a href="/user/<?= $ul[0] ?>.php" class="slink flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-600 <?= $act ?>">
        <i data-lucide="<?= $ul[1] ?>" class="w-[17px] h-[17px]"></i> <?= $ul[2] ?>
      </a>
      <?php endforeach; ?>
    </nav>
    <div class="p-3">
      <div class="bg-primary rounded-2xl p-4 text-white">
        <div class="text-[11px] text-white/60 uppercase tracking-wider mb-1">Wallet Balance</div>
        <div class="text-xl font-bold font-heading"><?= fmt_money($_wbal) ?></div>
      </div>
    </div>
    <div class="p-3 border-t border-gray-100">
      <a href="/auth/logout.php" class="slink flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-red-500 hover:bg-red-50 border-l-transparent">
        <i data-lucide="log-out" class="w-[17px] h-[17px]"></i> Sign Out
      </a>
    </div>
  </aside>
  <div id="uOV" class="fixed inset-0 bg-black/30 z-30 hidden lg:hidden" onclick="toggleUSB()"></div>

  <main class="flex-1 min-w-0">
    <header class="sticky top-0 z-20 bg-white/90 backdrop-blur-xl border-b border-gray-100">
      <div class="flex items-center justify-between px-4 sm:px-6 h-16">
        <div class="flex items-center gap-3">
          <button onclick="toggleUSB()" class="lg:hidden p-2 rounded-lg hover:bg-gray-100"><i data-lucide="menu" class="w-5 h-5"></i></button>
          <h1 class="text-lg font-bold font-heading"><?= e($page_title) ?></h1>
        </div>
        <div class="flex items-center gap-2">
          <a href="/pages/apply-loan.php" class="hidden sm:inline-flex btn-primary px-4 py-2 rounded-lg text-xs font-bold items-center gap-1 shadow-sm">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Apply Loan
          </a>
          <div class="relative">
            <button onclick="document.getElementById('nDrop').classList.toggle('hidden')" class="p-2 rounded-lg hover:bg-gray-100 relative">
              <i data-lucide="bell" class="w-5 h-5 text-gray-500"></i>
              <?php if ($_ncount > 0): ?><span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[9px] rounded-full flex items-center justify-center font-bold"><?= $_ncount ?></span><?php endif; ?>
            </button>
            <div id="nDrop" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden">
              <div class="p-3 border-b border-gray-100 text-xs font-bold text-gray-400 uppercase">Notifications</div>
              <?php
              $nstmt = $db->prepare("SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 5"); $nstmt->execute([$_u['id']]); $nots = $nstmt->fetchAll();
              if (empty($nots)): ?><p class="text-sm text-gray-400 text-center py-6">No notifications</p>
              <?php else: foreach ($nots as $nt): ?>
              <div class="px-3 py-2.5 hover:bg-gray-50 border-b border-gray-50 <?= $nt['is_read']?'':'bg-primary/[.03]' ?>">
                <div class="text-sm font-semibold text-gray-700"><?= e($nt['title']) ?></div>
                <div class="text-xs text-gray-400 mt-0.5"><?= time_ago($nt['created_at']) ?></div>
              </div>
              <?php endforeach; endif; ?>
            </div>
          </div>
        </div>
      </div>
    </header>
    <div class="p-4 sm:p-6 lg:p-8">
      <?= render_flash() ?>
