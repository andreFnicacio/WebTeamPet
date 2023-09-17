<?php

namespace App\Console\Commands;

use App\Helpers\Utils;
use App\Models\Grupos;
use App\Models\Planos;
use App\Models\PlanosGrupos;
use App\Models\PlanosProcedimentos;
use App\Models\Procedimentos;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Log;

class ImportProceduresIntoPlans extends Command
{
    const SPECIALITIES = "Especialistas";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:procedures {file} {plan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Procedures into Plan';

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
     * @return mixed
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $planName = $this->argument('plan');

        try {
            $plan = Planos::where('nome_plano', '=', ucwords($planName))->where('participativo', 1)->firstOrFail();
        } catch (\Exception $e) {
            throw new \Exception("Plan not found");
        }

        if (!file_exists(base_path($filePath))) {
            throw new FileNotFoundException("File not found");
        }

        $data = Utils::csvToArray($filePath);
        $errors = [];

        $planGroup = null;
        foreach ($data as $row) {
            $group = Grupos::where('nome_grupo', $row['grupo'])->first();

            if (!$group) {
                $group = $this->createProcedureGroup($row['grupo']);
            }

            try {
                $procedure = $this->createProcedure($row, $group);
            } catch (\Exception $e) {
                $errors['procedures'][] = $row;
                continue;
            }

            if ($procedure) {
                try {
                    $planGroup = $this->createPlanGroup($group->id, $plan->id);
                } catch (\Exception $e) {
                    $errors['plan_group'][] = $row;
                }
            }

            if ($planGroup) {
                try {
                    $this->createPlanProcedure($plan->id, $procedure->id, $row);
                } catch (\Exception $e) {
                    $errors['plan_procedure'][] = $row;
                }
            }
        }

        if (empty($errors)) {
            $this->info("All records were imported successfully");
        } else {
            $this->error("Some records could not be processed, check the log file");
            Log::error("Import Error: " . json_encode($errors));
        }
    }

    private function createProcedureGroup($groupName)
    {
        return Grupos::create(['nome_grupo' => $groupName]);
    }

    private function createProcedure($procedureRow, $group)
    {
        $procedure = Procedimentos::create([
            'nome_procedimento' => $procedureRow['procedimento'],
            'especialista' => $group->nome_grupo === self::SPECIALITIES,
            'valor_base' => $procedureRow['honorarios'],
            'id_grupo' => $group->id,
            'ativo' => true,
            'emergencial' => false,
            'pre_cirurgico' => false
        ]);

        if ($procedure) {
            $procedure->update([
                'cod_procedimento' => $procedure->id
            ]);
        }

        return $procedure;
    }

    private function createPlanGroup($groupId, $planId)
    {
        $planGroup = PlanosGrupos::where('plano_id', $planId)
            ->where('grupo_id', $groupId)->first();

        if ($planGroup) {
            return $planGroup;
        }

        return PlanosGrupos::create([
            'id' => $groupId,
            'liberacao_automatica' => true,
            'dias_carencia' => 0,
            'quantidade_usos' => 99999,
            'valor_desconto' => 0.00,
            'plano_id' => $planId,
            'grupo_id' => $groupId
        ]);
    }

    private function createPlanProcedure($planId, $procedureId, $procedureRow)
    {
        return PlanosProcedimentos::create([
            'id_procedimento' => $procedureId,
            'id_plano' => $planId,
            'valor_cliente' => null,
            'valor_credenciado' => null,
            'beneficio_tipo' => 'fixo',
            'beneficio_valor' => $procedureRow['copart']
        ]);
    }
}
