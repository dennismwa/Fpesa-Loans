<?php
require_once __DIR__.'/../config/helpers.php';http_response_code(404);$page_title='Page Not Found';
require_once __DIR__.'/../includes/header.php';require_once __DIR__.'/../includes/navbar.php';
?>
<section class="pt-28 pb-20 min-h-[80vh] flex items-center"><div class="max-w-lg mx-auto px-4 text-center">
  <div class="text-8xl font-extrabold grad-text mb-4 font-heading">404</div>
  <h1 class="text-2xl font-bold mb-4 font-heading">Page Not Found</h1>
  <p class="text-gray-500 mb-8">The page you're looking for doesn't exist.</p>
  <a href="/" class="btn-primary px-8 py-3 rounded-xl text-sm font-bold inline-flex items-center gap-2"><i data-lucide="home" class="w-4 h-4"></i> Go Home</a>
</div></section>
<?php require_once __DIR__.'/../includes/footer.php';?>
