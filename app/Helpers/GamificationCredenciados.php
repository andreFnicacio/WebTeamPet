<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento
 * Date: 28/03/19
 * Time: 15:02
 */

namespace App\Helpers;

use App\Models\MovimentacoesCredenciados;
use Carbon\Carbon;
use Modules\Guides\Entities\HistoricoUso;

//use App\Models\Urh;

class GamificationCredenciados
{

    private $hasExameLab = false;
    private $hasExameImg = false;
    private $hasConsulta = false;

    private $historicoUso = null;
    private $guiaConsulta = null;
    private $valorMomentoConsulta = null;

    private $idClinica = null;

//    private $urh = null;

    private static $idRetornoConsulta = '10101012';

    public static $gruposConsultas = [
        '10100',
        '10102'
    ];

    public static $grupoExameLab = '10101011';
    public static $grupoExameImg = '26100';

    // const REGRA_GAMIFICATION_EXAME_LAB = ['descricao' => 'Emissão de Exame Laboratorial', 'valor' => -0.2];
    // const REGRA_GAMIFICATION_EXAME_IMG = ['descricao' => 'Emissão de Exame por Imagem', 'valor' => -0.3];

    const REGRA_GAMIFICATION_FORMACAO_2 = ['descricao' => 'Tempo de Formação - Até 2 anos', 'valor' => 0];
    const REGRA_GAMIFICATION_FORMACAO_2_5 = ['descricao' => 'Tempo de Formação - De 2 a 5 anos', 'valor' => 0.05];
    const REGRA_GAMIFICATION_FORMACAO_5_10 = ['descricao' => 'Tempo de Formação - De 5 a 10 anos', 'valor' => 0.1];
    const REGRA_GAMIFICATION_FORMACAO_10 = ['descricao' => 'Tempo de Formação - Acima de 10 anos', 'valor' => 0.2];

    // const REGRA_GAMIFICATION_AV_ATEND_1 = ['descricao' => 'Avaliação - 1 Estrela', 'valor' => -0.2];
    // const REGRA_GAMIFICATION_AV_ATEND_2 = ['descricao' => 'Avaliação - 2 Estrelas', 'valor' => -0.1];
    // const REGRA_GAMIFICATION_AV_ATEND_3 = ['descricao' => 'Avaliação - 3 Estrelas', 'valor' => 0];
    // const REGRA_GAMIFICATION_AV_ATEND_4 = ['descricao' => 'Avaliação - 4 Estrelas', 'valor' => 0.1];
    // const REGRA_GAMIFICATION_AV_ATEND_5 = ['descricao' => 'Avaliação - 5 Estrelas', 'valor' => 0.2];

    public function __construct($numeroGuia = null)
    {
        if ($numeroGuia) {
            $historicoUso = HistoricoUso::where('numero_guia', $numeroGuia)->first();
            $this->historicoUso = $historicoUso;

            $this->hasConsulta = $this->hasConsulta();
            $this->hasExameLab = $this->hasExameLab();
            $this->hasExameImg = $this->hasExameImg();

            if (($this->hasExameLab || $this->hasExameImg) && $this->historicoUso->id_solicitador) {
                $this->idClinica = $this->historicoUso->id_solicitador;
            } else {
                $this->idClinica = $this->historicoUso->id_clinica;
            }

            $this->guiaConsulta = $this->getConsulta($this->hasConsulta);

            if ($this->guiaConsulta) {
                $this->valorMomentoConsulta = $this->guiaConsulta->valor_momento;
            }
//            $this->urh = Urh::find($this->guiaConsulta->clinica->id_urh);
        }
    }

    public function applyGamificationConsulta()
    {

        if ($this->hasConsulta && ($this->guiaConsulta->id_procedimento != self::$idRetornoConsulta)) {

            // Criar movimentação Formação Prestador
            $prestador = $this->guiaConsulta->prestador;
            $tempoFormacao = $prestador->tempoFormacao();

            $regraGame = $this::REGRA_GAMIFICATION_FORMACAO_2;
            if ($tempoFormacao) {
                if ($tempoFormacao >= 2 && $tempoFormacao < 5) {
                    $regraGame = $this::REGRA_GAMIFICATION_FORMACAO_2_5;
                } elseif ($tempoFormacao >= 5 && $tempoFormacao < 10) {
                    $regraGame = $this::REGRA_GAMIFICATION_FORMACAO_5_10;
                } elseif ($tempoFormacao > 10) {
                    $regraGame = $this::REGRA_GAMIFICATION_FORMACAO_10;
                }
            }

            //TODO: Desabilitando regra de Gamification de Credenciados
            $regraGame = $this::REGRA_GAMIFICATION_FORMACAO_2;

            //$this->setMovimentacao(MovimentacoesCredenciados::TIPO_GAMIFICATION_TEMPO_FORMACAO, $regraGame);
        }

        // if ($this->hasExameLab) {
        //     if (!self::checkRegraConsultaAplicada($this->historicoUso->id)) {
        //         // Criar movimentação Exame Laboratorial
        //         $regraGame = $this::REGRA_GAMIFICATION_EXAME_LAB;
        //         $this->setMovimentacao(MovimentacoesCredenciados::TIPO_GAMIFICATION_CONSULTA, $regraGame, $this->historicoUso->id);
        //     }
        // }

        // if ($this->hasExameImg) {
        //     if (!self::checkRegraConsultaAplicada($this->historicoUso->id)) {
        //         // Criar movimentação Exame Imagem
        //         $regraGame = $this::REGRA_GAMIFICATION_EXAME_IMG;
        //         $this->setMovimentacao(MovimentacoesCredenciados::TIPO_GAMIFICATION_CONSULTA, $regraGame, $this->historicoUso->id);
        //     }
        // }

    }

    // public function applyGameficationAvaliacaoCredenciado($avaliacao)
    // {
    //     $registrado = (new MovimentacoesCredenciados)->where('tipo', MovimentacoesCredenciados::TIPO_GAMIFICATION_AVALIACAO_ATENDIMENTO)->where('id_guia_origem', $this->historicoUso->id)->exists();

    //     if($registrado) {
    //         return false;
    //     }

    //     try {
    //         $reflection = new \ReflectionClass(self::class);
    //     } catch (\Exception $e) {
    //         return false;
    //     }

    //     $regra = $reflection->getConstant("REGRA_GAMIFICATION_AV_ATEND_".$avaliacao);
    //     if(!$regra) {
    //         return false;
    //     }

    //     $this->setMovimentacao(MovimentacoesCredenciados::TIPO_GAMIFICATION_AVALIACAO_ATENDIMENTO, $regra, $this->historicoUso->id, $avaliacao);
    // }

    public function setMovimentacao($tipo, $regraGame, $idGuiaOrigem = null, $estrelas = null)
    {
        MovimentacoesCredenciados::create([
            'tipo' => $tipo,
            'descricao' => $regraGame['descricao'],
            'valor' => floor($this->valorMomentoConsulta * $regraGame['valor']),
            'id_clinica' => $this->idClinica,
            'id_guia_consulta' => $this->guiaConsulta->id,
            'id_guia_origem' => $idGuiaOrigem,
            'estrelas' => $estrelas
        ]);
    }

    public function getConsulta($hasConsulta)
    {
        // dd($hasConsulta);
        if (!$hasConsulta) {
            $consulta = $this->getConsultaPassada();
        } else {
            $consulta = $this->historicoUso;
        }
        return $consulta;
    }

    private function getConsultaPassada()
    {
        $end = Carbon::today()->endOfDay();
//        $start = Carbon::today()->subDay(30)->startOfDay();
        $consultaPassada = HistoricoUso::where('status', 'LIBERADO')
            ->where('numero_guia', '!=', $this->historicoUso->numero_guia)
            ->where(function($query) use ($end) {
                $query->where(function($query) use ($end) {
                    $query->where('tipo_atendimento', "!=", HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->where('created_at', '<=', $end);
                });
                $query->orWhere(function($query) use ($end) {
                    $query->where('tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->where('realizado_em', '<=', $end);
                });
            })
            ->whereHas('procedimento', function ($query) {
                $query->whereIn('id_grupo', self::$gruposConsultas);
                $query->where('id', '!=', self::$idRetornoConsulta);
            })
            ->where('id_clinica', $this->idClinica)
            ->where('id_pet', $this->historicoUso->id_pet)
            ->orderBy('id', 'DESC')
            ->first();
        return $consultaPassada;
    }

    public function hasConsulta()
    {
        return HistoricoUso::where('numero_guia', $this->historicoUso->numero_guia)
            ->whereHas('procedimento', function ($query) {
                $query->whereIn('id_grupo', array_merge(self::$gruposConsultas));
            })
            ->where('status', 'LIBERADO')
            ->exists();
    }

    public function hasExameLab()
    {
        return HistoricoUso::where('numero_guia', $this->historicoUso->numero_guia)
            ->whereHas('procedimento', function ($query) {
                $query->where('id_grupo', self::$grupoExameLab);
            })
            ->where('status', 'LIBERADO')
            ->exists();
    }

    public function hasExameImg()
    {
        return HistoricoUso::where('numero_guia', $this->historicoUso->numero_guia)
            ->whereHas('procedimento', function ($query) {
                $query->where('id_grupo', self::$grupoExameImg);
            })
            ->where('status', 'LIBERADO')
            ->exists();
    }

    public function checkRegraConsultaAplicada($idConsulta)
    {
        $exists = (new MovimentacoesCredenciados)->where('tipo', MovimentacoesCredenciados::TIPO_GAMIFICATION_CONSULTA)->where('id_guia_consulta', $idConsulta)->exists();
        return $exists;
    }

    /**
     * @param $consulta
     * @return Carbon
     */
    protected function getDataRealizacao($consulta)
    {
        if ($consulta->tipo_atendimento == HistoricoUso::TIPO_ENCAMINHAMENTO) {
            return $consulta->realizado_em;
        } else {
            return $consulta->created_at;
        }
    }

}