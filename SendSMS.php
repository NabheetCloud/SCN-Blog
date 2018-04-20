<?php
$request = file_get_contents('php://input');
$json= json_decode($request);
// URL Shortener for Approve
$longURL= "http://<url>/MailApproval.php/".$json->taskid."/Approve";
$url = "https://api-ssl.bitly.com/v3/shorten?access_token=<API Key>&longUrl=".urlencode($longURL);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);   
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
$respApprove = curl_exec($ch);
curl_close($ch);

// URL Shortener for Reject
$longURL= "http://<url>/MailApproval.php/".$json->taskid."/Reject";
$url = "https://api-ssl.bitly.com/v3/shorten?access_token=<API Key>&longUrl=".urlencode($longURL);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);      
$respReject = curl_exec($ch);
curl_close($ch);
$respRejectJson = json_decode($respReject);
$respApproveJson = json_decode($respApprove);

// Send SMS API$context = array();
$context["message"] = $json->message ." Approve-> ".$respApproveJson->data->url . ' Reject-> ' . $respRejectJson->data->url;
$ch1 = curl_init('https://sandbox.api.sap.com/proximusenco/sms/outboundmessages');
$request_headers = array();
$request_headers[] = 'Content-Type: application/json';
$request_headers[] = 'Accept: application/json';
$request_headers[] = 'APIKey: <API Key>';
curl_setopt($ch1, CURLOPT_HTTPHEADER, $request_headers );
curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch1, CURLOPT_VERBOSE, 1);
curl_setopt($ch1, CURLOPT_HEADER, 1);

$context["binary"] = false;
$context["destinations"] = [$json->destinations[0]];
curl_setopt($ch1, CURLOPT_POSTFIELDS,json_encode($context)           );
$resp = curl_exec($ch1);
curl_close($ch1);

?>