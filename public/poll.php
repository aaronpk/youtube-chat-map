<?php
require __DIR__ . '/../vendor/autoload.php';

session_start();


if(empty($_SESSION['liveChatID'])) {
	echo json_encode([
		'error' => 'no-chat'
	]);
	die();
}


$http = new p3k\HTTP();

$url = YouTubeMap\youtube_api('liveChat/messages');

$response = $http->get($url.'?'.http_build_query([
	'liveChatId' => $_SESSION['liveChatID'],
	'part' => 'snippet,authorDetails',
	'playlistId' => $playlist_id,
	'pageToken' => $_SESSION['nextPageToken'] ?? null,
	'profileImageSize' => 120,
]), [
	'Authorization: Bearer '.$_SESSION['token']['access_token']
]);
$data = json_decode($response['body'], true);

header("Content-type: application/json");

if(isset($data['error']['code']) && $data['error']['code'] == 404) {
	unset($_SESSION['nextPageToken']);
	unset($_SESSION['liveChatID']);
	unset($_SESSION['videoID']);
	echo json_encode([
		'result' => 'ended',
	]);
	die();
}

if(isset($data['nextPageToken'])) {
	$_SESSION['nextPageToken'] = $data['nextPageToken'];
}

if(isset($data['items'])) {
	$fp = fopen(__DIR__.'/../log/'.$_SESSION['videoID'].'.txt', 'a');

	foreach($data['items'] as $item) {
		$location = \YouTubeMap\extract_location_from_message($item['snippet']['textMessageDetails']['messageText']);
		if($location) {
			$chat = [
				'message' => $item['snippet']['textMessageDetails']['messageText'],
				'author_name' => $item['authorDetails']['displayName'],
				'author_photo' => $item['authorDetails']['profileImageUrl'],
				'location' => $location,
			];
			$_SESSION['chat'][] = $chat;
		}
		fwrite($fp, json_encode($chat, JSON_UNESCAPED_SLASHES)."\n");
	}

	fclose($fp);
}

echo json_encode([
	#'interval' => $data['pollingIntervalMillis'],
	'interval' => 5000,
	'messages-debug' => $data,
]);

