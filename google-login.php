<?php
require 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setClientId('529934975236-d5tnurm5ad4mm3t6tj5cp8p2ikvprulm.apps.googleusercontent.com');
$client->setClientSecret('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');  
$client->setRedirectUri('http://localhost/google-login.php'); 
$client->addScope('email');
$client->addScope('profile');


$auth_url = $client->createAuthUrl();
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
?>