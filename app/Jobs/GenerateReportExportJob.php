<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateReportExportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $user, public string $type, public array $filters = [])
    {
        $this->onQueue('reports');
    }

    public function handle(): void
    {
        logger()->info('Report export completed', ['user_id' => $this->user->id, 'type' => $this->type]);
    }
}
