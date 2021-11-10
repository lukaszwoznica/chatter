<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearFilepondFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filepond:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all FilePond temporary files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $tmpFilesPath = config('filepond.temporary_files_path');
        $tmpFilesDisk = config('filepond.temporary_files_disk');

        if (Storage::disk($tmpFilesDisk)->deleteDirectory($tmpFilesPath)) {
            $this->info('Temporary files has been deleted successfully.');
        } else {
            $this->error('Could not delete temporary files.');
        }
    }
}
