<?php

namespace App\Services;

use App\Models\PLanos;
use App\Models\PlanosGrupos;
use App\Models\PlanosProcedimentos;
use App\Models\Procedimentos;

class PlanoInconsistenciasService
{
    private $plano;

    public function __construct(Planos $plano)
    {
       $this->plano = $plano;
    }

    public function checarInconsistencias(array $procedimentosArquivo)
    {
        try {
            $planosGrupos = $this->plano->planosGrupos;
            $planosProcedimentos = $this->plano->planosProcedimentos;
            $idsProcedimentosPlano = $planosProcedimentos->pluck('id_procedimento')->all();

            $idsProcedimentosArquivo = [];
            $procedimentosInconsistentes = [];
            
            foreach($procedimentosArquivo as $procedimentoArquivo) {
                $procedimentoArquivo = array_values($procedimentoArquivo);
                $idProcedimento = $procedimentoArquivo[0];

                if(empty($idProcedimento)) {
                    continue;
                }

                $coparticipacao = \App\Helpers\Utils::moneyReverse(trim($procedimentoArquivo[2]));
                $limite = $procedimentoArquivo[3];
                $carencia = $procedimentoArquivo[4];

                $idsProcedimentosArquivo[] = $idProcedimento;

                if(trim($limite) === "Ilimitado") {
                    $limite = 99999;
                }

                $planoProcedimento = $planosProcedimentos->where('id_procedimento', $idProcedimento)->first();
                
                if (!$planoProcedimento) {
                    $procedimentosInconsistentes[$idProcedimento][] = [
                        'tipo' => 'procedimento',
                        'valor_cadastrado' => null,
                        'valor_arquivo' => $idProcedimento,
                        'coparticipacao' => $coparticipacao,
                        'limite' => $limite,
                        'carencia' => $carencia,
                        'descricao' => 'Este procedimento não está vinculado ao plano'
                    ];
                    continue;
                }
                
                $procedimento = $planoProcedimento->procedimento;
                $planoGrupo = $planosGrupos->where('grupo_id', $procedimento->id_grupo)->first();
               
                $descricaoCoparticipacao = '';
                if(!isset($planoProcedimento->beneficio_valor)) {
                    $descricaoCoparticipacao = 'O valor da coparticipação deste procedimento no plano não está cadastrado';
                }elseif($planoProcedimento->beneficio_valor !== $coparticipacao) {
                    $descricaoCoparticipacao = 'O valor da coparticipação deste procedimento no plano não confere com o arquivo';
                }

                if($descricaoCoparticipacao){
                    $procedimentosInconsistentes[$idProcedimento][] = [
                        'tipo' => 'coparticipacao',
                        'valor_cadastrado' => $planoProcedimento->beneficio_valor,
                        'valor_arquivo' => $coparticipacao,
                        'descricao' => $descricaoCoparticipacao
                    ];
                }
                
                if ($planoGrupo->dias_carencia != $carencia) {
                    $procedimentosInconsistentes[$idProcedimento][] = [
                        'tipo' => 'carencia',
                        'valor_cadastrado' => $planoGrupo->dias_carencia,
                        'valor_arquivo' => $carencia,
                        'descricao' => 'A carência deste procedimento no plano não confere com o arquivo'
                    ];
                }

                if ($planoGrupo->quantidade_usos != $limite) {
                    $procedimentosInconsistentes[$idProcedimento][] = [
                        'tipo' => 'limites',
                        'valor_cadastrado' => $planoGrupo->quantidade_usos,
                        'valor_arquivo' => $limite,
                        'descricao' => 'O limite deste procedimento no plano não confere com o arquivo.'
                    ];
                }
            }

            $procedimentosExcedentes = array_diff($idsProcedimentosPlano, $idsProcedimentosArquivo);

            foreach ($procedimentosExcedentes as $procedimentoExcedente) {
                $procedimentosInconsistentes[$procedimentoExcedente][] = [
                    'tipo' => 'procedimentoExcedente',
                    'valor_cadastrado' => $procedimentoExcedente,
                    'valor_arquivo' => null,
                    'descricao' => 'Este procedimento está vinculado ao plano no sistema, mas não consta no arquivo'
                ];
            }

            return $procedimentosInconsistentes;

        } catch (\Exception $e) {
            self::setError("Não foi possível checar os procedimentos:\n{$e->getMessage()}");
            return redirect()->back();
        }

    }

    public function corrigirInconsistencias(array $procedimentosInconsistentes)
    {
        $planosGrupos = $this->plano->planosGrupos;
        $planosProcedimentos = $this->plano->planosProcedimentos;

        foreach($procedimentosInconsistentes as $idProcedimento => $inconsistencias){
            $planoProcedimento = $planosProcedimentos->where('id_procedimento', $idProcedimento)->first();
            foreach($inconsistencias as $inconsistencia){
                
                $procedimento = Procedimentos::find($idProcedimento);

                if(!$procedimento){
                    continue;
                }

                $planoGrupo = $planosGrupos->where('grupo_id', $procedimento->id_grupo)->first();

                if(!$planoGrupo){
                    $planoGrupo = PlanosGrupos::create([
                        'id' => $procedimento->id_grupo,
                        'liberacao_automatica' => false,
                        'dias_carencia' => $inconsistencia['carencia'],
                        'quantidade_usos' => $inconsistencia['limite'],
                        'valor_desconto' => 0.00,
                        'plano_id' => $this->plano->id,
                        'grupo_id' => $procedimento->id_grupo
                    ]);
                }

                if($inconsistencia['tipo'] == 'procedimento'){
                    $planoProcedimento = PlanosProcedimentos::create([
                        'id_procedimento' => $idProcedimento,
                        'id_plano' => $this->plano->id,
                        'observacao' => 'Criado automaticamente através da checagem de inconsistências',
                        'valor_cliente' => null,
                        'valor_credenciado' => null,
                        'beneficio_tipo' => 'fixo',
                        'beneficio_valor' => $inconsistencia['coparticipacao']
                    ]);
                }

                if($inconsistencia['tipo'] == 'coparticipacao'){
                    $planoProcedimento->update([
                        'beneficio_valor' => $inconsistencia['valor_arquivo'],
                        'beneficio_tipo' => 'fixo',
                    ]);
                }

                if($inconsistencia['tipo'] == 'carencia'){
                    $planoGrupo->update(['dias_carencia' => $inconsistencia['valor_arquivo']]);
                }

                if($inconsistencia['tipo'] == 'limites'){
                    $planoGrupo->update(['quantidade_usos' => $inconsistencia['valor_arquivo']]);
                }

                if($inconsistencia['tipo'] == 'procedimentoExcedente'){
                    $planoProcedimento->delete();
                    $grupo = $planoGrupo->grupo;
                    $procedimentosPorGrupo = $this->plano->procedimentosPorGrupo($grupo);
                    if ($procedimentosPorGrupo->count() === 0) {
                        $planoGrupo->delete();
                    }
                }
            }
        }
    }
}