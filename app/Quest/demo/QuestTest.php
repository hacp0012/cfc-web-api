<?php

namespace App\quest\demo;

use App\Quest\Quest;
use App\Quest\QuestSpaw;
use App\quest\QuestSpawClass;
use App\Quest\QuestSpawMethod;
use Illuminate\Http\UploadedFile;

#[QuestSpawClass()]
class QuestTest
{
  /**
   * Create a new class instance of Quest Guru.
   */
  public function __construct() {}


  # -------------------------------------------------------------------------------------------:
  #[QuestSpaw(ref: '9ef4f696-9bdd-4b31-8aba-d626799b2299', method: QuestSpawMethod::GET)]
  public static function printHello(string $message, int|float $age): string
  {
    return "$message ... avec ta tete de Facochere : $age";
  }

  #[QuestSpaw(ref: 'RdVWAQFS7FSFZeYMkp', filePocket: 'document')]
  public function fileTest(UploadedFile $document, int $age)
  {
    dd($document);
  }


  # -------------------------------------------------------------------------------------------:
  #[QuestSpaw(ref: 'KsREAZ8dUZDIyzByU9')]
  function myQuestTest(int $start, float $infinityEnd): array
  {
    return [$start, $infinityEnd];
  }

  # -------------------------------------------------------------------------------------------:
  #[QuestSpaw(
    ref: 'my quest flag ID',
    method: QuestSpawMethod::GET,
    middleware: ['sunctum:auth'],
    filePocket: 'guidPicture',
    alias: ['moon' => 'middle'],
    jsonResponse: true,
  )]
  function yogaStage(int $moon, int $sunRise, $guidPicture): int
  {
    // ? Illuminate\Http\UploadedFile ?
    //* -> $guidPicture

    return $moon + $sunRise;
  }
}
