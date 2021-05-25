<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class uploadFilesCloudFox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudfox:upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $s3drive = Storage::disk('s3_documents');
        $file_path = storage_path('app/produto.png');
        $archiveName = pathinfo($file_path, PATHINFO_FILENAME);
        $archiveExtension = pathinfo($file_path, PATHINFO_EXTENSION);
        $s3drive->putFileAs(
            'cloudfox/defaults',
            $file_path,
            "$archiveName.$archiveExtension",
            'public'
        );

        $urlPath = $s3drive->url(
            "cloudfox/defaults/$archiveName.$archiveExtension"
        );

        $this->line("URL PUBLICA= $urlPath");

        return 0;
    }
}
