<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
if(!function_exists('token_get_all')||!class_exists('mysqli')){fwrite(STDERR,"Use XAMPP PHP with mysqli and tokenizer.\n");exit(1);}
$root=$argv[1]??'';if(!is_dir($root)){fwrite(STDERR,"Provide your Vehicle-Service-System root.\n");exit(1);}
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
echo "PHP: ".PHP_VERSION."\n";
echo "READ-ONLY AUTH CHECK. No credentials or individual records are printed.\n";
foreach(['vehicle-service-management/includes/config.php','vehicle-service-management/api/config/database.php','vehicle-service-management-admin/includes/config.php'] as $relative){
 echo "\n[$relative]\n";$file=rtrim($root,'/\\').'/'.$relative;
 if(!is_file($file)){echo "Missing file.\n";continue;}$v=configLiterals($file);
 if(!isset($v['host'],$v['database'],$v['password'])||!isset($v['user'])&&!isset($v['username'])){echo "Cannot read dynamic config; inspect locally without sharing secrets.\n";continue;}
 echo 'Database: '.$v['database']."\n";
 if(!in_array(strtolower($v['host']),['localhost','127.0.0.1','::1'],true)){echo "Remote database skipped.\n";continue;}
 try{
 $c=new mysqli($v['host'],$v['username']??$v['user'],$v['password'],$v['database']);
 $duplicates=(int)$c->query('SELECT COUNT(*) n FROM (SELECT user_id FROM customers WHERE user_id IS NOT NULL GROUP BY user_id HAVING COUNT(*)>1) duplicates')->fetch_assoc()['n'];
 echo "Duplicate owned profile groups: $duplicates\n";
 if($duplicates)echo "STOP: export a backup and review duplicates. No row is automatically merged/deleted.\n";
 $r=(int)$c->query("SELECT COUNT(*) n FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('auth_identities','auth_challenges','auth_password_resets','auth_rate_limits')")->fetch_assoc()['n'];
 $columns=(int)$c->query("SELECT COUNT(*) n FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='api_tokens' AND COLUMN_NAME IN ('auth_method','reauthenticated_at')")->fetch_assoc()['n'];
 $index=(int)$c->query("SELECT COUNT(*) n FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='customers' AND INDEX_NAME='auth_customer_user_unique' AND NON_UNIQUE=0")->fetch_assoc()['n'];
 echo "Auth tables: $r/4; token columns: $columns/2; unique profile index: $index/1\n";
 echo $r===4&&$columns===2&&$index===1&&!$duplicates?"AUTH SCHEMA READY\n":"AUTH MIGRATION REQUIRED (do not overwrite the whole database).\n";$c->close();
 }catch(Throwable $e){echo "Could not inspect the local schema. Check MySQL/configuration.\n";}
}
echo "\nNo records, schema or source files were changed.\n";
