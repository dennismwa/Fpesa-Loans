<?php
require_once __DIR__ . '/../config/helpers.php';
if (is_logged_in()) { header('Location: /user/dashboard.php'); exit; }

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) { $err = 'Security token expired. Try again.'; }
    else {
        $email = trim($_POST['email'] ?? '');
        $pass = $_POST['password'] ?? '';
        if (!$email || !$pass) { $err = 'Please fill in all fields.'; }
        else {
            $db = Database::connect();
            $st = $db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
            $st->execute([$email]);
            $u = $st->fetch();
            if ($u && password_verify($pass, $u['password'])) {
                $_SESSION['user_id'] = $u['id'];
                $_SESSION['user_name'] = $u['full_name'];
                $db->prepare("UPDATE users SET last_login=NOW() WHERE id=?")->execute([$u['id']]);
                add_log('user_login', 'User signed in', $u['id']);
                $redir = $_SESSION['redirect_after_login'] ?? '/user/dashboard.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redir);
                exit;
            }
            $err = 'Invalid email or password.';
        }
    }
}

$page_title = 'Sign In';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-12">
  <div class="blob w-[400px] h-[400px] bg-primary -top-40 -right-40 fixed"></div>
  <div class="w-full max-w-md relative z-10" data-aos="fade-up">
    <div class="text-center mb-8">
      <a href="/" class="inline-flex items-center gap-2.5">
        <div class="w-11 h-11 rounded-xl bg-primary flex items-center justify-center"><span class="text-white font-bold text-xl font-heading">F</span></div>
        <span class="text-2xl font-bold grad-text font-heading"><?= e(site_name()) ?></span>
      </a>
    </div>
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
      <h1 class="text-2xl font-bold mb-1 font-heading">Welcome back</h1>
      <p class="text-sm text-gray-500 mb-8">Sign in to your account to continue.</p>

      <?php echo render_flash(); ?>
      <?php if ($err): ?><div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-r-lg mb-6 text-sm"><?= e($err) ?></div><?php endif; ?>

      <form method="POST" class="space-y-5" autocomplete="on">
        <?= csrf_field() ?>
        <div>
          <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Email Address</label>
          <div class="relative">
            <i data-lucide="mail" class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" class="finput w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 text-sm" placeholder="you@example.com" autofocus>
          </div>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Password</label>
          <div class="relative">
            <i data-lucide="lock" class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input type="password" name="password" required id="lp" class="finput w-full pl-11 pr-11 py-3 rounded-xl border border-gray-200 text-sm" placeholder="Enter your password">
            <button type="button" onclick="var x=document.getElementById('lp');x.type=x.type==='password'?'text':'password'" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"><i data-lucide="eye" class="w-4 h-4"></i></button>
          </div>
        </div>
        <button type="submit" class="btn-primary w-full py-3.5 rounded-xl text-sm font-bold shadow-sm">Sign In</button>
      </form>
      <p class="text-center text-sm text-gray-500 mt-6">Don't have an account? <a href="/auth/register.php" class="text-primary font-bold hover:underline">Create Account</a></p>
    </div>
    <p class="text-center text-xs text-gray-400 mt-6"><a href="/admin/login.php" class="hover:text-primary transition">Admin Login</a></p>
  </div>
</div>
<script>lucide.createIcons();AOS.init({duration:500,once:true})</script>
</body></html>
