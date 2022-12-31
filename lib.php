<?php
namespace YouTubeMap;

// Load .env file if exists
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
if(file_exists(__DIR__.'/.env')) {
  $dotenv->load();
}

function extract_location_from_message($message) {

	$message = trim($message);

	// Remove my name 
	$name = $_ENV['CHANNEL_NAME'];
	$nameNoSpaces = str_replace(' ', '', $name);
	$message = trim(str_replace([$name, $nameNoSpaces], '', $message));

	// Strip leading greetings
	$message = trim(preg_replace(/^(aaron|yes|thanks|thank you|hi|hello|hey|hola|hallo|greetings|morning|good morning|evening|good evening|afternoon|good afternoon|goodnight|good night|congrats|congratulations|god kväll),?/i, '', $message));

	$message = trim(preg_replace('/https?:\/\/[^\s]+/', '', $message));
	
	// Check for "from" followed by the end of the message or punctuation
	if(preg_match('/from ([a-z, ]+)/i', $message, $match)) {
		$message = trim($match[1]);
	}

	// Strip all non alpha text
	$message = trim(preg_replace('/[^a-z, ]+/i', '', $message));

	// Strip trailing phrases
	$message = trim(preg_replace('/\b(there|here|everyone|all)$/', '', $message));

	// Look for single words
	if(substr_count($message, ' ') == 0) {
		return $message;
	}

	// Look for just "City, State" pattern
	if(preg_match('/^([a-z]+, [a-z]+)/i', $message, $match)) {
		return $match[1];
	}

	// If there are too many words left, bail out
	if(substr_count($message, ' ') >= 3) {
		return '';
	}

	return $message;
}

function youtube_api($method) {
	$baseURL = 'https://www.googleapis.com/youtube/v3';
	return $baseURL . '/' . $method;
}

function get_youtube_token() {
	if(!isset($_SESSION['token']))
		return null;

	$token = $_SESSION['token'];

	if(time() >= $token['expires_at'])
		return null;

	return $token;
}

