<?php

namespace App\mobile_v1\app\user;

use App\mobile_v1\app\search\SearchEngine;
use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\Quest;
use Hacp0012\Quest\QuestResponse;
use Hacp0012\Quest\SpawMethod;
use Illuminate\Http\Request;

class UserMyCommunity
{
  /**
   * Create a new class instance.
   */
  public function __construct(Request $request)
  {
    $this->user = $request->user();
  }

  private $user;

  // ---------------------------------------------------------------- /
  public int $defaultStepLength = 18;
  private array $userFields = ['id', 'fullname', 'address', 'role', 'civility'];

  #[QuestSpaw(ref: 'auQbIpSqWXMzsWysunL7hqhAc1o42dRJOoT2fQShngUl5VO3LtFQG4', method: SpawMethod::GET)]
  public function search(string $keyphrase, int $currentStep = 0): array
  {
    $searchEngine = new SearchEngine(keyphrase: $keyphrase);

    $tableFields = ['name', 'fullname', 'telephone', 'address', 'email'];

    $results = $searchEngine->customSearch(new User, $tableFields);

    $filtreds = [];
    foreach($results as $person) {
      if ($person['com_loc'] == $this->user->com_loc) $filtreds[] = $person;
    }

    $sliceds = $this->stepper($filtreds, $currentStep);

    QuestResponse::setForJson(
      ref: 'auQbIpSqWXMzsWysunL7hqhAc1o42dRJOoT2fQShngUl5VO3LtFQG4',
      model: ['success' => true, 'currentStep' => count($sliceds) > 0 ? $currentStep + 1 : $currentStep],
      dataName: 'results',
    );

    return $sliceds;
  }

  private function stepper(array $data, int $currentStep = 0): array
  {
    return array_slice(
      array: $data,
      offset: $this->defaultStepLength * $currentStep,
      length: $this->defaultStepLength,
    );
  }

  #[QuestSpaw(ref: 'RJulBHKKz1sUC2Ur5bdpa6pZI9lCMIXGFeDDu8QpeabAdg3r6HDkXv', method: SpawMethod::GET)]
  public function getMembers(int $currentStep = 0): array
  {
    if ($this->user->com_loc) {
      $users = User::whereCom_loc($this->user->com_loc)->get($this->userFields);

      QuestResponse::setForJson(
        ref: 'RJulBHKKz1sUC2Ur5bdpa6pZI9lCMIXGFeDDu8QpeabAdg3r6HDkXv',
        model: ['success' => true, 'currentStep' => $users->count() > 0 ? $currentStep + 1 : $currentStep],
        dataName: 'members',
      );

      $sliceds = $this->stepper(array_values($users->toArray()), $currentStep);

      return $sliceds;
    } else {
      QuestResponse::setForJson(
        ref: 'RJulBHKKz1sUC2Ur5bdpa6pZI9lCMIXGFeDDu8QpeabAdg3r6HDkXv',
        model: ['success' => false],
        dataName: 'members',
      );

      return [];
    }
  }
}
