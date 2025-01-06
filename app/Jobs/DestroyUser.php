<?php

namespace App\Jobs;

use App\mobile_v1\app\user\UserDestroyer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DestroyUser implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   */
  public function __construct(private string $userId) {}

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $userDestroyer = new UserDestroyer($this->userId);

    $userDestroyer->destroy();
  }
}
