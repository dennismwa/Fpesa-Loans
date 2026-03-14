<?php
require_once __DIR__ . '/../config/helpers.php';
if (is_logged_in()) { header('Location: /user/dashboard.php'); exit; }

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) { $err = 'Security token expired.'; }
    else {
        $name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $pass = $_POST['password'] ?? '';
        $conf = $_POST['confirm_password'] ?? '';

        if (!$name || !$email || !$phone || !$pass) { $err = 'All fields are required.'; }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $err = 'Enter a valid email address.'; }
        elseif (strlen($pass) < 6) { $err = 'Password must be at least 6 characters.'; }
        elseif ($pass !== $conf) { $err = 'Passwords do not match.'; }
        else {
            $db = Database::connect();
            $chk = $db->prepare("SELECT id FROM users WHERE email = ?"); $chk->execute([$email]);
            if ($chk->fetch()) { $err = 'This email is already registered. Please sign in.'; }
            else {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $db->prepare("INSERT INTO users (full_name, email, phone, password, created_at) VALUES (?,?,?,?,NOW())")->execute([$name, $email, $phone, $hash]);
                $uid = $db->lastInsertId();
                $db->prepare("INSERT INTO wallets (user_id, balance, created_at) VALUES (?,0,NOW())")->execute([$uid]);
                notify($uid, 'Welcome to ' . site_name() . '!', 'Your account is ready. Start by applying for a loan.', 'success');
                add_log('user_register', "New user: $email", $uid);
                set_flash('success', 'Account created successfully! Please sign in.');
                header('Location: /auth/login.php');
                exit;
            }
        }
    }
}

$page_title = 'Create Account';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-12">
  <div class="blob w-[400px] h-[400px] bg-primary -top-40 -left-40 fixed"></div>
  <div class="w-full max-w-md relative z-10" data-aos="fade-up">
    <div class="text-center mb-8">
      <a href="/" class="inline-flex items-center gap-2.5">
        <div class="w-11 h-11 rounded-xl bg-primary flex items-center justify-center"><span class="text-white font-bold text-xl font-heading">F</span></div>
        <span class="text-2xl font-bold grad-text font-heading"><?= e(site_name()) ?></span>
      </a>
    </div>
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
      <h1 class="text-2xl font-bold mb-1 font-heading">Create Account</h1>
      <p class="text-sm text-gray-500 mb-8">Join <?= e(site_name()) ?> and access fast loans.</p>

      <?php if ($err): ?><div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-r-lg mb-6 text-sm"><?= e($err) ?></div><?php endif; ?>

      <form method="POST" class="space-y-5">
        <?= csrf_field() ?>
        <div>
          <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Full Name</label>
          <div class="relative"><i data-lucide="user" class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input type="text" name="full_name" required value="<?= e($_POST['full_name'] ?? '') ?>" class="finput w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 text-sm" placeholder="John Doe">
          </div>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Email</label>
          <div class="relative"><i data-lucide="mail" class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" class="finput w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 text-sm" placeholder="you@example.com">
          </div>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Phone Number</label>
          <div class="relative"><i data-lucide="phone" class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input type="tel" name="phone" required value="<?= e($_POST['phone'] ?? '') ?>" class="finput w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 text-sm" placeholder="0712345678">
          </div>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Password</label>
          <div class="relative"><i data-lucide="lock" class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input type="password" name="password" required class="finput w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 text-sm" placeholder="Min 6 characters">
          </div>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700 mb-1.5 block">Confirm Password</label>
          <div class="relative"><i data-lucide="lock" class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input type="password" name="confirm_password" required class="finput w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 text-sm" placeholder="Repeat password">
          </div>
        </div>
        <button type="submit" class="btn-primary w-full py-3.5 rounded-xl text-sm font-bold shadow-sm">Create Account</button>
      </form>
      <p class="text-center text-sm text-gray-500 mt-6">Already have an account? <a href="/auth/login.php" class="text-primary font-bold hover:underline">Sign In</a></p>
    </div>
  </div>
</div>
<script>lucide.createIcons();AOS.init({duration:500,once:true})</script>
</body></html>
