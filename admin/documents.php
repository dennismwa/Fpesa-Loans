<?php
$page_title='Documents';require_once __DIR__.'/layout.php';$db=Database::connect();
if($_SERVER['REQUEST_METHOD']==='POST'&&verify_csrf()){$did=(int)($_POST['doc_id']??0);$db->prepare("DELETE FROM documents WHERE id=?")->execute([$did]);set_flash('success','Deleted.');header('Location:/admin/documents.php');exit;}
$docs=$db->query("SELECT d.*,u.full_name,l.loan_number FROM documents d JOIN users u ON d.user_id=u.id LEFT JOIN loans l ON d.loan_id=l.id ORDER BY d.created_at DESC")->fetchAll();
?>
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden"><div class="p-5 border-b border-gray-100"><h3 class="font-bold font-heading">All Documents (<?=count($docs)?>)</h3></div>
<?php if(empty($docs)):?><p class="text-sm text-gray-400 text-center py-12">No documents</p>
<?php else:?><div class="overflow-x-auto"><table class="dtable w-full text-sm"><thead><tr><th class="px-4 py-3 text-left">Title</th><th class="px-4 py-3 text-left">User</th><th class="px-4 py-3 text-left">Loan</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-left">Date</th><th class="px-4 py-3 text-center">Actions</th></tr></thead><tbody>
<?php foreach($docs as $d):?>
<tr class="border-t border-gray-50"><td class="px-4 py-3 font-medium"><?=e($d['title']?:$d['filename'])?></td><td class="px-4 py-3"><?=e($d['full_name'])?></td><td class="px-4 py-3 text-xs"><?=$d['loan_number']?'#'.e($d['loan_number']):'—'?></td><td class="px-4 py-3 text-xs capitalize"><?=str_replace('_',' ',$d['type'])?></td><td class="px-4 py-3 text-xs text-gray-400"><?=fmt_date($d['created_at'])?></td>
<td class="px-4 py-3 text-center"><div class="flex items-center justify-center gap-1"><?php if($d['file_path']!=='generated'):?><a href="/<?=e($d['file_path'])?>" target="_blank" download class="p-1.5 rounded-lg hover:bg-primary/10 text-primary"><i data-lucide="download" class="w-4 h-4"></i></a><?php endif;?>
<form method="POST" class="inline" onsubmit="return confirm('Delete?')"><?=csrf_field()?><input type="hidden" name="doc_id" value="<?=$d['id']?>"><button class="p-1.5 rounded-lg hover:bg-red-50 text-red-400"><i data-lucide="trash-2" class="w-4 h-4"></i></button></form></div></td></tr>
<?php endforeach;?></tbody></table></div><?php endif;?></div>
<?php require_once __DIR__.'/layout_footer.php';?>
