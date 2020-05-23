<?php
require __DIR__ . '/../vendor/autoload.php';

session_start();

header('Content-type: application/json');

if(empty($_SESSION['liveChatID'])) {
	echo json_encode([
		'error' => 'no-chat'
	]);
	die();
}

// Return one message from the stack

$chat = null;

if(isset($_SESSION['chat'])) {
	$chat = array_shift($_SESSION['chat']);
}

echo json_encode([
	'chat' => $chat
]);
