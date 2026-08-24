<?php

namespace App\Commands;

use App\Services\ContentCache;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class PingRedis extends BaseCommand
{
    protected $group       = 'Cache';
    protected $name        = 'redis:ping';
    protected $description = 'Check that Redis is reachable for API caching.';
    protected $usage       = 'redis:ping';

    public function run(array $params)
    {
        if (ContentCache::ping()) {
            CLI::write('Redis connection is working.', 'green');
            return;
        }

        CLI::error('Redis is not reachable. Public API will read from the database until Redis is up.');
    }
}
