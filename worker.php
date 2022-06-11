<?php

define('CLI_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$ipaddress = "localhost";
$username = "guest";
$password = "guest";

$connection = new AMQPStreamConnection($ipaddress, 5672, $username, $password);
$channel = $connection->channel();

$queueName = "task_queue";

$channel->queue_declare($queueName, false, true, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    global $connection;
    $channel = $connection->channel();

    echo ' [x] Received ', $msg->body, "\n";
    $data = json_decode($msg->body,true);
    $courseid = $data['courseid'];
    $userid = $data['userid'];

    $buildqueueuser = "build_message_" . $userid . "_" . "$courseid";
    $channel->queue_declare($buildqueueuser, false, true, false, false);
    $data = "Sedang Building Course $courseid";
    $msguser = new AMQPMessage(
        $data,
        array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
    );
    $channel->basic_publish($msguser, '', $buildqueueuser);

    $old_path = getcwd();
    chdir('docker');
    shell_exec("./backupBuild.sh $courseid");
    chdir($old_path);

    $finishqueueuser = "finish_message_" . $userid . "_" . "$courseid";
    $channel->queue_declare($finishqueueuser, false, true, false, false);
    $data = "Selesai Building Course $courseid";
    $msguser = new AMQPMessage(
        $data,
        array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
    );
    $channel->basic_publish($msguser, '', $finishqueueuser);    

    echo " [x] Done\n";
    $msg->ack();
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume($queueName, '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>
