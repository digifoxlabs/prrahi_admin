<?php

namespace App\Actions\Activity;

use App\Models\ActivityLog;

class LogActivityAction
{
    public static function handle(array $data): void
    {
        ActivityLog::create([
            'actor_type' => $data['actor_type'],
            'actor_id'   => $data['actor_id'],
            'activity_type' => $data['activity_type'],
            'subject_type'  => $data['subject_type'] ?? null,
            'subject_id'    => $data['subject_id'] ?? null,
            'latitude'      => $data['latitude'] ?? null,
            'longitude'     => $data['longitude'] ?? null,
            'meta'          => $data['meta'] ?? null,
        ]);
    }
}
