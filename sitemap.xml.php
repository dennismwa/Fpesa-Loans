<?php
require_once __DIR__.'/config/helpers.php';
header('Content-Type: application/xml; charset=utf-8');
$host=(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']==='on'?'https':'http').'://'.$_SERVER['HTTP_HOST'];
$db=Database::connect();$types=$db->query("SELECT slug,updated_at FROM loan_types WHERE is_active=1")->fetchAll();
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url><loc><?=$host?>/</loc><changefreq>daily</changefreq><priority>1.0</priority></url>
<url><loc><?=$host?>/pages/loans.php</loc><changefreq>weekly</changefreq><priority>0.9</priority></url>
<url><loc><?=$host?>/pages/loan-calculator.php</loc><changefreq>monthly</changefreq><priority>0.8</priority></url>
<url><loc><?=$host?>/pages/apply-loan.php</loc><changefreq>weekly</changefreq><priority>0.9</priority></url>
<url><loc><?=$host?>/pages/about.php</loc><changefreq>monthly</changefreq><priority>0.7</priority></url>
<url><loc><?=$host?>/pages/contact.php</loc><changefreq>monthly</changefreq><priority>0.7</priority></url>
<url><loc><?=$host?>/auth/login.php</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>
<url><loc><?=$host?>/auth/register.php</loc><changefreq>monthly</changefreq><priority>0.6</priority></url>
<?php foreach($types as $t):?>
<url><loc><?=$host?>/pages/loan-details.php?slug=<?=$t['slug']?></loc><lastmod><?=date('Y-m-d',strtotime($t['updated_at']))?></lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>
<?php endforeach;?>
</urlset>
