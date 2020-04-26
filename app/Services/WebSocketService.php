<?php

namespace App\Services;

use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * @see https://www.swoole.co.uk/docs/modules/swoole-websocket-server
 */
class WebSocketService implements WebSocketHandlerInterface
{
// Declare constructor without parameters
    public function __construct()
    {
    }

    public function onOpen(Server $server, Request $request)
    {
// Before the onOpen event is triggered, the HTTP request to establish the WebSocket has passed the Laravel route,
// so Laravel's Request, Auth information is readable, and Session is readable and writable, but only in the onOpen event.
// \Log::info('New WebSocket connection', [$request->fd, request()->all(), session()->getId(), session('xxx'), session(['yyy' => time()])]);
        $server->push($request->fd, 'Welcome to LaravelS');
// throw new \Exception('an exception');// all exceptions will be ignored, then record them into Swoole log, you need to try/catch them
    }

    public function onMessage(Server $server, Frame $frame)
    {
// \Log::info('Received message', [$frame->fd, $frame->data, $frame->opcode, $frame->finish]);
        $server->push($frame->fd, date('Y-m-d H:i:s'));
// throw new \Exception('an exception');// all exceptions will be ignored, then record them into Swoole log, you need to try/catch them
    }

    public function onClose(Server $server, $fd, $reactorId)
    {
// throw new \Exception('an exception');// all exceptions will be ignored, then record them into Swoole log, you need to try/catch them
    }
}
