<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Projeto;
use App\Models\Tarefa;
use App\Models\Timesheet;

class TimesheetController extends Controller
{
    public function departamentos(Request $request)
    {
        return Departamento::all();
    }

    public function projetos(Request $request)
    {
        if($request->filled('id_departamento')) {
            return Projeto::where('id_departamento', $request->get('id_departamento'))->get();
        }
        return Projeto::all();
    }

    public function projeto(Request $request)
    {
        if(!$request->filled('id_departamento')) {
            return [
                'status' => false
            ];
        }

        $projeto = Projeto::create(array_merge($request->all(),[
            'id_responsavel' => auth()->user()->id
        ]));

        return [
            'status' => empty($projeto),
            'data' => $projeto
        ];
    }

    public function tarefas(Request $request)
    {
        if($request->filled('id_projeto')) {
            return Tarefa::where('id_projeto', $request->get('id_projeto'))->get();
        }
        return Tarefa::all();
    }

    public function tarefa(Request $request)
    {
        if(!$request->filled('id_projeto')) {
            return [
                'status' => false
            ];
        }

        $tarefa = Tarefa::create($request->all());
        return [
            'status' => empty($tarefa),
            'data' => $tarefa
        ];
    }


    public function status($json = true)
    {
        if(!auth()->user()) {
            return false;
        }

        $status = Timesheet::where('id_usuario', auth()->user()->id)->whereNull('fim')->exists();
        if($json) {
            return [
                'status' => $status
            ];
        }

        return $status;
    }

    public function corrente()
    {
        if(!$this->status(false)) {
            return [
                'timesheet' => null
            ];
        }

        $user = auth()->user()->id;
        $timesheet = Timesheet::where('id_usuario', auth()->user()->id)->whereNull('fim')->first();

        if(!$timesheet) {
            return [
                'timesheet' => null
            ];
        }
        $tarefa = $timesheet->tarefa()->first();
        $projeto = $tarefa->projeto()->first();
        $departamento = $projeto->departamento()->first();

        return [
            'timesheet' => $timesheet,
            'task' => $tarefa,
            'project' => $projeto,
            'department' => $departamento,
        ];
    }

    public function iniciar(Request $request)
    {
        if($this->status(false)) {
            return $this->corrente();
        }

        return Timesheet::create([
            'id_usuario' => auth()->user()->id,
            'id_tarefa' => $request->get('id_tarefa'),
            'inicio' => new Carbon(),
        ]);
    }

    public function parar(Request $request)
    {
        if(!$this->status(false)) {
            return [
                'status' => false
            ];
        }

        $timesheet = Timesheet::where('id_usuario', auth()->user()->id)->whereNull('fim')->first();
        $timesheet->fim = new Carbon();
        $timesheet->duracao = $timesheet->fim->diffInSeconds($timesheet->inicio);
        $operation = $timesheet->update();
        return [
            'status' => $operation
        ];
    }

    public function historico(Request $request)
    {
        $query = Timesheet::where('id_usuario', auth()->user()->id)->orderBy('id','DESC');

        $timesheets = $query->get();

        return $timesheets->map(function($t) {
            $sheet = new \stdClass();

            $tarefa = $t->tarefa()->first();
            $projeto = $tarefa->projeto()->first();
            $departamento = $projeto->departamento()->first();

            $color = Utils::colorForLetter($projeto->nome);
            $sheet->id = $t->id;
            $sheet->_project = $projeto;
            $sheet->_department = $departamento;
            $sheet->_task = $tarefa;

            $sheet->projeto = [
                'nome' => $departamento->nome . ' > ' .
                          $projeto->nome . ' > ' .
                          $tarefa->nome,
                'sigla' => $color['letter'],
                'classes' => [
                    'bg-font-' . $color['color'],
                    'bg-' . $color['color']
                ]
            ];

            $sheet->inicio = $t->inicio->format('d/m/Y H:i:s');
            if($t->fim) {
                $sheet->fim = $t->fim->format('d/m/Y H:i:s');
            } else {
                $sheet->fim = null;
            }

            if($t->duracao) {
                $sheet->duracao = Utils::secondsToFormattedHours($t->duracao);
            } else {
                $sheet->duracao = null;
            }

            return $sheet;
        });
    }

    public function timesheet(Request $request)
    {
        $sheet = $request->get('sheet');

        $s = Timesheet::findOrFail($sheet['id']);

        $s->inicio = Carbon::createFromFormat('d/m/Y H:i:s', $sheet['inicio']);
        $s->fim = Carbon::createFromFormat('d/m/Y H:i:s', $sheet['fim']);
        $s->duracao = $s->fim->diffInSeconds($s->inicio);

        $s->save();

        return $s;
    }
}
