<?php
$cid = 'Your client ID from user settings';
$cs = 'Your client secret from user settings';

include_once 'v2/iDoklad.php';
            
use petrvacha\iDoklad\iDoklad;
use petrvacha\iDoklad\auth\iDokladCredentials;
use petrvacha\iDoklad\iDokladException;

try{
    $iDoklad = new iDoklad($cid, $cs, '');
    $iDoklad->setCredentialsCallback(function($credentials){
        file_put_contents('credentials.json', $credentials->toJson());
    });
    if(!file_exists('credentials.json')){
        $iDoklad->authCCF();
    } 
    $credentials = new iDokladCredentials(file_get_contents('credentials.json'), true);
    $iDoklad->setCredentials($credentials);
    $request = new \petrvacha\iDoklad\request\iDokladRequest('IssuedInvoices');
    $response = $iDoklad->sendRequest($request);
    echo '<pre>';
    print_r($response);
    echo '</pre>';
} catch (iDokladException $ex) {
    echo $ex->getMessage();
    echo $ex->getTraceAsString();
}
