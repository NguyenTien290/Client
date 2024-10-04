<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class ReceiveMessageCommand extends Command
{

    // protected $signature = 'receive:message {routingKey}';

    // protected $signature = 'receive:message {headerKey} {headerValue}';

     protected $signature = 'receive:message {pattern}';
    protected $description = 'Receive a message to RabbitMQ using Exchange';
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // $connection = new AMQPStreamConnection(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_USER'), env('RABBITMQ_PASSWORD'));
        // $channel = $connection->channel();

        // $channel->queue_declare('hello', false, true, false, false, false, []);

        // Khai báo Direct Exchange
        // $channel->exchange_declare('direct_logs', 'direct', false, true, false);

        // $channel->exchange_declare('topic_logs', 'topic', false, true, false);

        // $channel->exchange_declare('headers_logs', 'headers', false, true, false);

        // Khai báo queue và lấy tên queue động
        // list($queue_name,,) = $channel->queue_declare('', false, true, true, false);

        // Lấy routing key từ tham số
        //  $pattern = $this->argument('pattern');

        // $headerKey = $this->argument('headerKey');
        // $headerValue = $this->argument('headerValue');

        // Bind queue 
        //  $channel->queue_bind($queue_name, 'topic_logs', $pattern);
        //  $channel->queue_bind($queue_name, 'topic_logs', $pattern);
        // $headers = new AMQPTable([
        //     $headerKey => $headerValue,
        //     'x-match' => 'all',
        // ]);

        // $channel->queue_bind($queue_name, 'headers_logs', '',false, $headers);
        // $callback = function (AMQPMessage $message) {
        //     $this->info('Received: ' . $message->body);
        // };

        // $channel->basic_consume($queue_name, '', false, true, false, false, $callback);

        // while ($channel->is_consuming()) {
        //     $channel->wait();
        // }

        // $channel->close();
        // $connection->close();

        // Kết nối đến RabbitMQ
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        // Khai báo Topic Exchange
        $channel->exchange_declare('topic_logs', 'topic', false, true, false);

        // Khai báo queue và lấy tên queue động
        list($queue_name, ,) = $channel->queue_declare('', false, true, true, false);

        // Lấy topic pattern từ tham số
        $pattern = $this->argument('pattern');

        // Bind queue với exchange và topic pattern
        $channel->queue_bind($queue_name, 'topic_logs', $pattern);

        // Hàm callback để xử lý thông điệp nhận được
        $callback = function (AMQPMessage $message) {
            $this->info('Received: ' . $message->body);
        };

        // Lắng nghe thông điệp từ queue
        $channel->basic_consume($queue_name, '', false, true, false, false, $callback);

        // Chờ nhận thông điệp
        while ($channel->is_consuming()) {
            $channel->wait();
        }

        // Đóng kết nối
        $channel->close();
        $connection->close();
    }
    }
}
