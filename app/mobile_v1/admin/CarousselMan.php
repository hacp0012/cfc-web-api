<?php

namespace App\mobile_v1\admin;

use App\mobile_v1\classes\FileHanderClass;
use App\Models\Admin;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestResponse;
use Hacp0012\Quest\SpawMethod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CarousselMan
{
  /**
   * Create a new class instance.
   */
  public function __construct(Request $request)
  {
    $user = $request->user();
    $this->admin = (new AdminMan)->getOne(userId: $user->id);
  }

  public string $ownerId = 'vXC0KSzZeLfkCOsp36vyBjd0NY6ji3RjdOaN';
  public string $imageGroupe = 'HOME_SLIDER';
  public string $contentGroup = 'IMAGE';
  public Collection|null|Admin $admin = null;

  # METHODS ---------------------------------------------------------->
  #[QuestSpaw(ref: 'vXC0KSzZeLfkCOsp36vyBjd0NY6ji3RjdOaN', jsonResponse: true, method: SpawMethod::GET)]
  public function getIts(): Collection
  {
    $pictures = FileHanderClass::get(
      type: FileHanderClass::TYPE['IMAGE'],
      owner: $this->ownerId,
      ownerGroup: $this->imageGroupe,
      contentGroup: $this->contentGroup,
    );

    QuestResponse::setForJson(ref: 'vXC0KSzZeLfkCOsp36vyBjd0NY6ji3RjdOaN', model: ['success' => true], dataName: 'images');

    return $pictures;
  }

  #[QuestSpaw(ref: 'WporhlnxyCd37QXeu85Q6WUxMFPxxYDjjtcC', jsonResponse: true, method: SpawMethod::DELETE)]
  public function removeOne(string $pid): bool
  {
    $state = FileHanderClass::destroy(publicId: $pid);

    QuestResponse::setForJson(ref: 'WporhlnxyCd37QXeu85Q6WUxMFPxxYDjjtcC', dataName: 'success');
    return $state;
  }

  #[QuestSpaw(ref: 'RAt4fKTL3gxHurY944MAFpOSbISBjhlc8H5W', jsonResponse: true, filePocket: 'picture')]
  public function addOn(UploadedFile $picture, string $label): bool|string
  {
    $pid = null;
    $uFile = FileHanderClass::validate(type: FileHanderClass::TYPE['IMAGE'], uploadedFile: $picture);

    QuestResponse::setForJson(ref: 'RAt4fKTL3gxHurY944MAFpOSbISBjhlc8H5W', dataName: 'state');

    if ($this->admin && $uFile) {
      $state = FileHanderClass::store(
        document: $uFile,
        type: FileHanderClass::TYPE['IMAGE'],
        owner: $this->ownerId,
        ownerGroup: $this->imageGroupe,
        contentGroup: $this->contentGroup,
        label: $label,
        public_id: $pid,
      );

      if ($state) return $pid;
    }

    return false;
  }

  #[QuestSpaw(ref: 'dYgPfNcenZWJ4wdJ0Zzi6eMMq3YhiwXZI6uL', jsonResponse: true, method: SpawMethod::DELETE)]
  public function removeAll(): bool
  {
    $all = $this->getIts();

    $state = false;
    foreach($all as $picture) {
      FileHanderClass::destroy(id: $picture->id);
      $state = true;
    }

    return $state;
  }

  #[QuestSpaw(ref: 'ZaHJf8baqanY0B7N3S1LlOusxNM0GCf3pkbN', jsonResponse: true, filePocket: 'picture')]
  public function uppdate(string $id, UploadedFile $picture): bool
  {
    $uFile = FileHanderClass::validate(type: FileHanderClass::TYPE['IMAGE'], uploadedFile: $picture);
    $pid = null;
    $state = false;

    if ($uFile) {
      $state = FileHanderClass::replace(
        document: $uFile,
        type: FileHanderClass::TYPE['IMAGE'],
        id: $id,
        new_public_id: $pid,
      );
    }

    QuestResponse::setForJson(ref: 'ZaHJf8baqanY0B7N3S1LlOusxNM0GCf3pkbN', dataName: 'success');

    return $state;
  }

  #[QuestSpaw(ref: 'nZ6EfZgVCaxB5THOc4dZ4s8JXtwHa55B9GZw', jsonResponse: true)]
  public function updateLabel(string $pid, string $label): bool
  {
    $state = FileHanderClass::updateLabel(publicId: $pid, label: $label);

    QuestResponse::setForJson(ref: 'nZ6EfZgVCaxB5THOc4dZ4s8JXtwHa55B9GZw', dataName: 'success');
    return $state;
  }
}
