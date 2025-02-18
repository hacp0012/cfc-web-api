<?php

namespace App\mobile_v1\ws;

use Bloatless\WebSocket\Application\Application;
use Bloatless\WebSocket\Connection;

class WSocketHandler extends Application
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  /**
   * @var array<string,Connection> $clients
   */
  private array $clients = [];

  private string $currentClientId = '';

  /**
   * @var array $nicknames
   */
  private array $nicknames = [];

  // ----------------------------------------------- /

  /**
   * Handles new connections to the application.
   *
   * @param Connection $connection
   * @return void
   */
  public function onConnect(Connection $connection): void
  {
    $id = $connection->getClientId();
    $this->currentClientId = $id;
    $this->clients[$id] = $connection;
    $this->nicknames[$id] = 'Guest' . rand(10, 999);
  }

  /**
   * Handles client disconnects.
   *
   * @param Connection $connection
   * @return void
   */
  public function onDisconnect(Connection $connection): void
  {
    $id = $connection->getClientId();
    unset($this->clients[$id], $this->nicknames[$id]);
  }

  /**
   * Handles incomming data/requests.
   * If valid action is given the according method will be called.
   *
   * @param string $data
   * @param Connection $client
   * @return void
   */
  public function onData(string $data, Connection $client): void
  {
    try {
      $decodedData = $this->decodeData($data);

      // check if action is valid
      if ($decodedData['action'] !== 'echo') {
        return;
      }

      $message = $decodedData['data'] ?? '';
      if ($message === '') {
        return;
      }

      $clientId = $client->getClientId();
      $message = $this->nicknames[$clientId] . ': ' . $message;
      $this->actionEcho($message);
    } catch (\RuntimeException $e) {
      // @todo Handle/Log error
    }
  }

  /**
   * Handles data pushed into the websocket server using the push-client.
   *
   * @param array $data
   */
  public function onIPCData(array $data): void
  {
    $actionName = 'action' . ucfirst($data['action']);
    $message = 'System Message: ' . $data['data'] ?? '';
    if (method_exists($this, $actionName)) {
      call_user_func([$this, $actionName], $message);
    }
  }

  /**
   * Echoes data back to client(s).
   *
   * @param string $text
   * @return void
   */
  private function actionEcho(string $text): void
  {
    $encodedData = $this->encodeData('echo', $text);
    foreach ($this->clients as $sendto) {
      $sendto->send($encodedData);
    }
  }

  // --> ------------------------------------------------------------

  public function response(string $text, Connection $client): void
  {
    $encodedData = $this->encodeData('message', $text);
    $client->send($encodedData);
  }

  /** @param Connection $client */
  public function runController()
  {
    if (isset($this->clients[$this->currentClientId])) {
      new Notification(
        client: $this->clients[$this->currentClientId],
        inst: $this,
      );
    }
  }
}
