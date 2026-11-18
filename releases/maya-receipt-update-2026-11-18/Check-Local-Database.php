<?php
// CLI-only, read-only local schema check. Never includes/evaluates the config files.
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
if(!function_exists('token_get_all')||!class_exists('mysqli')){fwrite(STDERR,"Use XAMPP PHP with mysqli and tokenizer enabled. No files or database records were changed.\n");exit(1);}
$root=$argv[1]??'';
if(!is_dir($root)){fwrite(STDERR,"Provide the existing Vehicle-Service-System directory.\n");exit(1);}
$required=[
 'job_schedule_events'=>['id'],'customers'=>['user_id'],'vehicles'=>['user_id'],
 'reservations'=>['user_id','checked_in_at','scheduled_duration_minutes','booking_channel','original_duration_minutes','original_appointment_at'],
 'users'=>['staff_level','mechanic_id'],'service_categories'=>['id'],'service_catalog'=>['id'],
 'reservation_services'=>['id'],'payments'=>['payment_type'],
];
function configLiterals(string $file):array{
 $tokens=token_get_all(file_get_contents($file));$values=[];$wanted=['host','user','username','password','database'];
 for($i=0;$i<count($tokens);$i++){
  $t=$tokens[$i];if(!is_array($t)||$t[0]!==T_VARIABLE)continue;$name=substr($t[1],1);if(!in_array($name,$wanted,true))continue;
  $j=$i+1;while(isset($tokens[$j])&&is_array($tokens[$j])&&$tokens[$j][0]===T_WHITESPACE)$j++;
  if(($tokens[$j]??null)!=='=')continue;$j++;
  while(isset($tokens[$j])&&is_array($tokens[$j])&&$tokens[$j][0]===T_WHITESPACE)$j++;
  $v=$tokens[$j]??null;if(!is_array($v)||$v[0]!==T_CONSTANT_ENCAPSED_STRING)continue;
  $raw=$v[1];$inside=substr($raw,1,-1);
  $values[$name]=$raw[0]==="'"?str_replace(["\\\\","\\'"],["\\","'"],$inside):stripcslashes($inside);
 }
 return $values;
}
echo "READ-ONLY CHECK: config database names and schema columns only. No account records/passwords are printed.\n";
$usedDatabases=[];$inventoryConnection=null;
foreach(['vehicle-service-management/includes/config.php','vehicle-service-management/api/config/database.php','vehicle-service-management-admin/includes/config.php'] as $relative){
 echo "\n[$relative]\n";$file=rtrim($root,'/\\').'/'.$relative;
 if(!is_file($file)){echo "Config file missing.\n";continue;}
 $v=configLiterals($file);
 if(!isset($v['host'],$v['database'],$v['password'])||!isset($v['username'])&&!isset($v['user'])){echo "Config uses settings that this literal-only checker cannot read. Inspect its database name locally. Do not send the full config file.\n";continue;}
 echo 'Database: '.$v['database']."\n";
 $usedDatabases[$v['database']]=true;
 if(!in_array(strtolower($v['host']),['localhost','127.0.0.1','::1'],true)){echo "Remote host skipped; this tool checks local XAMPP only.\n";continue;}
 try{
  $conn=new mysqli($v['host'],$v['username']??$v['user'],$v['password'],$v['database']);
  $r=$conn->query('SELECT TABLE_NAME,COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()');$found=[];
  while($row=$r->fetch_assoc())$found[$row['TABLE_NAME']][$row['COLUMN_NAME']]=true;
  $missing=[];foreach($required as $table=>$columns)foreach($columns as $column)if(!isset($found[$table][$column]))$missing[]=$table.'.'.$column;
  if(!$missing)echo "Schema check: READY (all columns required by the website guard exist).\n";
  else{echo 'Schema check: MISSING '.count($missing)." required columns/table fields:\n";foreach($missing as $name)echo ' - '.$name."\n";}
  if($inventoryConnection===null)$inventoryConnection=$conn;else $conn->close();
 }catch(Throwable $e){echo "Could not connect/read the configured local database. Check MySQL and your local settings. Credentials were not printed.\n";}
}

if($inventoryConnection){
 echo "\nVISIBLE LOCAL DATABASE INVENTORY (read-only)\n";
 echo "Not referenced by these three configs does NOT prove a database is unused.\n";
 try{
  $r=$inventoryConnection->query("SELECT s.SCHEMA_NAME,COUNT(t.TABLE_NAME) table_count,COALESCE(SUM(t.TABLE_ROWS),0) approximate_rows,COALESCE(SUM(t.DATA_LENGTH+t.INDEX_LENGTH),0) bytes_used FROM information_schema.SCHEMATA s LEFT JOIN information_schema.TABLES t ON t.TABLE_SCHEMA=s.SCHEMA_NAME GROUP BY s.SCHEMA_NAME ORDER BY s.SCHEMA_NAME");
  $visible=[];
  while($row=$r->fetch_assoc()){
   $db=$row['SCHEMA_NAME'];$visible[$db]=true;
   $system=in_array(strtolower($db),['mysql','information_schema','performance_schema','sys','phpmyadmin'],true);
   $label=$system?'SYSTEM / KEEP':(isset($usedDatabases[$db])?'CURRENT CONFIG / KEEP':'NOT REFERENCED BY THESE CONFIGS / REVIEW ONLY');
   echo json_encode($db).': '.$label.'; tables/views='.$row['table_count'].'; approximate rows='.$row['approximate_rows'].'; bytes='.$row['bytes_used']."\n";
  }
  $sandbox='vehicle_service_system_sandbox';
  if(isset($usedDatabases[$sandbox]))echo "\nvehicle_service_system_sandbox is referenced by current config. It may contain current bookings/payments: DO NOT DELETE IT.\n";
  elseif(isset($visible[$sandbox]))echo "\nvehicle_service_system_sandbox is not referenced by these configs. Export a backup and compare its data before deciding whether it is obsolete.\n";
  else echo "\nvehicle_service_system_sandbox was not found among visible databases.\n";
  foreach(array_keys($usedDatabases) as $db){
   $s=$inventoryConnection->prepare("SELECT COUNT(*) n FROM information_schema.TABLES WHERE TABLE_SCHEMA=? AND TABLE_NAME='payment_gateway_attempts'");$s->bind_param('s',$db);$s->execute();$exists=(int)$s->get_result()->fetch_assoc()['n'];$s->close();
   if(!$exists){echo 'Legacy gateway table in '.json_encode($db).": absent (nothing to remove).\n";continue;}
   $identifier='`'.str_replace('`','``',$db).'`';
   try{
    $counts=$inventoryConnection->query("SELECT COUNT(*) total,COALESCE(SUM(state IN ('creating','pending','unknown','review')),0) unresolved FROM ".$identifier.'.payment_gateway_attempts')->fetch_assoc();
    echo 'Legacy gateway table in '.json_encode($db).': rows='.$counts['total'].'; unresolved='.$counts['unresolved'].".\n";
    if((int)$counts['total']===0)echo "Only the empty gateway TABLE may be a cleanup candidate after backup. The whole database may still be active.\n";
    else echo "Keep gateway rows/history until reviewed and backed up; do not reset unresolved payments.\n";
   }catch(Throwable $e){echo "Could not inspect legacy gateway rows. Keep the table until reviewed.\n";}
  }
 }catch(Throwable $e){echo "Could not list all databases with this account. No cleanup was performed.\n";}
 $inventoryConnection->close();
}
echo "\nNo database changes or deletions were performed.\n";

