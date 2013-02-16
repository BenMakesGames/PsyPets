<?php
function add_cookie_message($message)
{
	global $SETTINGS;

	$i = rand();

	while(array_key_exists($i, $_COOKIE['psypets_messages']))
		$i = rand();

	$_COOKIE['psypets_messages'][$i] = $message;

	setcookie('psypets_messages[' . $i . ']', $message, 0, $SETTINGS['cookie_path'], $SETTINGS['cookie_domain']);
}

function get_cookie_messages()
{
	global $SETTINGS;

	if(is_array($_COOKIE['psypets_messages']))
	{
		$messages = $_COOKIE['psypets_messages'];

		foreach($messages as $key=>$value)
			setcookie('psypets_messages[' . $key . ']', '', time() - 3600, $SETTINGS['cookie_path'], $SETTINGS['cookie_domain']);

		unset($_COOKIE['psypets_messages']);

		return $messages;
	}
	else
		return array();
}
?>
