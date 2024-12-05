<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\ShortIo;
use Modules\GatewayIntegrations\Gateways\ShortenLinks\contract\ShortenLinkGatewayInterface;

class DeleteExpiredShortLinks extends Command
{
    protected $signature = 'shortlinks:delete-expired';

    protected $description = 'Delete expired short links';

    public function __construct(
        private readonly ShortenLinkGatewayInterface $shortenLinkGateway,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $expiredLinks = ShortIo::query()->where('expires_at', '<=', now())->limit(10)->get();

        foreach ($expiredLinks as $link) {
            $result = $this->shortenLinkGateway->delete($link->short_id);
            if ($result) {
                $link->delete();
            }
        }

        $this->info('Comando shortlinks:delete-expired executado com sucesso!');

        return Command::SUCCESS;
    }
}
