<?php
require __DIR__ . '/../vendor/autoload.php';

$google_client_id = $_ENV['GOOGLE_CLIENT_ID'];
$google_client_secret = $_ENV['GOOGLE_CLIENT_SECRET'];
$redirect_uri = $_ENV['GOOGLE_REDIRECT_URI'];

$authorize = 'https://accounts.google.com/o/oauth2/v2/auth';
$token = 'https://oauth2.googleapis.com/token';

session_start();


if(array_key_exists('check', $_GET)) {

  header('Content-type: application/json');
  echo json_encode([
    'loggedin' => (YouTubeMap\get_youtube_token() ? true : false)
  ]);


} elseif(array_key_exists('code', $_GET)) {

  if($_GET['state'] != $_SESSION['state']) {
    die('State did not match');
  }
  
  $ch = curl_init($token);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'grant_type' => 'authorization_code',
    'code' => $_GET['code'],
    'client_id' => $google_client_id,
    'client_secret' => $google_client_secret,
    'redirect_uri' => $redirect_uri,
  ]));
  $response = curl_exec($ch);
  
  $token = json_decode($response, true);
  $token['issued_at'] = time();
  $token['expires_at'] = time() + $token['expires_in'];
  $_SESSION['token'] = $token;
  unset($_SESSION['state']);
  
  header('Location: /');
  
} else {

  $_SESSION['state'] = bin2hex(random_bytes(12));
  $authorize = $authorize.'?'.http_build_query([
    'response_type' => 'code',
    'client_id' => $google_client_id,
    'redirect_uri' => $redirect_uri,
    'scope' => 'https://www.googleapis.com/auth/youtube.readonly',
    'state' => $_SESSION['state'],
  ]);

  if(isset($_GET['go'])) {
    header('Location: '.$authorize);
  } else {
    ?>
    <a href="<?= $authorize ?>">Connect Google</a>
    <?php
  }
}

