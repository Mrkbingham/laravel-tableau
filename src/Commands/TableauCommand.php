<?php

namespace InterWorks\Tableau\Commands;

use Illuminate\Console\Command;

class TableauCommand extends Command
{
    public $signature = 'laravel-tableau';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
