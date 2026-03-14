<?php
$page_title='Settings';require_once __DIR__.'/layout.php';$db=Database::connect();
if($_SERVER['REQUEST_METHOD']==='POST'&&verify_csrf()){
  $fields=['site_name','site_tagline','site_description','primary_color','secondary_color','contact_email','contact_phone','contact_address','application_fee','currency','facebook_url','twitter_url','instagram_url','linkedin_url','meta_title','meta_description','meta_keywords'];
  foreach($fields as $k){$v=trim($_POST[$k]??'');$db->prepare("INSERT INTO settings(setting_key,setting_value,updated_at)VALUES(?,?,NOW()) ON DUPLICATE KEY UPDATE setting_value=?,updated_at=NOW()")->execute([$k,$v,$v]);}
  if(!empty($_FILES['logo']['name'])){$fn=upload_file($_FILES['logo'],__DIR__.'/../uploads/logos',['jpg','jpeg','png','svg','webp']);if($fn)$db->prepare("INSERT INTO settings(setting_key,setting_value,updated_at)VALUES('logo',?,NOW()) ON DUPLICATE KEY UPDATE setting_value=?,updated_at=NOW()")->execute([$fn,$fn]);}
  set_flash('success','Settings saved!');header('Location:/admin/settings.php');exit;
}
$all=$db->query("SELECT setting_key,setting_value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);$g=function($k,$d='')use($all){return $all[$k]??$d;};
?>
<form method="POST" enctype="multipart/form-data" class="space-y-6"><?=csrf_field()?>
  <div class="bg-white rounded-2xl border border-gray-100 p-6"><h3 class="font-bold mb-5 font-heading">General</h3>
    <div class="grid sm:grid-cols-2 gap-4">
      <div><label class="text-sm font-semibold mb-1 block">Site Name</label><input type="text" name="site_name" value="<?=e($g('site_name','Fpesa'))?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
      <div><label class="text-sm font-semibold mb-1 block">Tagline</label><input type="text" name="site_tagline" value="<?=e($g('site_tagline'))?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
      <div class="sm:col-span-2"><label class="text-sm font-semibold mb-1 block">Description</label><textarea name="site_description" rows="2" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"><?=e($g('site_description'))?></textarea></div>
      <div><label class="text-sm font-semibold mb-1 block">Currency</label><input type="text" name="currency" value="<?=e($g('currency','KSH'))?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
      <div><label class="text-sm font-semibold mb-1 block">Application Fee</label><input type="number" name="application_fee" value="<?=e($g('application_fee','200'))?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
    </div></div>
  <div class="bg-white rounded-2xl border border-gray-100 p-6"><h3 class="font-bold mb-5 font-heading">Appearance</h3>
    <div class="grid sm:grid-cols-3 gap-4">
      <div><label class="text-sm font-semibold mb-1 block">Primary Color</label><div class="flex gap-2"><input type="color" name="primary_color" value="<?=e($g('primary_color','#0D6B3F'))?>" class="w-10 h-10 rounded-lg border-0 cursor-pointer"><span class="self-center text-sm text-gray-500"><?=e($g('primary_color','#0D6B3F'))?></span></div></div>
      <div><label class="text-sm font-semibold mb-1 block">Secondary Color</label><div class="flex gap-2"><input type="color" name="secondary_color" value="<?=e($g('secondary_color','#F59E0B'))?>" class="w-10 h-10 rounded-lg border-0 cursor-pointer"><span class="self-center text-sm text-gray-500"><?=e($g('secondary_color','#F59E0B'))?></span></div></div>
      <div><label class="text-sm font-semibold mb-1 block">Logo</label><input type="file" name="logo" accept="image/*" class="finput w-full py-2 px-4 rounded-xl border border-gray-200 text-sm"><?php if($g('logo')):?><img src="/uploads/logos/<?=e($g('logo'))?>" class="h-8 mt-2"><?php endif;?></div>
    </div></div>
  <div class="bg-white rounded-2xl border border-gray-100 p-6"><h3 class="font-bold mb-5 font-heading">Contact</h3>
    <div class="grid sm:grid-cols-3 gap-4">
      <div><label class="text-sm font-semibold mb-1 block">Email</label><input type="email" name="contact_email" value="<?=e($g('contact_email'))?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
      <div><label class="text-sm font-semibold mb-1 block">Phone</label><input type="text" name="contact_phone" value="<?=e($g('contact_phone'))?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
      <div><label class="text-sm font-semibold mb-1 block">Address</label><input type="text" name="contact_address" value="<?=e($g('contact_address'))?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
    </div></div>
  <div class="bg-white rounded-2xl border border-gray-100 p-6"><h3 class="font-bold mb-5 font-heading">Social</h3>
    <div class="grid sm:grid-cols-2 gap-4"><?php foreach(['facebook'=>'Facebook','twitter'=>'Twitter/X','instagram'=>'Instagram','linkedin'=>'LinkedIn'] as $k=>$v):?>
      <div><label class="text-sm font-semibold mb-1 block"><?=$v?></label><input type="url" name="<?=$k?>_url" value="<?=e($g($k.'_url','#'))?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div><?php endforeach;?></div></div>
  <div class="bg-white rounded-2xl border border-gray-100 p-6"><h3 class="font-bold mb-5 font-heading">SEO</h3>
    <div class="space-y-4">
      <div><label class="text-sm font-semibold mb-1 block">Meta Title</label><input type="text" name="meta_title" value="<?=e($g('meta_title'))?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
      <div><label class="text-sm font-semibold mb-1 block">Meta Description</label><textarea name="meta_description" rows="2" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"><?=e($g('meta_description'))?></textarea></div>
      <div><label class="text-sm font-semibold mb-1 block">Keywords</label><input type="text" name="meta_keywords" value="<?=e($g('meta_keywords'))?>" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
    </div></div>
  <button type="submit" class="btn-primary px-8 py-3 rounded-xl text-sm font-bold">Save All Settings</button>
</form>
<?php require_once __DIR__.'/layout_footer.php';?>
