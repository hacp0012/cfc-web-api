<?php

namespace App\mobile_v1\ws;

use Bloatless\WebSocket\Application\StatusApplication;
use Bloatless\WebSocket\Server;

// Server solution on : https://github.com/bloatless/php-websocket/issues/27
// Server ext-socket solution on : https://puvox.software/blog/solution-error-in-composer-the-requested-php-extension-ext-sockets-is-missing-from-your-system/?unapproved=13709&moderation-hash=0a846bd8b42b33e72e95f87291f40a8b#comment-13709

class WSocket
{
  const HANDLER_NAME            = 'ws';
  const MAX_CLIENTS             = 10e6;
  const MAX_CONNEXTIONS_PER_IP  = 3;
  const TIMER_INTERVAL          = 360; // ms

  /**
   * Create a new class instance.
   */
  public function __construct()
  {
    // create new server instance

    // dd($_SERVER);
    // $_SERVER['SERVER_NAME']
    // $_SERVER['SERVER_ADDR']
    // $_SERVER['SERVER_PORT']
    // $_SERVER['REMOTE_PORT']
    // $_SERVER['REMOTE_ADDR']
    // $_SERVER['TMP']

    $server = new Server(
      $_SERVER['SERVER_ADDR'],
      intval($_SERVER['SERVER_PORT']),
      $_SERVER['TMP'] . '/phpwss.sock',
    );

    // * server settings *
    // $server->setAllowedOrigin('example.com');
    // $server->setCheckOrigin(false);
    $server->setMaxClients(WSocket::MAX_CLIENTS);
    $server->setMaxConnectionsPerIp(WSocket::MAX_CONNEXTIONS_PER_IP);

    $handler = WSocketHandler::getInstance();

    // $server->addTimer(WSocket::TIMER_INTERVAL, function () use ($handler) {
    //   $handler->{'runController'}();
    // });

    // add your applications
    $server->registerApplication('status', StatusApplication::getInstance());
    $server->registerApplication(WSocket::HANDLER_NAME, $handler);

    // start the server
    $server->run();
  }

  public function close() {}
}
