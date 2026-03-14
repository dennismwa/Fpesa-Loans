<?php
require_once __DIR__.'/../config/helpers.php';
header('Content-Type: application/json');
$act=$_GET['action']??$_POST['action']??'';

switch($act){
  case 'calculate':
    $a=(float)($_GET['amount']??0);$r=(float)($_GET['rate']??14);$m=(int)($_GET['months']??12);
    if($a<=0||$m<=0){echo json_encode(['error'=>'Invalid']);break;}
    $c=calculate_emi($a,$r,$m);
    echo json_encode(['success'=>true,'emi'=>$c['emi'],'total'=>$c['total_payment'],'interest'=>$c['total_interest']]);
    break;
  case 'loan_types':
    $db=Database::connect();$t=$db->query("SELECT id,name,slug,icon,min_amount,max_amount,interest_rate,min_term,max_term FROM loan_types WHERE is_active=1 ORDER BY sort_order")->fetchAll();
    echo json_encode(['success'=>true,'types'=>$t]);break;
  case 'notifications':
    if(!is_logged_in()){echo json_encode(['count'=>0]);break;}
    $db=Database::connect();$s=$db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0");$s->execute([$_SESSION['user_id']]);
    echo json_encode(['count'=>(int)$s->fetchColumn()]);break;
  case 'mark_read':
    if(!is_logged_in()){echo json_encode(['ok'=>false]);break;}
    $db=Database::connect();$db->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?")->execute([$_SESSION['user_id']]);
    echo json_encode(['ok'=>true]);break;
  default:echo json_encode(['error'=>'Unknown action']);
}
