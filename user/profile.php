<?php
$page_title = 'Profile';
require_once __DIR__ . '/layout.php';
$db = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $act = $_POST['action'] ?? '';
    if ($act === 'update_profile') {
        $db->prepare("UPDATE users SET full_name=?, phone=?, national_id=?, employment_status=?, monthly_income=?, address=?, updated_at=NOW() WHERE id=?")
            ->execute([trim($_POST['full_name']??''), trim($_POST['phone']??''), trim($_POST['national_id']??''), trim($_POST['employment_status']??''), (float)($_POST['monthly_income']??0), trim($_POST['address']??''), $_u['id']]);
        set_flash('success', 'Profile updated!');
    } elseif ($act === 'change_password') {
        $cur = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $conf = $_POST['confirm_password'] ?? '';
        if (!password_verify($cur, $_u['password'])) { set_flash('error', 'Current password is wrong.'); }
        elseif (strlen($new) < 6) { set_flash('error', 'New password must be at least 6 characters.'); }
        elseif ($new !== $conf) { set_flash('error', 'New passwords do not match.'); }
        else {
            $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($new, PASSWORD_DEFAULT), $_u['id']]);
            set_flash('success', 'Password changed!');
        }
    }
    header('Location: /user/profile.php'); exit;
}
// Refresh user
$st = $db->prepare("SELECT * FROM users WHERE id=?"); $st->execute([$_u['id']]); $u = $st->fetch();
?>

<div class="grid lg:grid-cols-3 gap-6">
  <div class="bg-white rounded-2xl border border-gray-100 p-6" data-aos="fade-up">
    <div class="text-center mb-6">
      <div class="w-20 h-20 rounded-full bg-primary flex items-center justify-center text-white text-2xl font-bold mx-auto mb-3"><?= strtoupper($u['full_name'][0]) ?></div>
      <h3 class="font-bold text-lg font-heading"><?= e($u['full_name']) ?></h3>
      <p class="text-sm text-gray-400"><?= e($u['email']) ?></p>
    </div>
    <div class="space-y-2.5 text-sm">
      <?php foreach ([['Phone',$u['phone']],['ID',$u['national_id']?:'Not set'],['Employment',$u['employment_status']?:'Not set'],['Income',fmt_money($u['monthly_income'])],['Joined',fmt_date($u['created_at'])]] as $f): ?>
      <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-gray-400"><?= $f[0] ?></span><span class="font-medium"><?= e($f[1]) ?></span></div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="lg:col-span-2 space-y-6">
    <div class="bg-white rounded-2xl border border-gray-100 p-6" data-aos="fade-up" data-aos-delay="100">
      <h3 class="font-bold mb-5 font-heading">Edit Profile</h3>
      <form method="POST" class="space-y-4">
        <?= csrf_field() ?><input type="hidden" name="action" value="update_profile">
        <div class="grid sm:grid-cols-2 gap-4">
          <div><label class="text-sm font-semibold mb-1 block">Full Name</label><input type="text" name="full_name" value="<?= e($u['full_name']) ?>" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm"></div>
          <div><label class="text-sm font-semibold mb-1 block">Phone</label><input type="tel" name="phone" value="<?= e($u['phone']) ?>" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm"></div>
          <div><label class="text-sm font-semibold mb-1 block">National ID</label><input type="text" name="national_id" value="<?= e($u['national_id']??'') ?>" class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm"></div>
          <div><label class="text-sm font-semibold mb-1 block">Employment</label>
            <select name="employment_status" class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm"><option value="">Select</option>
            <?php foreach(['Employed','Self-Employed','Business Owner','Student','Unemployed'] as $es): ?><option value="<?= $es ?>" <?= ($u['employment_status']??'')===$es?'selected':'' ?>><?= $es ?></option><?php endforeach; ?>
            </select></div>
          <div><label class="text-sm font-semibold mb-1 block">Monthly Income (KSH)</label><input type="number" name="monthly_income" value="<?= $u['monthly_income'] ?>" class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm"></div>
        </div>
        <div><label class="text-sm font-semibold mb-1 block">Address</label><textarea name="address" rows="2" class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm"><?= e($u['address']??'') ?></textarea></div>
        <button type="submit" class="btn-primary px-6 py-3 rounded-xl text-sm font-bold">Save Changes</button>
      </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6" data-aos="fade-up" data-aos-delay="200">
      <h3 class="font-bold mb-5 font-heading">Change Password</h3>
      <form method="POST" class="space-y-4">
        <?= csrf_field() ?><input type="hidden" name="action" value="change_password">
        <div><label class="text-sm font-semibold mb-1 block">Current Password</label><input type="password" name="current_password" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm"></div>
        <div class="grid sm:grid-cols-2 gap-4">
          <div><label class="text-sm font-semibold mb-1 block">New Password</label><input type="password" name="new_password" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm"></div>
          <div><label class="text-sm font-semibold mb-1 block">Confirm</label><input type="password" name="confirm_password" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm"></div>
        </div>
        <button type="submit" class="px-6 py-3 rounded-xl text-sm font-bold border-2 border-primary text-primary hover:bg-primary/5 transition">Update Password</button>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
