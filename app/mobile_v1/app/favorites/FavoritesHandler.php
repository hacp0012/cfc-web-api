<?php

namespace App\mobile_v1\app\favorites;

use App\mobile_v1\classes\Constants;
use App\mobile_v1\classes\FileHanderClass;
use App\mobile_v1\classes\UserDataHandlerClass;
use App\Models\Communique;
use App\Models\Echos;
use App\Models\Enseignement;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestResponse;
use Hacp0012\Quest\SpawMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

enum FavSection
{
  case echo;
  case teaching;
  case com;
}


class FavoritesHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct(private Request $request)
  {
    $this->user = $this->request->user();
  }
  # ---------------------------------------------------- #
  private string $storeKey = 'FAVORITES_DATAS_M8TeoTS1FNBw7lfLJ0';

  private $user;

  private int $defaultStepLength = 18;

  #[QuestSpaw(ref: 'ba4f78bc-f55d-4e7e-8dc0-7bca9abd9599', method: SpawMethod::GET)]
  public function getListWithContents(int|null $currentStep = null): array
  {
    $userData = new UserDataHandlerClass(userId: $this->user->id);

    $found = $userData->get($this->storeKey);

    if ($found) {
      $columns = ['created_at', 'title', 'text', 'id'];

      $datas = [];
      $keys = [];

      foreach ($found as $item) {
        $section = $item['section'];

        if ($section == FavSection::echo->name) {
          $echo = Echos::where('id', $item['id'])->get($columns)->first();

          if ($echo) {
            $keys[] = ['id' => $echo->id, 'section' => FavSection::echo->name];

            $text = Str::words($echo->text, 36, '...');

            # Picture.
            $picture = FileHanderClass::get(
              type: FileHanderClass::TYPE['IMAGE'],
              owner: $item['id'],
              ownerGroup: Constants::GROUPS_ECHO,
              contentGroup: 'ATACHED_IMAGE',
            )->first();

            # Data.
            $datas[] = [
              'section'     => FavSection::echo->name,

              'id'          => $echo->id,
              'title'       => $echo->title,
              'text'        => $text,
              'created_at'  => $echo->created_at,

              'picture'     => $picture ? $picture->pid : null,
            ];
          }
        } elseif ($section == FavSection::com->name) {
          $innerColumns = [...$columns, 'picture'];

          $com = Communique::where('id', $item['id'])->get($innerColumns)->first();

          if ($com) {
            $keys[] = ['id' => $com->id, 'section' => FavSection::com->name];

            $text = Str::words($com->text, 36, '...');

            # Data.
            $datas[] = [
              'section'     => FavSection::com->name,

              'id'          => $com->id,
              'title'       => $com->title,
              'text'        => $text,
              'created_at'  => $com->created_at,

              'picture'     => $com->picture,
            ];
          }
        } elseif ($section == FavSection::teaching->name) {
          $innerColumns = [...$columns, 'picture'];

          $teach = Enseignement::where('id', $item['id'])->get($innerColumns)->first();

          if ($teach) {
            $keys[] = ['id' => $teach->id, 'section' => FavSection::teaching->name];

            $text = Str::words($teach->text, 36, '...');

            # Data.
            $datas[] = [
              'section'     => FavSection::teaching->name,

              'id'          => $teach->id,
              'title'       => $teach->title,
              'text'        => $text,
              'created_at'  => $teach->created_at,

              'picture'     => $teach->picture,
            ];
          }
        }
      }

      array_reverse($datas);

      $steppeds = $this->stepper($datas, $currentStep ?? 0);

      QuestResponse::setForJson(
        ref: 'ba4f78bc-f55d-4e7e-8dc0-7bca9abd9599',
        model: [
          'success' => true,
          'currentStep' => is_null($currentStep) ? 0 : (count($steppeds) == 0 ? $currentStep : $currentStep + 1),
        ],
        dataName: 'favorites',
      );

      # Update keys.
      $userData->set($this->storeKey, $keys);
      return is_null($currentStep) ? $datas : $steppeds;
    }

    QuestResponse::setForJson(
      ref: 'ba4f78bc-f55d-4e7e-8dc0-7bca9abd9599',
      model: ['success' => true, 'currentStep' => 0],
      dataName: 'favorites',
    );

    return [];
  }

  #[QuestSpaw(ref: 'c58e2412-f1ab-401f-9746-d831d8db1ce4')]
  public function add(string $id, string $section): bool
  {
    QuestResponse::setForJson(ref: 'c58e2412-f1ab-401f-9746-d831d8db1ce4', dataName: 'success');

    $userData = new UserDataHandlerClass(userId: $this->user->id);

    $found = $userData->get($this->storeKey);

    if ($found != null && is_array($found)) {
      $new = [];
      # Avoid double.
      foreach ($found as $item) {
        if ($item['id'] == $id && $item['section'] == $section) continue;
        else $new[] = $item;
      }

      $new[] = ['id' => $id, 'section' => $section];

      return $userData->set($this->storeKey, $new);
    } else {
      $found[] = ['id' => $id, 'section' => $section];

      return $userData->set($this->storeKey, $found);
    }
  }

  #[QuestSpaw(ref: '03b6d770-281a-416e-b37a-ed98fe922dc2', method: SpawMethod::DELETE)]
  public function remove(string $id, string $section): bool
  {
    QuestResponse::setForJson(ref: '03b6d770-281a-416e-b37a-ed98fe922dc2', dataName: 'success');

    $userData = new UserDataHandlerClass(userId: $this->user->id);

    $found = $userData->get($this->storeKey);

    if ($found && is_array($found)) {
      $new = [];
      # remove double.
      foreach ($found as $item) {
        if ($item['id'] == $id && $item['section'] == $section) continue;
        else $new[] = $item;
      }

      return $userData->set($this->storeKey, $new);
    }

    return false;
  }

  private function stepper(array $data, int $currentStep = 0): array
  {
    return array_slice(
      array: $data,
      offset: $this->defaultStepLength * $currentStep,
      length: $this->defaultStepLength,
    );
  }
}
