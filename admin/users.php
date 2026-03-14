<?php
$page_title = 'Manage Users';
require_once __DIR__ . '/layout.php';
$db = Database::connect();

if ($_SERVER['REQUEST_METHOD']==='POST' && verify_csrf()) {
    $act=$_POST['action']??''; $uid=(int)($_POST['user_id']??0);
    if ($act==='toggle' && $uid) { $db->prepare("UPDATE users SET is_active = NOT is_active WHERE id=?")->execute([$uid]); set_flash('success','Status updated.'); }
    elseif ($act==='delete' && $uid) { $db->prepare("DELETE FROM users WHERE id=?")->execute([$uid]); set_flash('success','User deleted.'); }
    elseif ($act==='edit' && $uid) { $db->prepare("UPDATE users SET full_name=?, email=?, phone=?, updated_at=NOW() WHERE id=?")->execute([trim($_POST['full_name']??''),trim($_POST['email']??''),trim($_POST['phone']??''),$uid]); set_flash('success','User updated.'); }
    header('Location: /admin/users.php'); exit;
}

$vid = isset($_GET['view'])?(int)$_GET['view']:0;
if ($vid):
    $u=$db->prepare("SELECT * FROM users WHERE id=?"); $u->execute([$vid]); $u=$u->fetch();
    if(!$u){set_flash('error','Not found.');header('Location: /admin/users.php');exit;}
    $uloans=$db->prepare("SELECT l.*,lt.name as lt FROM loans l JOIN loan_types lt ON l.loan_type_id=lt.id WHERE l.user_id=? ORDER BY l.created_at DESC"); $uloans->execute([$vid]); $uloans=$uloans->fetchAll();
?>
<a href="/admin/users.php" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary mb-6"><i data-lucide="arrow-left" class="w-4 h-4"></i> Back</a>
<div class="grid lg:grid-cols-3 gap-6">
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <div class="text-center mb-4">
      <div class="w-16 h-16 rounded-full bg-primary flex items-center justify-center text-white text-xl font-bold mx-auto mb-3"><?= strtoupper($u['full_name'][0]) ?></div>
      <h3 class="font-bold text-lg font-heading"><?= e($u['full_name']) ?></h3>
      <p class="text-xs text-gray-400 mb-2"><?= e($u['email']) ?></p>
      <?= status_badge($u['is_active']?'active':'rejected') ?>
    </div>
    <div class="space-y-2 text-sm mt-4">
      <?php foreach([['Phone',$u['phone']],['ID',$u['national_id']?:'—'],['Income',fmt_money($u['monthly_income'])],['Wallet',fmt_money(get_wallet_balance($u['id']))],['Joined',fmt_date($u['created_at'])]] as $f): ?>
      <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-gray-400"><?=$f[0]?></span><span class="font-medium"><?=e($f[1])?></span></div>
      <?php endforeach; ?>
    </div>
    <form method="POST" class="mt-4"><?=csrf_field()?><input type="hidden" name="action" value="toggle"><input type="hidden" name="user_id" value="<?=$u['id']?>">
      <button class="w-full py-2 rounded-lg border text-xs font-bold <?=$u['is_active']?'border-red-200 text-red-600 hover:bg-red-50':'border-emerald-200 text-emerald-600 hover:bg-emerald-50'?>"><?=$u['is_active']?'Deactivate':'Activate'?></button></form>
  </div>
  <div class="lg:col-span-2 space-y-6">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
      <h3 class="font-bold mb-4 font-heading">Edit User</h3>
      <form method="POST" class="grid sm:grid-cols-2 gap-4"><?=csrf_field()?><input type="hidden" name="action" value="edit"><input type="hidden" name="user_id" value="<?=$u['id']?>">
        <div><label class="text-sm font-semibold mb-1 block">Name</label><input type="text" name="full_name" value="<?=e($u['full_name'])?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
        <div><label class="text-sm font-semibold mb-1 block">Email</label><input type="email" name="email" value="<?=e($u['email'])?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
        <div><label class="text-sm font-semibold mb-1 block">Phone</label><input type="tel" name="phone" value="<?=e($u['phone'])?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
        <div class="flex items-end"><button class="btn-primary px-6 py-2.5 rounded-xl text-sm font-bold">Save</button></div>
      </form>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
      <div class="p-5 border-b border-gray-100"><h3 class="font-bold text-sm font-heading">Loans (<?=count($uloans)?>)</h3></div>
      <?php if(empty($uloans)):?><p class="text-sm text-gray-400 text-center py-6">No loans</p>
      <?php else:?><table class="dtable w-full text-sm"><thead><tr><th class="px-4 py-3 text-left">Loan #</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-right">Amount</th><th class="px-4 py-3 text-center">Status</th></tr></thead><tbody>
      <?php foreach($uloans as $l):?><tr class="border-t border-gray-50"><td class="px-4 py-3 font-mono text-xs"><?=e($l['loan_number'])?></td><td class="px-4 py-3"><?=e($l['lt'])?></td><td class="px-4 py-3 text-right font-bold"><?=fmt_money($l['principal'])?></td><td class="px-4 py-3 text-center"><?=status_badge($l['status'])?></td></tr><?php endforeach;?></tbody></table><?php endif;?>
    </div>
  </div>
</div>
<?php else:
    $search=trim($_GET['search']??''); $w=$search?"WHERE full_name LIKE ? OR email LIKE ? OR phone LIKE ?":""; $p=$search?["%$search%","%$search%","%$search%"]:[];
    $tot=$db->prepare("SELECT COUNT(*) FROM users $w"); $tot->execute($p); $tot=$tot->fetchColumn();
    $pg=paginate($tot,15,(int)($_GET['page']??1));
    $rows=$db->prepare("SELECT * FROM users $w ORDER BY created_at DESC LIMIT {$pg['per']} OFFSET {$pg['offset']}"); $rows->execute($p); $users=$rows->fetchAll();
?>
<div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6 flex flex-wrap gap-3 items-center justify-between">
  <form class="flex gap-2 flex-1 max-w-md"><input type="text" name="search" value="<?=e($search)?>" placeholder="Search users..." class="finput flex-1 py-2.5 px-4 rounded-xl border border-gray-200 text-sm"><button class="btn-primary px-4 py-2.5 rounded-xl text-sm font-bold">Search</button></form>
  <span class="text-sm text-gray-400"><?=$tot?> users</span>
</div>
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
  <div class="overflow-x-auto"><table class="dtable w-full text-sm"><thead><tr><th class="px-4 py-3 text-left">User</th><th class="px-4 py-3 text-left">Phone</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-left">Joined</th><th class="px-4 py-3 text-center">Actions</th></tr></thead><tbody>
  <?php foreach($users as $u):?>
  <tr class="border-t border-gray-50">
    <td class="px-4 py-3"><div class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xs font-bold"><?=strtoupper($u['full_name'][0])?></div><div><div class="font-medium text-sm"><?=e($u['full_name'])?></div><div class="text-[11px] text-gray-400"><?=e($u['email'])?></div></div></div></td>
    <td class="px-4 py-3 text-sm"><?=e($u['phone'])?></td>
    <td class="px-4 py-3 text-center"><?=status_badge($u['is_active']?'active':'rejected')?></td>
    <td class="px-4 py-3 text-xs text-gray-400"><?=fmt_date($u['created_at'])?></td>
    <td class="px-4 py-3 text-center"><div class="flex items-center justify-center gap-1">
      <a href="/admin/users.php?view=<?=$u['id']?>" class="p-1.5 rounded-lg hover:bg-primary/10 text-primary" title="View"><i data-lucide="eye" class="w-4 h-4"></i></a>
      <form method="POST" class="inline" onsubmit="return confirm('Toggle status?')"><?=csrf_field()?><input type="hidden" name="action" value="toggle"><input type="hidden" name="user_id" value="<?=$u['id']?>"><button class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-500" title="Toggle"><i data-lucide="toggle-left" class="w-4 h-4"></i></button></form>
      <form method="POST" class="inline" onsubmit="return confirm('Delete permanently?')"><?=csrf_field()?><input type="hidden" name="action" value="delete"><input type="hidden" name="user_id" value="<?=$u['id']?>"><button class="p-1.5 rounded-lg hover:bg-red-50 text-red-500" title="Delete"><i data-lucide="trash-2" class="w-4 h-4"></i></button></form>
    </div></td>
  </tr>
  <?php endforeach;?></tbody></table></div>
</div>
<?=render_pagination($pg,'/admin/users.php'.($search?"?search=".urlencode($search):''))?>
<?php endif;?>
<?php require_once __DIR__.'/layout_footer.php';?>
