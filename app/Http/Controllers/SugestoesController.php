<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAlteracoesCadastraisRequest;
use App\Http\Requests\UpdateAlteracoesCadastraisRequest;
use App\Repositories\AlteracoesCadastraisRepository;
use App\Http\Controllers\AppBaseController;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Entrust;

class SugestoesController extends AppBaseController
{
    private $filters;

    public function __construct()
    {
        $this->filters = [
            's' => function(Request $request, \Illuminate\Database\Eloquent\Builder &$query) {
                if($request->filled('s')) {
                    $s = '%' . $request->get('s') . '%';
                    $query->where(function(Builder $query) use ($s) {
                       $query->where('titulo', 'LIKE', $s)
                             ->orWhere('corpo', 'LIKE', $s);
                    })->orWhere(function(Builder $query) use ($s) {
                        $query->whereHas('user', function (Builder $query) use ($s) {
                            $query->where('email', 'LIKE', $s)
                                ->orWhere('name', 'LIKE', $s);
                        });
                    });
                }
                return $request->get('s');
            },
            'status' => function(Request $request, Builder &$query) {
                $arquivadas = false;
                if($request->filled('status')) {
                    $status = $request->get('status');
                    if(!is_array($status)) {
                        $status = [$status];
                    }

                    $query->where(function(Builder $queryWhere) use ($status, &$arquivadas) {
                        $status = collect($status);
                        if($status->contains('ARQUIVADA')) {
                            $arquivadas = true;
                            $queryWhere->orWhere('arquivada', 1);
                        } else {
                            $arquivadas = false;
                        }

                        if($status->contains('LIDA')) {
                            $queryWhere->orWhere('lido', 1);
                        }
                        if($status->contains('REALIZADA')) {
                            $queryWhere->orWhere('realizado', 1);
                        }
                    });
                }

                if(!$arquivadas) {
                    $query->where('arquivada', '=', 0);
                }
                return $request->get('status');
            },
            'data' => function(Request $request, Builder &$query) {
                $dates = self::getDates($request);
                list($start, $end) = array_values($dates);
                $query->whereBetween('created_at', [$start, $end]);
                return $dates;
            }
        ];
    }

    private function rules() {
        return [
            'titulo' => 'required|string|max:191',
            'corpo'  => 'required'
        ];
    }

    public function resolver($id)
    {
        if(!\Entrust::can('resolver_sugestoes')) {
            return self::notAllowed();
        }

        $user = auth()->user();

        $sugestoes = \App\Models\Sugestoes::find($id);
        if(!$sugestoes) {
            self::setError('404 - Sugestão não encontrada');
            return back();
        }

        if(!$sugestoes->lido) {
            $sugestoes->lido = 1;
            $sugestoes->visto_por = $user->id;
        }
        $sugestoes->realizado = 1;
        $sugestoes->realizador = $user->id;
        $sugestoes->update();

        self::toast('Sugestão atualizada para', 'REALIZADA', 'font-green-meadow');
        return back();
    }

    public function ler($id)
    {
        if(!\Entrust::can('ler_sugestoes')) {
            return self::notAllowed();
        }

        $user = auth()->user();
        $sugestoes = \App\Models\Sugestoes::find($id);
        if(!$sugestoes) {
            self::setError('404 - Sugestão não encontrada');
            return back();
        }

        $sugestoes->lido = 1;
        $sugestoes->visto_por = $user->id;
        $sugestoes->update();

        self::toast('Sugestão atualizada para', 'LIDA');
        return back();
    }

    public function priorizar(Request $request)
    {
        if(!\Entrust::can('priorizar_sugestoes')) {
            return self::notAllowed();
        }

        $id = $request->get('id_sugestao');
        $prioridade = $request->get('prioridade');
        $sugestoes = \App\Models\Sugestoes::find($id);
        if(!$sugestoes) {
            self::setError('404 - Sugestão não encontrada');
            return back();
        }
        $sugestoes->prioridade = $prioridade;
        $sugestoes->update();

        self::toast('Sugestão priorizada para', $prioridade);
        return back();
    }

    public function arquivar($id)
    {
        if(!\Entrust::can('arquivar_sugestoes')) {
            return self::notAllowed();
        }

        $sugestoes = \App\Models\Sugestoes::find($id);
        if(!$sugestoes) {
            self::setError('404 - Sugestão não encontrada');
            return back();
        }
        $sugestoes->arquivada = 1;
        $sugestoes->update();

        self::toast('Sugestão atualizada para', "ARQUIVADA", 'font-grey');
        return back();
    }

    public function index(Request $request)
    {
        if(!\Entrust::can('listar_sugestoes')) {
            return self::notAllowed();
        }

        $query = \App\Models\Sugestoes::query();
        $params = [];
        foreach($this->filters as $param => $filter) {
            $params[$param] = $filter($request, $query);
        }
        $query->orderBy('lido', 'ASC')
              ->orderBy('realizado', 'ASC')
              ->orderBy('arquivada', 'ASC');
        $sql = $query->toSql();

        $sugestoes = $query->get();
        return view('sugestoes.index', [
            'sugestoes' => $sugestoes,
            'params' => $params
        ]);
    }

    public function store(Request $request)
    {
        $v = validator($request->all(),$this->rules());
        if($v->fails()) {
            if ($v->fails()) {
                $messages = join("\n", $v->getMessageBag()->all());
                $messages = str_replace('file', 'O arquivo', $messages);
                self::setError($messages, 'Oops.');

                return back()
                    ->withErrors($v)
                    ->withInput();
            }
        }

        $sugestao = new \App\Models\Sugestoes();
        $sugestao->fill($request->all());
        $sugestao->id_usuario = auth()->user()->id;
        $sugestao->prioridade = 1;
        $sugestao->save();

        self::toast('Sugestão enviada com sucesso');
        return back();
    }
}
