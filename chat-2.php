<?php

require __DIR__ . '/vendor/autoload.php';

$app_id = '182511';
$app_key = 'e721d40bbc86918310ab';
$app_secret = '79ab229f24fa81adc347';

$pusher = new Pusher(
  $app_key,
  $app_secret,
  $app_id
);

$current_user = new stdClass();
$current_user->display_name = $_COOKIE["username"];
$current_user->ID = $_COOKIE["rid"];

$presence_data = array('username' => $current_user->display_name);
echo $pusher->presence_auth($_POST['channel_name'], $_POST['socket_id'], $current_user->ID, $presence_data);

?>