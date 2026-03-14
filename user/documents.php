<?php
$page_title = 'Documents';
require_once __DIR__ . '/layout.php';
$db = Database::connect();
$uid = $_u['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $lid = (int)($_POST['loan_id'] ?? 0);
    if (!empty($_FILES['signed_doc']['name']) && $lid) {
        $fn = upload_file($_FILES['signed_doc'], __DIR__ . '/../uploads/agreements', ['jpg','jpeg','png','pdf']);
        if ($fn) {
            $db->prepare("INSERT INTO documents (user_id,loan_id,type,title,filename,file_path,file_size,uploaded_by,created_at) VALUES (?,?,'signed_agreement','Signed Agreement',?,?,?,'user',NOW())")
                ->execute([$uid, $lid, $fn, 'uploads/agreements/'.$fn, $_FILES['signed_doc']['size']]);
            set_flash('success', 'Document uploaded successfully!');
        } else { set_flash('error', 'Upload failed. Check file type/size (max 10MB).'); }
    }
    header('Location: /user/documents.php'); exit;
}

$docs = $db->prepare("SELECT d.*, l.loan_number FROM documents d LEFT JOIN loans l ON d.loan_id=l.id WHERE d.user_id=? ORDER BY d.created_at DESC");
$docs->execute([$uid]); $docs = $docs->fetchAll();

$user_loans = $db->prepare("SELECT id, loan_number FROM loans WHERE user_id=? AND status='active'");
$user_loans->execute([$uid]); $user_loans = $user_loans->fetchAll();
?>

<?php if (!empty($user_loans)): ?>
<div class="bg-white rounded-2xl border border-gray-100 p-6 mb-8" data-aos="fade-up">
  <h3 class="font-bold mb-4 font-heading">Upload Signed Agreement</h3>
  <form method="POST" enctype="multipart/form-data" class="flex flex-wrap gap-4 items-end">
    <?= csrf_field() ?>
    <div class="flex-1 min-w-[180px]"><label class="text-sm font-semibold mb-1.5 block">Loan</label>
      <select name="loan_id" required class="finput w-full py-3 px-4 rounded-xl border border-gray-200 text-sm">
        <?php foreach ($user_loans as $l): ?><option value="<?= $l['id'] ?>">#<?= e($l['loan_number']) ?></option><?php endforeach; ?>
      </select></div>
    <div class="flex-1 min-w-[180px]"><label class="text-sm font-semibold mb-1.5 block">Document (PDF/Image)</label>
      <input type="file" name="signed_doc" required accept=".pdf,.jpg,.jpeg,.png" class="finput w-full py-2.5 px-4 rounded-xl border border-gray-200 text-sm"></div>
    <button type="submit" class="btn-primary px-6 py-3 rounded-xl text-sm font-bold">Upload</button>
  </form>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden" data-aos="fade-up">
  <div class="p-5 border-b border-gray-100"><h3 class="font-bold font-heading">My Documents</h3></div>
  <?php if (empty($docs)): ?><p class="text-sm text-gray-400 text-center py-12">No documents yet</p>
  <?php else: ?><div class="divide-y divide-gray-50">
    <?php foreach ($docs as $d): ?>
    <div class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center"><i data-lucide="file-text" class="w-5 h-5 text-primary"></i></div>
        <div>
          <div class="text-sm font-medium"><?= e($d['title'] ?: ucfirst(str_replace('_',' ',$d['type']))) ?></div>
          <div class="text-xs text-gray-400"><?= $d['loan_number']?'#'.e($d['loan_number']).' &bull; ':'' ?><?= fmt_date($d['created_at']) ?> &bull; <?= ucfirst($d['uploaded_by']) ?></div>
        </div>
      </div>
      <?php if ($d['file_path'] !== 'generated'): ?>
      <a href="/<?= e($d['file_path']) ?>" target="_blank" download class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary/10 text-primary text-xs font-bold hover:bg-primary/20 transition">
        <i data-lucide="download" class="w-3.5 h-3.5"></i> Download
      </a>
      <?php else: ?><span class="text-xs text-gray-400">System generated</span><?php endif; ?>
    </div>
    <?php endforeach; ?></div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
