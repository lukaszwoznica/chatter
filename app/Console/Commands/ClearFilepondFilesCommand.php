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
    protected $signature = 'filepond:clear {--older-than= : Delete only files older than specific value (in minutes). }';

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
        $filteredOlderThanOption = filter_var($this->option('older-than'), FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 0
            ]
        ]);

        if ($this->option('older-than') && !$filteredOlderThanOption) {
            $this->error('Invalid older-than option value.');
            return;
        }

        $tmpFilesPath = config('filepond.temporary_files_path');
        $tmpFilesDisk = config('filepond.temporary_files_disk');
        $olderThanTimestamp = now()->subMinutes($filteredOlderThanOption ?: 0)->getTimestamp();

        collect(Storage::disk($tmpFilesDisk)->listContents($tmpFilesPath))
            ->each(function ($tmpDirectory) use ($tmpFilesDisk, $olderThanTimestamp) {
                if ($tmpDirectory['type'] === 'dir' && $tmpDirectory['timestamp'] <= $olderThanTimestamp) {
                    Storage::disk($tmpFilesDisk)->deleteDirectory($tmpDirectory['path']);
                }
            });

        $this->info('FilePond temporary files has been deleted successfully.');
    }
}
