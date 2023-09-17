<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DadosTemporais;
use Illuminate\Support\Facades\Auth;

class DadosTemporaisController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public static function cron()
    {
        Auth::loginUsingId(1, TRUE);

        //Registra os cancelamentos diários
        DadosTemporais::registrarCancelamentos();

        //Registra os novos cadastros do dia
        DadosTemporais::registrarVidasAtivas();

        //Registra vidas inativas no dia
        DadosTemporais::registrarVidasInativas();

        //Registra vidas mensais
        DadosTemporais::registrarVidasMensais();

        //Registra vidas anuais
        DadosTemporais::registrarVidasAnuais();

        //Registra nota NPS
        DadosTemporais::registrarNPS();

        //Registra sinistralidade diária
        DadosTemporais::registrarSinistralidadeMensal();

        //Registra vendas
        DadosTemporais::registrarVendas();

        //Registra inadimplência mensal
        DadosTemporais::registrarAtrasoMensal();

        //Registra faturamento mensal
        DadosTemporais::registrarFaturamentoMensal();

        //Registra media recorrente mensal
        DadosTemporais::registrarMRM();

        //Registra faturamento mensal previsto
        DadosTemporais::registrarFaturamentoMensalPrevisto();

        //Registra a rentabilidade mensal dos planos
        DadosTemporais::registrarRentabilididadeMensalDosPlanos();
        
    }

    public static function cronSuperlogica()
    {
        //Dados temporais do Superlógica
        DadosTemporais::registrarSuperlogicaFinanceiro();
    }

}
