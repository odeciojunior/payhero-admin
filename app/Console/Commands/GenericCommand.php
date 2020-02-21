<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Core\Entities\Domain;
use Modules\Core\Services\CloudFlareService;
use function foo\func;

/**
 * Class GenericCommand
 * @package App\Console\Commands
 */
class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'generic';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function handle()
    {
        try {

            $cloudflareService = new CloudFlareService();

            $domains = Domain::where('name', 'tiamatcabuloso.ml')
                ->get();

            $total = $domains->count();

            foreach ($domains as $key => $domain) {

                $this->info($key + 1 . ' de ' . $total . '. Domínio: ' . $domain->name);

                $records = $cloudflareService->getRecords($domain->name);
                $sacRecord = collect($records)->first(function ($item) {
                    if (Str::contains($item->name, 'sac.')) {
                        return $item;
                    }
                });

                if (isset($sacRecord)) {
                    $deleted = $cloudflareService->deleteRecord($sacRecord->id);
                    if ($deleted) {
                        $this->line('Record antigo deletado!');
                        $recordId = $cloudflareService->addRecord("A", 'sac', $cloudflareService::sacIp);
                        $this->line('Novo record criado: ' . $recordId);
                    }
                } else {
                    $this->warn('Record não encontrado');
                }
            }

            $this->info('ACABOOOOOOOOOOOOOU!');

        } catch (\Exception $e) {

            $this->error($e->getMessage());
        }
    }
}
