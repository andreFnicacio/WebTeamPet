<?php

namespace App\Http\Controllers;

use App\Models\ComunicadosCredenciados;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Guides\Entities\HistoricoUso;
use Modules\Veterinaries\Entities\Prestadores;

class HomeController extends Controller
{
    private $registeredHome = [
        'CLIENTE' => 'area_cliente.v2.home',
        'CLINICAS' => 'clinics::home'
    ];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getRegisteredHome($role) {
        if(array_key_exists($role, $this->registeredHome)) {
            return view($this->registeredHome[$role]);
        }

        return 'home';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Entrust::hasRole(['CLIENTE'])) {
            return $this->homeCliente();
        } else if (\Entrust::hasRole(['ADMINISTRADOR'])) {
            return $this->homeDashboard();
        } else if (\Entrust::hasRole(['CLINICAS'])) {
            return $this->homeClinicas();
        }
        return view('home');
    }

    public function homeCliente()
    {
        return $this->getRegisteredHome('CLIENTE');
    }

    public function homeDashboard()
    {
        return view('dashboard.home');
    }

    public function homeClinicas()
    {

        $clinica = (new \Modules\Clinics\Entities\Clinicas)->where('id_usuario', Auth::user()->id)->first();

        if ($clinica->aceite_urh) {

            $start = (new Carbon())->today()->startOfMonth();
            $end = (new Carbon())->today()->endOfMonth();

            $guias = $clinica->historicoUsos()
                ->where('status', (new HistoricoUso())::STATUS_LIBERADO)
                ->where(function($query) use ($start, $end) {
                    $query->where(function($query) use ($start, $end) {
                        $query->where('tipo_atendimento', "!=", HistoricoUso::TIPO_ENCAMINHAMENTO)
                            ->whereBetween('historico_uso.created_at', [$start, $end]);
                    });
                    $query->orWhere(function($query) use ($start, $end) {
                        $query->where('tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                            ->whereBetween('historico_uso.realizado_em', [$start, $end]);
                    });
                });

            $guiasGlosadas = $clinica->historicoUsos()
                ->where('status', (new HistoricoUso())::STATUS_LIBERADO)
                ->where(function($query) use ($start, $end) {
                    $query->where(function($query) use ($start, $end) {
                        $query->where('tipo_atendimento', "!=", HistoricoUso::TIPO_ENCAMINHAMENTO)
                            ->whereBetween('historico_uso.created_at', [$start, $end]);
                    });
                    $query->orWhere(function($query) use ($start, $end) {
                        $query->where('tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                            ->whereBetween('historico_uso.realizado_em', [$start, $end]);
                    });
                })
                ->where('glosado', '!=', '0');

            $comunicadosCredenciados = ComunicadosCredenciados::where('published_at', '<=', Carbon::now()->format('Y-m-d H:i'))
                ->orderBy('published_at', 'DESC')
                ->orderBy('created_at', 'DESC')
                ->limit(10)
                ->get();

            $prestadoresRating = [];
            $clinicaPrestadores = $clinica->historicoUsos()
                ->where(function($query) use ($start, $end) {
                    $query->where(function($query) use ($start, $end) {
                        $query->where('tipo_atendimento', "!=", HistoricoUso::TIPO_ENCAMINHAMENTO)
                            ->whereBetween('historico_uso.created_at', [$start, $end]);
                    });
                    $query->orWhere(function($query) use ($start, $end) {
                        $query->where('tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                            ->whereBetween('historico_uso.realizado_em', [$start, $end]);
                    });
                })
                ->whereNotNull('id_prestador')
                ->groupBy('id_prestador')
                ->pluck('id_prestador');
            foreach ($clinicaPrestadores as $cp) {
                $prestador = Prestadores::find($cp);
                if(!$prestador) {
                    continue;
                }

                $prestadoresRating[] = [
                    'nome' => $prestador->nome,
                    'rating' =>$prestador->rating(),
                    'badge' =>$prestador->ratingBadge()
                ];
            }

            $prestadoresRating = collect($prestadoresRating)->sortBy('rating')->reverse()->toArray();

            $extrato = $clinica->getExtratoMensal($start, $end);

            $valorUrhAcumulada = 0;
            $graficoMovimentacaoAcumulada = [];
            $graficoUrhStart = (new Carbon())->today()->startOfMonth();
            $graficoUrhEnd = (new Carbon())->today()->addMonth()->startOfMonth();
            while ($graficoUrhStart->format('Y-m-d') != $graficoUrhEnd->format('Y-m-d')) {
                $valorExtratoDia =  $extrato->where('data', $graficoUrhStart->format('d/m/Y'))->sum('urh');
                $graficoMovimentacaoAcumulada[$graficoUrhStart->format('d/m/Y')] = $valorExtratoDia;
                $valorUrhAcumulada += $valorExtratoDia;
                $graficoUrhStart->addDay();
            }

            $data = [
                'clinica' => $clinica,
                // 'guias' => $guias->get(),
                'guiasGlosadas' => $guiasGlosadas->get(),
                'valorUrhAcumulada' => round($valorUrhAcumulada),
                'graficoMovimentacaoAcumulada' => $graficoMovimentacaoAcumulada,
                'comunicadosCredenciados' => $comunicadosCredenciados,
                'prestadoresRating' => $prestadoresRating,
                'extrato' => $extrato->take(10),
                'contadores' => [
                    'guiasEmitidas' => $guias->count(),
                    'guiasGlosadas' => $guiasGlosadas->count(),
                ]
            ];

            return view('clinics::home')->with($data);

        } else {
            return view('clinics::manual_credenciado');
        }
    }
}

