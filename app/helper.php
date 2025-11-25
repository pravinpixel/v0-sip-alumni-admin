<?php
use App\Helpers\AccessGuard;

if (!function_exists('access')) {
    function access()
    {
        return new AccessGuard();
    }
}

if (!function_exists('returnError')) {

    function returnError($errors = false, $message = 'Error', $code = 400)
    {
        return response([
            'success' => false,
            'message' => $message,
            'error' => $errors
        ], $code);
    }
}

if (!function_exists('returnSuccess')) {

    function returnSuccess($data, $message = 'Success')
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }
}
if (!function_exists('sendsms')) {

    function sendsms($mobileno, $message){

    $message = urlencode($message);

    $sender = 'SIPIND'; 
    $apikey = '864112os7wco63l6z6381357h53oh57jk8';
    $baseurl = 'https://instantalerts.co/api/web/send?apikey='.$apikey;

    $url = $baseurl.'&sender='.$sender.'&to='.$mobileno.'&message='.$message;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Use file get contents when CURL is not installed on server.
    if(!$response){
        $response = file_get_contents($url);
    }

    return $response;
    
}

    //call function
    //  sendsms('919585850324', 'Hello, This is a test message from spring edge');
}