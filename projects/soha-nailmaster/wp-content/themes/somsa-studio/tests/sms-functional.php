<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
$_SERVER['REQUEST_SCHEME']='http';$_SERVER['HTTP_HOST']='soha-nailmaster.test';
require dirname(__DIR__,4).'/wp-load.php';
global $wpdb;$bookings=$wpdb->prefix.'somsa_bookings';$queue=$wpdb->prefix.'somsa_sms';$fixture='SOMSA SMS test '.getmypid();$calls=0;$responseKind='success';
foreach(['somsa_sms_enabled'=>'1','somsa_sms_provider'=>'kavenegar','somsa_sms_key'=>'TESTKEYNOTREAL','somsa_sms_sender'=>'100000','somsa_sms_hours'=>3] as $option=>$value)add_filter('pre_option_'.$option,fn()=>$value);
add_filter('pre_http_request',function($pre,$args,$url)use(&$calls,&$responseKind){if(!str_starts_with($url,'https://api.kavenegar.com/'))return $pre;$calls++;if($responseKind==='timeout')return new WP_Error('http_request_failed','mock timeout');$data=$responseKind==='reject'?['return'=>['status'=>401],'entries'=>[]]:['return'=>['status'=>200],'entries'=>[['messageid'=>123456]]];return ['headers'=>[],'body'=>wp_json_encode($data),'response'=>['code'=>200,'message'=>'OK'],'cookies'=>[]];},10,3);
$wpdb->query('START TRANSACTION');register_shutdown_function(fn()=>$wpdb->query('ROLLBACK'));
$make=function($state,$hours)use($wpdb,$bookings,$fixture){$slot=wp_date('Y-m-d H:i:s',time()+$hours*3600);$wpdb->insert($bookings,['token'=>bin2hex(random_bytes(24)),'customer'=>$fixture,'phone'=>'09120000000','service'=>0,'slot'=>$slot,'active_slot'=>null,'deposit'=>150000,'status'=>$state,'created_at'=>current_time('mysql'),'expires_at'=>wp_date('Y-m-d H:i:s',time()+900)]);return $wpdb->insert_id;};
$assert=function($ok,$label){if(!$ok)throw new Exception('FAIL '.$label);echo 'PASS '.$label."\n";};
$due=$make('paid',2);$future=$make('paid',5);$demo=$make('demo',2);$cancel=$make('paid',2);somsa_sms_sync();$wpdb->update($bookings,['status'=>'cancelled'],['id'=>$cancel]);somsa_sms_worker();
$status=fn($id)=>$wpdb->get_var($wpdb->prepare("SELECT status FROM $queue WHERE booking_id=%d",$id));
$assert($status($due)==='sent','reminder within three hours');$assert($status($future)==='queued','future reminder waits');$assert($status($demo)===null,'demo never sends');$assert($status($cancel)==='cancelled','cancelled booking never sends');$oldCalls=$calls;somsa_sms_worker();$assert($oldCalls===$calls,'repeated worker does not duplicate');
$reject=$make('paid',2);$responseKind='reject';somsa_sms_worker();$assert($status($reject)==='failed','provider rejection logged');
$timeout=$make('paid',2);$responseKind='timeout';somsa_sms_worker();$assert($status($timeout)==='unknown','timeout does not pretend delivery');$oldCalls=$calls;somsa_sms_worker();$assert($calls===$oldCalls,'uncertain sends are not duplicated');
$stamp=somsa_sms_slot_timestamp(wp_date('Y-m-d H:i:s',time()+7200));$assert(abs($stamp-time()-7200)<5,'Tehran timezone handled');
$wpdb->query('ROLLBACK');echo "All SMS tests passed; no real SMS sent.\n";
