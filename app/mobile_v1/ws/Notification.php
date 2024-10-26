<?php

namespace App\mobile_v1\ws;

use Bloatless\WebSocket\Connection;

class Notification
{
  /**
   * Create a new class instance.
   * @param Connection $client
   */
  public function __construct(protected Connection $client, protected WSocketHandler $inst)
  {
    $inst->response('My Custom message................', $this->client);
  }
}
