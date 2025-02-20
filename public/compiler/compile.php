<?php
// Load environment variables from a separate file
require_once 'config.php'; 

$data = json_decode(file_get_contents('php://input'), true);

$language = $data['language'];
$script = $data['code'];

$postData = [
    'clientId' => getenv('JD_CLIENT_ID'),
    'clientSecret' => getenv('JD_CLIENT_SECRET'),
    'script' => $script,
    'language' => $language,
    'versionIndex' => '0',
    'compileOnly' => false
];

$ch = curl_init('https://api.jdoodle.com/v1/execute');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
