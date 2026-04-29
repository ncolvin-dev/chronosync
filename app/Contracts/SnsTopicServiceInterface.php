<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Meeting;

interface SnsTopicServiceInterface
{
    public function ensureTopicExists(Meeting $meeting): string;

    public function syncSubscriptions(Meeting $meeting): void;

    public function publish(Meeting $meeting, string $message): void;
}
