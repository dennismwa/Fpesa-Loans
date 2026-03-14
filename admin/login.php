<?php
require_once __DIR__ . '/../config/helpers.php';
if (is_admin()) { header('Location: /admin/dashboard.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) { $err = 'Token expired.'; }
    else {
        $db = Database::connect();
        $st = $db->prepare("SELECT * FROM admins WHERE email=? AND is_active=1"); $st->execute([trim($_POST['email']??'')]); $a = $st->fetch();
        if ($a && password_verify($_POST['password']??'', $a['password'])) {
            $_SESSION['admin_id'] = $a['id']; $_SESSION['admin_name'] = $a['name']; $_SESSION['admin_role'] = $a['role'];
            $db->prepare("UPDATE admins SET last_login=NOW() WHERE id=?")->execute([$a['id']]);
            add_log('admin_login','Admin signed in',$a['id']);
            header('Location: /admin/dashboard.php'); exit;
        }
        $err = 'Invalid email or password.';
    }
}
$page_title = 'Admin Login';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="min-h-screen flex items-center justify-center bg-dark px-4">
  <div class="w-full max-w-md">
    <div class="text-center mb-8">
      <div class="w-14 h-14 rounded-2xl bg-primary flex items-center justify-center mx-auto mb-4"><span class="text-white font-bold text-2xl font-heading">F</span></div>
      <h1 class="text-2xl font-bold text-white font-heading">Admin Panel</h1>
      <p class="text-sm text-gray-500"><?= e(site_name()) ?> Management</p>
    </div>
    <div class="glass-dark rounded-3xl p-8">
      <?php if($err): ?><div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-6 text-sm"><?= e($err) ?></div><?php endif; ?>
      <form method="POST" class="space-y-5">
        <?= csrf_field() ?>
        <div><label class="text-sm font-medium text-gray-400 mb-1.5 block">Email</label>
          <input type="email" name="email" required value="<?= e($_POST['email']??'') ?>" class="w-full py-3 px-4 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-primary focus:outline-none placeholder-gray-600" placeholder="admin@example.com"></div>
        <div><label class="text-sm font-medium text-gray-400 mb-1.5 block">Password</label>
          <input type="password" name="password" required class="w-full py-3 px-4 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-primary focus:outline-none placeholder-gray-600" placeholder="Password"></div>
        <button type="submit" class="btn-primary w-full py-3.5 rounded-xl text-sm font-bold">Sign In</button>
      </form>
    </div>
    <p class="text-center text-xs text-gray-600 mt-6">Default: admin@fpesa.co.ke / password</p>
  </div>
</div>
<script>lucide.createIcons()</script></body></html>
