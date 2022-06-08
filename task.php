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
$courseid = $argv[2];
$queueName = "task_queue";

$channel->queue_declare($queueName, false, true, false, false);

if (empty($data)) {
    $data = "Empty Course";
}

$data = array('userid'=>$userid, 'courseid'=>$courseid);
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
