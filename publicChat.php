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

$pusher->trigger($_POST['channel_name'], $_POST['event_name'], array('message' => $_POST['message'], 'userId' => $_POST['userId']))

?>