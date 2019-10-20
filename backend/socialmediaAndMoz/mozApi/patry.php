<?php
//while ($row = mysqli_fetch_assoc($result_select))
//{
try {

	//proxy details
	$ip = '127.0.0.1';
	$port = '9051';
	$auth = 'password';
	$command = 'signal NEWNYM';
	$fp = fsockopen($ip,$port,$error_number,$err_string,10);
	if(!$fp) { echo "ERROR: $error_number : $err_string"; }
	fwrite($fp,"AUTHENTICATE \"".$auth."\"\n");
	//fwrite($fp, $command."\n");

	//$objectURL= $row["item_url"];
	//$objectURL="https://www.youtube.com/watch?v=b5qZVk0F_yg";
	//sleep(11);
	$objectURL = "http://www.thehindu.com/features/magazine/keeping-the-thriller-alive/article7332623.ece";
	$accessID = "mozscape-c375b8f08b";
	$secretKey = "ecd0d78df20c04c9e6aae46f6174ed66";
	// Set your expires times for several minutes into the future.
	// An expires time excessively far in the future will not be honored by the Mozscape API.
	$expires = time() + 300;
	// Put each parameter on a new line.
	$stringToSign = $accessID."\n".$expires;

	$binarySignature = hash_hmac('sha1', $stringToSign, $secretKey, true);

	$urlSafeSignature = urlencode(base64_encode($binarySignature));

	//cols has value for page authority
	$cols = "34359738368";

	$requestUrl = "http://lsapi.seomoz.com/linkscape/url-metrics/".urlencode($objectURL)."?Cols=".$cols."&AccessID=".$accessID."&Expires=".$expires."&Signature=".$urlSafeSignature;


	$curl = curl_init($requestUrl);
	curl_setopt($curl, CURLOPT_URL, $requestUrl);
	curl_setopt($curl, CURLOPT_PROXY, "127.0.0.1:9050");
	curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_VERBOSE, 0);
	curl_setopt($curl,CURLOPT_COOKIESESSION,1);
	curl_setopt($curl,CURLOPT_FRESH_CONNECT,1);
	$content = curl_exec($curl);
	curl_close($curl);
	$json = json_decode($content, true);
	print_r($json);

	if (!is_null($json)) {
		$pa=$json['upa'];
	} else {
		echo "json is null";
	}

	echo $pa;
} catch (Exception $e1) {
	echo 'Caught exception: ',  $e1->getMessage(), "\n";
}
//}
