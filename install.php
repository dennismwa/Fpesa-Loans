<?php
/**
 * Fpesa Database Installer - Run once, then DELETE this file!
 */
$host='localhost';$dbname='vxjtgclw_loans';$user='vxjtgclw_loans';$pass='?zzbH8geE5$F{(gL';
echo '<!DOCTYPE html><html><head><title>Fpesa Install</title><script src="https://cdn.tailwindcss.com"></script><link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700&display=swap" rel="stylesheet"></head>';
echo '<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4"><div class="bg-white rounded-2xl shadow-xl p-8 max-w-lg w-full">';
echo '<h1 class="text-2xl font-bold mb-6" style="font-family:Outfit">Fpesa Database Installer</h1>';
try{
  $pdo=new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
  echo '<p class="text-emerald-600 mb-2">✓ Database connected</p>';
  $sql=file_get_contents(__DIR__.'/database.sql');
  if(!$sql){echo '<p class="text-red-600">✗ database.sql not found!</p>';}
  else{
    $stmts=array_filter(array_map('trim',preg_split('/;\s*\n/',str_replace(["\r\n","\r"],"\n",$sql))));
    $ok=0;$er=0;
    foreach($stmts as $st){$st=trim($st);if(!$st||$st==='COMMIT'||strpos($st,'SET ')===0||strpos($st,'START')===0)continue;
      try{$pdo->exec($st);$ok++;}catch(PDOException $e){if(strpos($e->getMessage(),'already exists')===false&&strpos($e->getMessage(),'Duplicate')===false){echo '<p class="text-amber-600 text-xs">⚠ '.htmlspecialchars(substr($e->getMessage(),0,120)).'</p>';$er++;}}}
    echo "<p class='text-emerald-600'>✓ $ok statements executed</p>";
    if($er)echo "<p class='text-amber-600'>⚠ $er warnings</p>";
    $tbls=$pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo '<p class="text-blue-600 mb-4">✓ '.count($tbls).' tables: '.implode(', ',$tbls).'</p>';
    $adm=$pdo->query("SELECT email FROM admins LIMIT 1")->fetch();
    if($adm)echo '<div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-4"><p class="font-bold text-emerald-800">✓ Installation Complete!</p><p class="text-sm text-emerald-700 mt-1">Admin: <strong>'.htmlspecialchars($adm['email']).'</strong> / <strong>password</strong></p></div>';
    echo '<div class="bg-red-50 border border-red-200 rounded-xl p-4"><p class="text-red-800 font-bold text-sm">⚠ DELETE install.php and database.sql NOW!</p></div>';
  }
}catch(PDOException $e){echo '<p class="text-red-600">✗ '.htmlspecialchars($e->getMessage()).'</p><p class="text-sm text-gray-500 mt-2">Check credentials in config/database.php</p>';}
echo '</div></body></html>';
