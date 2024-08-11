<?php

namespace App\mobile_v1\routes;

use App\Models\Pcn;

class Loader
{
  /**
   * Create a new class instance.
   */
  public function __construct()
  {
    //
  }

  public function load() {
    return Pcn::all();
    // return [];
  }
}
