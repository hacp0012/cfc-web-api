<?php

namespace App\mobile_v1\admin;

use App\mobile_v1\app\search\SearchEngine;
use App\Models\Admin;
use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestResponse;
use Hacp0012\Quest\SpawMethod;
use Illuminate\Database\Eloquent\Collection;

class AdminMan
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  # METHODS --------------------------------------------:

  #[QuestSpaw(ref: 'aQD8eIM9XxXUgv4D5aUOHmRC3ruAIdr8LVf7', jsonResponse: true, method: SpawMethod::GET)]
  public static function isAdmin(String $userId): bool
  {
    $admin = Admin::whereFirst(['user_ref' => $userId]);

    if ($admin) return true;

    return false;
  }

  #[QuestSpaw(ref: 'g7HHS60dfNou8biAQFTrre8Xn9HhImRomQjS', jsonResponse: true)]
  public function simpleAuthCheker(string $pssaword, ?string $uname = null): bool
  {
    $user = request()->user();

    QuestResponse::setForJson(ref: 'g7HHS60dfNou8biAQFTrre8Xn9HhImRomQjS', dataName: 'state');

    if ($user) {
      $admin = Admin::where(['user_ref' => $user->id, 'pswd' => $pssaword])->first();

      if ($admin) return true;
    }
    return false;
  }

  #[QuestSpaw(ref: '6D70wGNpOEWbetkHfAXIwBhiz19KvAXDT2aB', jsonResponse: true)]
  public function add(string $name, string $pssaword, ?string $userRef = null): bool
  {
    $data = [
      'name' => $name,
      'pswd' => $pssaword,
      'user_ref' => $userRef,
    ];

    QuestResponse::setForJson('6D70wGNpOEWbetkHfAXIwBhiz19KvAXDT2aB', dataName: 'success');

    Admin::create($data);

    return true;
  }

  /** Delete a admin.
   * - Can't delete a master admin.
   */
  #[QuestSpaw(ref: 'Qt5GKM16W5EoZ0kVQVIhOfXDxYI9I3cubFz3', jsonResponse: true, method: SpawMethod::DELETE)]
  public function remove(int $adminId): bool
  {
    QuestResponse::setForJson(ref: 'Qt5GKM16W5EoZ0kVQVIhOfXDxYI9I3cubFz3', dataName: 'success');

    $admin = Admin::find($adminId);

    if ($admin && $admin->is_master == false) {
      $state = $admin->delete();

      return $state;
    }

    return false;
  }

  #[QuestSpaw(ref: '8UELYWEvapizncRZqOg3DgjUscHlGGG0ARky', jsonResponse: true)]
  public function update(int $adminId, string $name = null, string $pssaword = null): bool
  {
    QuestResponse::setForJson(ref: '8UELYWEvapizncRZqOg3DgjUscHlGGG0ARky', dataName: 'success');

    $admin = Admin::find($adminId);

    $data = [];

    if ($name) $data['name'] = $name;
    if ($pssaword && $admin->is_master == false) $data['pswd'] = $pssaword;

    if ($admin && ($name || $pssaword)) {
      $state = Admin::whereId($adminId)->update($data);

      return $state;
    }

    return false;
  }

  #[QuestSpaw(ref: 'ZwfjuIPKGNMZqwBOJIkvKSKf1GIVr0TegkSV', jsonResponse: true, method: SpawMethod::GET)]
  public function list(): Collection
  {
    $admins = Admin::all(['name', 'id', 'user_ref', 'is_master']);

    QuestResponse::setForJson('ZwfjuIPKGNMZqwBOJIkvKSKf1GIVr0TegkSV', dataName: 'admins');

    return $admins;
  }

  #[QuestSpaw(ref: 'R3M95bTYq65a62TRjRlhFYUaPRNycHBpiOMd', jsonResponse: true, method: SpawMethod::GET)]
  public function getOne(?string $adminId = null, ?string $userId = null): Admin|null
  {
    if ($adminId) {
      $admin = Admin::whereId($adminId)->first();

      if ($admin) return $admin;
    } elseif ($userId) {
      $admin = Admin::whereUserRef($userId)->first();

      if ($admin) return $admin;
    }

    return null;
  }

  #---------------------------------------------------------------------------------------------------------------------
  #[QuestSpaw(ref: '03euj09f8w4mcP3PIR9xY2JeDkDw3dHvNc3J', method: SpawMethod::GET)]
  public function fetchUsers(string $name): array
  {
    $searchEngine = new SearchEngine(keyphrase: $name);

    $users = $searchEngine->customSearch(tableModel: new User, fields: ['name', 'fullname']);

    QuestResponse::setForJson(ref: '03euj09f8w4mcP3PIR9xY2JeDkDw3dHvNc3J', model: ['success' => true], dataName: 'users');

    $filtreds = [];
    foreach ($users as $user) $filtreds[] = [
      'fullname' => $user['fullname'],
      'name' => $user['name'],
      'telephone' => $user['telephone'],
      'id' => $user['id'],
    ];

    return $filtreds;
  }
}
