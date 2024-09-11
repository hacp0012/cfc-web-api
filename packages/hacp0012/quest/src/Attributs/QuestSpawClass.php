<?php

namespace Princ\Quest\Attributs;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class QuestSpawClass
{
  /**
   * Create a new class instance.
   * @param array<mixed>|null $constructWith A list of argument to pass to
   * the class constructor.
   * Not an _AsscoArray_ on only indexed array.
   *
   * _If parameters are less or more, the class will not be constructed, but methods will be called without class construction.
   * Be careful when you acceced values that will be constructed before with class constructore._
   */
  public function __construct(public array|null $constructWith = null) {}
}
