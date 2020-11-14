<?php
/*
// Ajax plugin 11/2013
//
*/

define ('DEBUG', false);
if(DEBUG == true)
{
//иногда запрещена эта команда и выдает ошибку
//    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}
else
{
//иногда запрещена эта команда и выдает ошибку
//    ini_set('display_errors', 'Off');
    error_reporting(0);
}

//COOKIEJAR 
	$fpn=fopen("COOKIEJAR","w"); 
	fputs($fpn," ");
	fclose($fpn);

$data="";



if (sizeof($_POST)>0) {
		$url=$_POST['proxy_url'];
		$method=$_POST['proxy_method'];
		unset($_POST['proxy_url']);
		unset($_POST['proxy_method']);
// кодируем ибо пройдя через ПОСТ данные автоматом раскодировались
		foreach ($_POST as $p=>$a) $data.= $p.'='.urlencode($a).'&';

	if ($method=='post') {

		ECHO    postdata($url, $data);

	} else {
 		ECHO	file_get_contents_curl($url.'?'.$data);
	}

}  else  {

echo "VERSION 1.1.1
<br>";
$w = stream_get_wrappers();
echo 'openssl: ',  extension_loaded  ('openssl') ? 'yes':'<font color=red><b>no</b></font>', "<br>";
echo 'http wrapper: ', in_array('http', $w) ? 'yes':'<font color=red><b>no</b></font>', "<br>";
echo 'https wrapper: ', in_array('https', $w) ? 'yes':'<font color=red><b>no</b></font>', "<br>";
//echo 'wrappers: ', var_dump($w);

}
/////////////////////////////////////////////////////////////////








function file_get_contents_curl($url) 
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.6 (KHTML, like Gecko) Chrome/16.0.897.0 Safari/535.6');
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "COOKIEJAR");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "COOKIEJAR");
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

 // включим редирект
	$content = curl_redir_exec($ch);
	curl_close($ch);
	return $content;
}


 
// рекурсиваная функция с поддержкой редиректов  на прямую ее не юзать только через //function file_get_contents_curl($url) 
 function curl_redir_exec($ch)  
{  
    static $curl_loops = 0;  
    static $curl_max_loops = 20;  
    if ($curl_loops   >= $curl_max_loops)  
    {  
	    $curl_loops = 0;  
	        return FALSE;  
    }  
     //!!!!!!!!!!!!!!!! не отключать HEADER иначе не сработает тредирект |||by andrey
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "COOKIEJAR");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_COOKIEFILE, "COOKIEJAR");
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.6 (KHTML, like Gecko) Chrome/16.0.897.0 Safari/535.6');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    
    $data = curl_exec($ch);  


    list($header, $data) = explode("nn", $data, 2);  
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);  


    if ($http_code == 301 || $http_code == 302)  
    {  
    $matches = array();  
        preg_match('/Location:(.*?)n/', $header, $matches);  
    $url = @parse_url(trim(array_pop($matches)));  
        if (!$url)  
    {  
        //couldn't process the url to redirect to  
        $curl_loops = 0;  
        return $data;  
        }  
    $last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));  
    if (!$url['scheme'])  
        $url['scheme'] = $last_url['scheme'];  
    if (!$url['host'])  
        $url['host'] = $last_url['host'];  
    if (!$url['path'])  
        $url['path'] = $last_url['path'];  
       $new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query']?'?'.$url['query']:'');  
    curl_setopt($ch, CURLOPT_URL, $new_url);  
//    echo 'Redirecting to: '. $new_url;  
    return curl_redir_exec($ch);  
    }   else {  

            $data = curl_exec($ch);  
            $curl_loops=0;  
            //echo $data;
            return $data;  
        }  
}


function postdata($url, $data) {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "COOKIEJAR");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_COOKIEFILE, "COOKIEJAR");
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.6 (KHTML, like Gecko) Chrome/16.0.897.0 Safari/535.6');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


// in real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));

// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$a=curl_exec ($ch);
	curl_close ($ch);
	return $a;
}

?>