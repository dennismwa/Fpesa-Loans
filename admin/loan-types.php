<?php
$page_title = 'Loan Types';
require_once __DIR__ . '/layout.php';
$db = Database::connect();

if ($_SERVER['REQUEST_METHOD']==='POST' && verify_csrf()) {
    $act=$_POST['action']??''; $id=(int)($_POST['type_id']??0);
    $name=trim($_POST['name']??''); $slug=strtolower(preg_replace('/[^a-z0-9]+/i','-',$name));
    $desc=trim($_POST['description']??''); $icon=trim($_POST['icon']??'briefcase');
    $mina=(float)($_POST['min_amount']??1000); $maxa=(float)($_POST['max_amount']??1000000);
    $rate=(float)($_POST['interest_rate']??12); $mint=(int)($_POST['min_term']??1); $maxt=(int)($_POST['max_term']??36);
    $active=isset($_POST['is_active'])?1:0; $sort=(int)($_POST['sort_order']??0);

    if ($act==='add'&&$name) { $db->prepare("INSERT INTO loan_types (name,slug,description,icon,min_amount,max_amount,interest_rate,min_term,max_term,is_active,sort_order,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW())")->execute([$name,$slug,$desc,$icon,$mina,$maxa,$rate,$mint,$maxt,$active,$sort]); set_flash('success','Added.'); }
    elseif ($act==='edit'&&$id) { $db->prepare("UPDATE loan_types SET name=?,slug=?,description=?,icon=?,min_amount=?,max_amount=?,interest_rate=?,min_term=?,max_term=?,is_active=?,sort_order=?,updated_at=NOW() WHERE id=?")->execute([$name,$slug,$desc,$icon,$mina,$maxa,$rate,$mint,$maxt,$active,$sort,$id]); set_flash('success','Updated.'); }
    elseif ($act==='delete'&&$id) { $db->prepare("DELETE FROM loan_types WHERE id=?")->execute([$id]); set_flash('success','Deleted.'); }
    header('Location: /admin/loan-types.php'); exit;
}

$types=$db->query("SELECT * FROM loan_types ORDER BY sort_order,id")->fetchAll();
$eid=isset($_GET['edit'])?(int)$_GET['edit']:0; $ed=null;
if($eid){$st=$db->prepare("SELECT * FROM loan_types WHERE id=?");$st->execute([$eid]);$ed=$st->fetch();}
?>
<div class="grid lg:grid-cols-3 gap-6">
  <div class="bg-white rounded-2xl border border-gray-100 p-6 h-fit">
    <h3 class="font-bold mb-5 font-heading"><?=$ed?'Edit':'Add'?> Loan Type</h3>
    <form method="POST" class="space-y-4"><?=csrf_field()?>
      <input type="hidden" name="action" value="<?=$ed?'edit':'add'?>">
      <?php if($ed):?><input type="hidden" name="type_id" value="<?=$ed['id']?>"><?php endif;?>
      <div><label class="text-sm font-semibold mb-1 block">Name</label><input type="text" name="name" required value="<?=e($ed['name']??'')?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
      <div><label class="text-sm font-semibold mb-1 block">Description</label><textarea name="description" rows="3" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"><?=e($ed['description']??'')?></textarea></div>
      <div><label class="text-sm font-semibold mb-1 block">Icon (lucide)</label><input type="text" name="icon" value="<?=e($ed['icon']??'briefcase')?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="text-sm font-semibold mb-1 block">Min Amount</label><input type="number" name="min_amount" value="<?=$ed['min_amount']??1000?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
        <div><label class="text-sm font-semibold mb-1 block">Max Amount</label><input type="number" name="max_amount" value="<?=$ed['max_amount']??1000000?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
      </div>
      <div><label class="text-sm font-semibold mb-1 block">Interest Rate %</label><input type="number" step="0.5" name="interest_rate" value="<?=$ed['interest_rate']??12?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="text-sm font-semibold mb-1 block">Min Term (m)</label><input type="number" name="min_term" value="<?=$ed['min_term']??1?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
        <div><label class="text-sm font-semibold mb-1 block">Max Term (m)</label><input type="number" name="max_term" value="<?=$ed['max_term']??36?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
      </div>
      <div><label class="text-sm font-semibold mb-1 block">Sort Order</label><input type="number" name="sort_order" value="<?=$ed['sort_order']??0?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
      <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" <?=($ed['is_active']??1)?'checked':''?> class="rounded border-gray-300 text-primary"><span class="text-sm font-medium">Active</span></label>
      <div class="flex gap-2"><button class="btn-primary px-6 py-2.5 rounded-xl text-sm font-bold"><?=$ed?'Update':'Add'?></button><?php if($ed):?><a href="/admin/loan-types.php" class="px-6 py-2.5 rounded-xl text-sm font-bold border border-gray-200 hover:bg-gray-50">Cancel</a><?php endif;?></div>
    </form>
  </div>
  <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100"><h3 class="font-bold font-heading">All Types (<?=count($types)?>)</h3></div>
    <div class="overflow-x-auto"><table class="dtable w-full text-sm"><thead><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-right">Range</th><th class="px-4 py-3 text-center">Rate</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-center">Actions</th></tr></thead><tbody>
    <?php foreach($types as $t):?>
    <tr class="border-t border-gray-50">
      <td class="px-4 py-3"><div class="flex items-center gap-2"><div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary"><i data-lucide="<?=$t['icon']?>" class="w-4 h-4"></i></div><span class="font-medium"><?=e($t['name'])?></span></div></td>
      <td class="px-4 py-3 text-right text-xs"><?=fmt_money($t['min_amount'])?> — <?=fmt_money($t['max_amount'])?></td>
      <td class="px-4 py-3 text-center"><?=$t['interest_rate']?>%</td>
      <td class="px-4 py-3 text-center"><?=status_badge($t['is_active']?'active':'rejected')?></td>
      <td class="px-4 py-3 text-center"><div class="flex items-center justify-center gap-1">
        <a href="/admin/loan-types.php?edit=<?=$t['id']?>" class="p-1.5 rounded-lg hover:bg-primary/10 text-primary"><i data-lucide="edit-3" class="w-4 h-4"></i></a>
        <form method="POST" class="inline" onsubmit="return confirm('Delete?')"><?=csrf_field()?><input type="hidden" name="action" value="delete"><input type="hidden" name="type_id" value="<?=$t['id']?>"><button class="p-1.5 rounded-lg hover:bg-red-50 text-red-400"><i data-lucide="trash-2" class="w-4 h-4"></i></button></form>
      </div></td>
    </tr>
    <?php endforeach;?></tbody></table></div>
  </div>
</div>
<?php require_once __DIR__.'/layout_footer.php';?>
