<?php
require __DIR__ . '/../vendor/autoload.php';

session_start();

header("Content-type: application/json");

if(!isset($_SESSION['token'])) {
	echo json_encode([
		'result' => 'not-logged-in'
	]);
}

# Accepts a video URL
# https://www.youtube.com/watch?v=XXXXXXXXXX
# or just the ID: XXXXXXXXXX
if(
	preg_match('/v=([a-zA-Z0-9_-]{11})/', $_POST['video'], $match) ||
	preg_match('/^([a-zA-Z0-9_-]{11})$/', $_POST['video'], $match)
) {
	$videoID = $match[1];

	$http = new p3k\HTTP();

	$url = YouTubeMap\youtube_api('videos');

	$response = $http->get($url.'?'.http_build_query([
		'part' => 'snippet,liveStreamingDetails',
		'id' => $videoID,
	]), [
		'Authorization: Bearer '.$_SESSION['token']['access_token']
	]);
	$data = json_decode($response['body'], true);

	$_SESSION['liveChatID'] = $data['items'][0]['liveStreamingDetails']['activeLiveChatId'] ?? null;
	$_SESSION['videoID'] = $videoID;

	echo json_encode([
		'result' => ($_SESSION['liveChatID'] ? 'ok' : 'no-chat'),
		'debug' => $data
	]);
} else {
	echo json_encode([
		'result' => 'error'
	]);
}

