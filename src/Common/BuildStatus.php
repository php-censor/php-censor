<?php

declare(strict_types=1);

namespace PHPCensor\Common;

enum BuildStatus: string
{
    case PENDING = 'pending';
    case RUNNING = 'running';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case CANCELED = 'canceled';

    public function getDescription(): string
    {
        return match($this) {
            self::PENDING => 'Waiting to be executed',
            self::RUNNING => 'Currently in progress',
            self::SUCCESS => 'Completed successfully',
            self::FAILED => 'Failed to complete',
            self::CANCELED => 'Manually canceled',
        };
    }
}