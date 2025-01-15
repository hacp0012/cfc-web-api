<?php

namespace App\mobile_v1\auth;

use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestResponse;
use Hacp0012\Quest\SpawMethod;
use Illuminate\Http\Request;

class DevicesAuthMan
{
  /**
   * Create a new class instance.
   */
  public function __construct(Request $request)
  {
    $this->user = $request->user();
  }

  private $user;

  #[QuestSpaw(ref: 'IhBgmzoDvD437MeXB1cJAAfvqsAQ3xSdC6JRj3xbMqK1aHSpoFCxEJ', method: SpawMethod::GET)]
  public function getAllOf(): array
  {
    $bearerToken = request()->bearerToken();
    $tokens = $this->user->tokens()->get(['name', 'id', 'token', 'created_at', 'last_used_at']);

    [$id, $tokenPart] = explode('|', $bearerToken, 2);

    $current = [];
    $others = [];

    foreach ($tokens as $token) {
      if (hash('sha256', $tokenPart) === $token->token) {
        // $this->user->tokens()->where('token', $token->token)->delete();
        $current = $token;
      } else {
        $others[] = $token;
      }
    }

    QuestResponse::setForJson(ref: 'IhBgmzoDvD437MeXB1cJAAfvqsAQ3xSdC6JRj3xbMqK1aHSpoFCxEJ', model: ['success' => true]);

    return [
      'current_device'  => $current,
      'other_divices'   => $others,
    ];
  }

  #[QuestSpaw(ref: '6cndGeguqWcW7SKUoCIbDmfh391jCKJet2d3HJsgrwnELsDC9geufB', method: SpawMethod::DELETE)]
  public function disconect(int $id): bool
  {
    $loginHandler = new LoginAuth;

    QuestResponse::setForJson(ref: '6cndGeguqWcW7SKUoCIbDmfh391jCKJet2d3HJsgrwnELsDC9geufB', dataName: 'success');

    return $loginHandler->logoutByPersonalTokenId(intval($id));
  }
}
