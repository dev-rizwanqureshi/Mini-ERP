<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateActivityLogListener implements ShouldQueue
{
    public function handle(object $event): void
    {
        $model = $event->invoice ?? $event->payment ?? null;

        if (! $model) {
            return;
        }

        ActivityLog::query()->create([
            'user_id' => auth()->id(),
            'action' => class_basename($event),
            'model_type' => $model::class,
            'model_id' => $model->id,
            'description' => class_basename($event).' recorded.',
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
