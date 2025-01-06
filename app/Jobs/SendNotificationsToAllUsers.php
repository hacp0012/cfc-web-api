<?php

namespace App\Jobs;

use App\mobile_v1\handlers\NotificationHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationsToAllUsers implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   */
  public function __construct(
    protected string $class,
    protected string $userId,
    protected string $subjetId,
    protected string $title,
    protected string $message,
    protected ?string $picture = null,
  ) {}

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $notificationHandler = new NotificationHandler($this->userId);

    // Send to all users.
    $group = $notificationHandler->send(title: $this->title, body: $this->message, picture: $this->picture);
    $action = $group->std($this->class, $this->subjetId);
    $action->toAll();

    // Clean all reads.
    $notificationHandler->deleteAllReads();
  }
}
