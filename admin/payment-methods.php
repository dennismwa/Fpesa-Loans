<?php
$page_title='Payment Methods';require_once __DIR__.'/layout.php';$db=Database::connect();
if($_SERVER['REQUEST_METHOD']==='POST'&&verify_csrf()){
  $act=$_POST['action']??'';$id=(int)($_POST['method_id']??0);$name=trim($_POST['name']??'');$type=$_POST['type']??'manual';
  $acn=trim($_POST['account_number']??'');$acnm=trim($_POST['account_name']??'');$instr=trim($_POST['instructions']??'');
  $active=isset($_POST['is_active'])?1:0;$sort=(int)($_POST['sort_order']??0);
  if($act==='add'&&$name){$db->prepare("INSERT INTO payment_methods(name,type,account_number,account_name,instructions,is_active,sort_order,created_at)VALUES(?,?,?,?,?,?,?,NOW())")->execute([$name,$type,$acn,$acnm,$instr,$active,$sort]);set_flash('success','Added.');}
  elseif($act==='edit'&&$id){$db->prepare("UPDATE payment_methods SET name=?,type=?,account_number=?,account_name=?,instructions=?,is_active=?,sort_order=?,updated_at=NOW() WHERE id=?")->execute([$name,$type,$acn,$acnm,$instr,$active,$sort,$id]);set_flash('success','Updated.');}
  elseif($act==='delete'&&$id){$db->prepare("DELETE FROM payment_methods WHERE id=?")->execute([$id]);set_flash('success','Deleted.');}
  header('Location:/admin/payment-methods.php');exit;
}
$methods=$db->query("SELECT * FROM payment_methods ORDER BY sort_order,id")->fetchAll();
$eid=isset($_GET['edit'])?(int)$_GET['edit']:0;$ed=null;if($eid){$st=$db->prepare("SELECT * FROM payment_methods WHERE id=?");$st->execute([$eid]);$ed=$st->fetch();}
?>
<div class="grid lg:grid-cols-3 gap-6">
  <div class="bg-white rounded-2xl border border-gray-100 p-6 h-fit"><h3 class="font-bold mb-5 font-heading"><?=$ed?'Edit':'Add'?> Method</h3>
    <form method="POST" class="space-y-4"><?=csrf_field()?><input type="hidden" name="action" value="<?=$ed?'edit':'add'?>"><?php if($ed):?><input type="hidden" name="method_id" value="<?=$ed['id']?>"><?php endif;?>
      <div><label class="text-sm font-semibold mb-1 block">Name</label><input type="text" name="name" required value="<?=e($ed['name']??'')?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm" placeholder="M-Pesa Paybill"></div>
      <div><label class="text-sm font-semibold mb-1 block">Type</label><select name="type" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"><?php foreach(['paybill'=>'Paybill','till'=>'Till','bank'=>'Bank','mpesa_api'=>'M-Pesa API','cash'=>'Cash','manual'=>'Manual'] as $k=>$v):?><option value="<?=$k?>" <?=($ed['type']??'')===$k?'selected':''?>><?=$v?></option><?php endforeach;?></select></div>
      <div><label class="text-sm font-semibold mb-1 block">Account/Till #</label><input type="text" name="account_number" value="<?=e($ed['account_number']??'')?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
      <div><label class="text-sm font-semibold mb-1 block">Account Name</label><input type="text" name="account_name" value="<?=e($ed['account_name']??'')?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
      <div><label class="text-sm font-semibold mb-1 block">Instructions</label><textarea name="instructions" rows="3" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"><?=e($ed['instructions']??'')?></textarea></div>
      <div><label class="text-sm font-semibold mb-1 block">Sort</label><input type="number" name="sort_order" value="<?=$ed['sort_order']??0?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
      <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" <?=($ed['is_active']??1)?'checked':''?> class="rounded border-gray-300 text-primary"><span class="text-sm">Active</span></label>
      <div class="flex gap-2"><button class="btn-primary px-6 py-2.5 rounded-xl text-sm font-bold"><?=$ed?'Update':'Add'?></button><?php if($ed):?><a href="/admin/payment-methods.php" class="px-6 py-2.5 rounded-xl text-sm font-bold border hover:bg-gray-50">Cancel</a><?php endif;?></div>
    </form></div>
  <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 overflow-hidden"><div class="p-5 border-b border-gray-100"><h3 class="font-bold font-heading">Methods (<?=count($methods)?>)</h3></div>
    <div class="divide-y divide-gray-50"><?php foreach($methods as $m):?>
    <div class="flex items-center justify-between px-5 py-4 hover:bg-gray-50">
      <div class="flex items-center gap-3"><div class="w-10 h-10 rounded-xl <?=$m['is_active']?'bg-primary/10 text-primary':'bg-gray-100 text-gray-400'?> flex items-center justify-center"><i data-lucide="credit-card" class="w-5 h-5"></i></div>
        <div><div class="font-medium text-sm"><?=e($m['name'])?></div><div class="text-xs text-gray-400"><?=ucfirst(str_replace('_',' ',$m['type']))?> <?=$m['account_number']?'• '.e($m['account_number']):''?></div></div></div>
      <div class="flex items-center gap-2"><?=status_badge($m['is_active']?'active':'rejected')?>
        <a href="/admin/payment-methods.php?edit=<?=$m['id']?>" class="p-1.5 rounded-lg hover:bg-primary/10 text-primary"><i data-lucide="edit-3" class="w-4 h-4"></i></a>
        <form method="POST" class="inline" onsubmit="return confirm('Delete?')"><?=csrf_field()?><input type="hidden" name="action" value="delete"><input type="hidden" name="method_id" value="<?=$m['id']?>"><button class="p-1.5 rounded-lg hover:bg-red-50 text-red-400"><i data-lucide="trash-2" class="w-4 h-4"></i></button></form></div>
    </div><?php endforeach;?></div></div>
</div>
<?php require_once __DIR__.'/layout_footer.php';?>
