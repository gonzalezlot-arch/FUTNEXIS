<?php

namespace App\Modules\Core\Console;

use App\Modules\Core\Actions\PruneAuthenticationData;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('core:prune-auth-data')]
#[Description('Delete authentication data past its retention period')]
class PruneAuthenticationDataCommand extends Command
{
    public function handle(PruneAuthenticationData $pruneAuthenticationData): int
    {
        foreach ($pruneAuthenticationData->handle() as $kind => $deleted) {
            $this->components->twoColumnDetail($kind, (string) $deleted);
        }

        return self::SUCCESS;
    }
}
