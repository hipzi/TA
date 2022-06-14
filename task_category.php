<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$ipaddress = "localhost";
$username = "guest";
$password = "guest";

$connection = new AMQPStreamConnection($ipaddress, 5672, $username, $password);
$channel = $connection->channel();

$userid = $argv[1];
$categoryid = $argv[2];
$queueName = "queue_category";

$channel->queue_declare($queueName, false, true, false, false);

if (empty($data)) {
    $data = "Empty Category";
}

$data = array('userid'=>$userid, 'categoryid'=>$categoryid);
$data = json_encode($data, true);
$msg = new AMQPMessage(
    $data,
    array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
);

$channel->basic_publish($msg, '', $queueName);

echo ' [x] Sent ', $data, "\n";

$channel->close();
$connection->close();
?>
