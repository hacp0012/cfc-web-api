<?php

namespace App\mobile_v1\admin;

use App\Jobs\DestroyUser;
use App\mobile_v1\app\search\CustomSearchEngineModelRequestMode;
use App\mobile_v1\app\search\SearchEngine;
use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestResponse;
use Hacp0012\Quest\SpawMethod;

class UsersMan
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  // * ------------------------------------------- *
  /** @return array<string,int> [trasheds, disableds]*/
  #[QuestSpaw(ref: 'FYUZWsfjHxwnY5nKVNQRI6IoUCQj6mVshDTNALyXmQ1FqUb1OX4xKZuJj3ZVSGr', method: SpawMethod::GET)]
  public function count(): array
  {
    $users = User::count();

    $trasheds = User::onlyTrashed()->count();

    QuestResponse::setForJson(
      ref: 'FYUZWsfjHxwnY5nKVNQRI6IoUCQj6mVshDTNALyXmQ1FqUb1OX4xKZuJj3ZVSGr',
      model: ['success' => true],
      dataName: 'counts',
    );

    return ['enableds' => $users, 'disableds' => $trasheds, 'inProcess' => 0];
  }

  #[QuestSpaw(ref: 'cM2i5Rja23egpoYNlLwTJveVemr69gsKUIoAYxJLS0O9PIsg8woAoP6ksylQpId', method: SpawMethod::DELETE)]
  public function remove(string $userId): bool
  {
    DestroyUser::dispatch($userId);

    QuestResponse::setForJson(ref: 'cM2i5Rja23egpoYNlLwTJveVemr69gsKUIoAYxJLS0O9PIsg8woAoP6ksylQpId', dataName: 'success');
    return true;
  }

  #[QuestSpaw(ref: 'jhlTufQm2nYBaaTxAABWcVwtsFZOMOPnxUdovxSlOFOQqt16Ut9ZAnjiqidC33T', method: SpawMethod::DELETE)]
  public function disable(string $userId): bool
  {
    $state = User::find($userId)?->delete() ?? false;

    QuestResponse::setForJson(ref: 'jhlTufQm2nYBaaTxAABWcVwtsFZOMOPnxUdovxSlOFOQqt16Ut9ZAnjiqidC33T', dataName: 'success');
    return $state;
  }

  #[QuestSpaw(ref: 'hYV4YMImQlmTKIECt86G1gklip1pVcg846HjnqaagbU9elvxZWub3z86EGhjZFE')]
  public function enable(string $userId): bool
  {
    $user = User::withTrashed()->find($userId);

    QuestResponse::setForJson(ref: 'hYV4YMImQlmTKIECt86G1gklip1pVcg846HjnqaagbU9elvxZWub3z86EGhjZFE', dataName: 'success');

    if ($user) {
      $state = $user->restore();

      return $state;
    }

    return false;
  }

  // * -------------------------------------------- * /
  private array $userFields = ['id', 'fullname', 'pool', 'com_loc', 'noyau_af', 'telephone', 'created_at', 'address'];

  #[QuestSpaw(ref: 'W5yLCMvZEWqg2w4IXlasVDPlGEvzaitSuPsUoULLm915AVV4qcdKHQl6MXlf2vU', method: SpawMethod::GET)]
  public function getEnableds(int $currentStep = 0): array
  {
    $users = User::all($this->userFields);

    QuestResponse::setForJson(
      ref: 'W5yLCMvZEWqg2w4IXlasVDPlGEvzaitSuPsUoULLm915AVV4qcdKHQl6MXlf2vU',
      model: ['success' => true, 'currentStep' => $users->count() > 0 ? $currentStep + 1 : $currentStep],
      dataName: 'users',
    );

    $sliceds = $this->stepper(array_values($users->toArray()), $currentStep);

    return $sliceds;
  }

  #[QuestSpaw(ref: 'oVVrODXrEzhtHFij2s0YWjTC8N9XMcH5GPWfWBnn20AqDfrVBXxp78pGS4JZZH1', method: SpawMethod::GET)]
  public function getDisableds(int $currentStep = 0): array
  {
    $users = User::onlyTrashed()->get($this->userFields);

    $sliceds = $this->stepper(array_values($users->toArray()), $currentStep);

    QuestResponse::setForJson(
      ref: 'oVVrODXrEzhtHFij2s0YWjTC8N9XMcH5GPWfWBnn20AqDfrVBXxp78pGS4JZZH1',
      model: ['success' => true, 'currentStep' => $users->count() > 0 ? $currentStep + 1 : $currentStep],
      dataName: 'users',
    );

    return $sliceds;
  }

  #[QuestSpaw(ref: 'mCgvrcpvsVxTCnMYaKS3PzIIHHNypwLgl577CztvSett3g4qaPE0TF', method: SpawMethod::GET)]
  public function getInprocess(int $currentStep = 0): array
  {
    $users = User::whereState('INVALIDE')->get($this->userFields);

    $sliceds = $this->stepper(array_values($users->toArray()), $currentStep);

    QuestResponse::setForJson(
      ref: 'oVVrODXrEzhtHFij2s0YWjTC8N9XMcH5GPWfWBnn20AqDfrVBXxp78pGS4JZZH1',
      model: ['success' => true, 'currentStep' => $users->count() > 0 ? $currentStep + 1 : $currentStep],
      dataName: 'users',
    );

    return $sliceds;
  }

  // * ------------------------------------------ *
  private int $defaultStepLength = 18;

  #[QuestSpaw(ref: '2Xw6JM9Wk6KJ4QOETif1N0xC8HyWYVQk2LQYGJka425KCKUPIKuGS8BLHGeiVlf', method: SpawMethod::GET)]
  public function search(string $keyphrase, bool $isDisableds = false, int $currentStep = 0): array
  {
    $searchEngine = new SearchEngine(keyphrase: $keyphrase);

    $tableFields = ['name', 'fullname'];

    $fieldMode = match ($isDisableds) {
      true => CustomSearchEngineModelRequestMode::ONLY_TRASHED,
      false => CustomSearchEngineModelRequestMode::WITHOUT_TRASHED,
    };

    $results = $searchEngine->customSearch(new User, $tableFields, mode: $fieldMode);

    $sliceds = $this->stepper($results, $currentStep);

    QuestResponse::setForJson(
      ref: '2Xw6JM9Wk6KJ4QOETif1N0xC8HyWYVQk2LQYGJka425KCKUPIKuGS8BLHGeiVlf',
      model: ['success' => true, 'currentStep' => count($sliceds) > 0 ? $currentStep + 1 : $currentStep, 'isDisableds' => $isDisableds],
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

  public function getNextEnabledsContents() {}

  public function getNextDisbledsContents() {}
}
