<?php 
namespace App\Services\Distributors;

use App\Models\Distributor;

class StoreDistributorRelationsService
{
    public static function execute(Distributor $distributor, array $payload): void
    {
        foreach ($payload['companies'] ?? [] as $company) {
            if (!empty($company['company_name'])) {
                $distributor->companies()->create($company);
            }
        }

        foreach ($payload['banks'] ?? [] as $bank) {
            if (!empty($bank['bank_name'])) {
                $distributor->banks()->create($bank);
            }
        }

        foreach ($payload['godowns'] ?? [] as $godown) {
            if (!empty($godown['no_godown'])) {
                $distributor->godowns()->create($godown);
            }
        }

        foreach ($payload['manpowers'] ?? [] as $manpower) {
            $distributor->manpowers()->create($manpower);
        }

        foreach ($payload['vehicles'] ?? [] as $vehicle) {
            $distributor->vehicles()->create($vehicle);
        }
    }
}
