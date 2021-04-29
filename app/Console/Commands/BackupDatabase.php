<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ub:backup-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database';

    protected  $process;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $today = today()->format('Y-m-d-h:i:sP');
        if(!is_dir(public_path('backups'))) mkdir(public_path('backups'));
        $this->process = new Process(sprintf(
            'mysqldump --compact --skip-comments -u%s -p%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            public_path("backups/{$today}.sql")
        ));

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {

            $this->process->mustRun();
            \Log::info('Daily DB Backup - Success');
        }catch (ProcessFailedException $exception){
            \Log::error('Daily DB Backup - Failed', $exception);
        }
    }
}
