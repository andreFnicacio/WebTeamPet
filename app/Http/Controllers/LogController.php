<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Helpers\Utils;
use Entrust;

class LogController extends Controller
{
    protected $table = 'log';

    public static function can()
    {
        return Entrust::hasRole(['ADMINISTRADOR', 'ATENDIMENTO', 'AUTORIZADOR', 'FINANCEIRO', 'AUDITORIA'. 'GERENCIAL', 'RH', 'CADASTRO', 'COBRANCA']);
    }

    public function index(Request $request)
    {
        if(!self::can()) {
            abort(403);
        }

        return view('logs.index');
    }

    public function search(Request $request)
    {
        if(!self::can()) {
            abort(403);
        }

    	$filters = [];
    	$areas = $request->get('areas', null);
    	$priorities = $request->get('priorities', null);
    	$events = $request->get('events', null);
        $message = $request->get('message', null);
        $last = $request->get('last', null);
        $defaultDays = 2;
        if($areas || $message) {
            $defaultDays = 15;
        }
        $days = $request->get('days', $defaultDays);

        $query = \App\Models\Log::orderBy('id', 'DESC')->select([
        	'id as id',
        	'mensagem as message', 
        	'area as area', 
        	'importancia as priority',
        	'evento as event',
        	'created_at as created_at',
            'executor as author'
        ]);

        if($areas) {
            $query->whereIn('area', $areas);
        }
        if($priorities) {
            $query->whereIn('importancia', $priorities);
        }
        if($events) {
            $query->whereIn('evento', $events);
        }

        if($message) {
            $query->where('mensagem', 'like', '%' . $message . '%');
        }
        if($days) {
            $query->whereDate('created_at', '>=', \Carbon\Carbon::now()->subDays($days));
        }
        if($last) {
            $query->where('id', '>', $last);
        }
      //  $query->limit('1000');

        $logs = $query->get();

        return [
        	'logs' => $logs->map(function($l) {
        		$l->created = \Carbon\Carbon::createFromTimestamp($l->created_at)->format(Utils::BRAZILIAN_DATETIME);
        		$user = User::find($l->author);
        		$l->author  = null;
        		if($user) {
        		    $l->author = $user->name;
                }
        		return $l;
        	})
        ];
    }

    public function parameters(Request $request) {
        if(!self::can()) {
            abort(403);
        }

    	$cacheTime = 60 * 60; //as seconds

    	$areas = Cache::remember('log__parameters__areas', $cacheTime, function () {
		    $areas = Log::select('area')->distinct()->orderBy('area')->get();
		    $areas = $areas->map(function($a) {
	    		$a = mb_convert_case($a->area, MB_CASE_UPPER);
	    		return $a;
	    	})->values();
	    	return $areas;
		});

		$priorities = Cache::remember('log__parameters__priorities', $cacheTime, function () {
    		$priorities = Log::select('importancia')->distinct()->get();
	    	$priorities = $priorities->map(function($p) {
	    		$p = mb_convert_case($p->importancia, MB_CASE_UPPER);
	    		return $p;
	    	})->values();
	    	return $priorities;
		});
    	

		$events = Cache::remember('log__parameters__events', $cacheTime, function () {
    		$events = Log::select('evento')->distinct()->orderBy('evento')->get();
	    	$events = $events->map(function($e) {
	    		$e = mb_convert_case($e->evento, MB_CASE_UPPER);
	    		return $e;
	    	})->values();
	    	return $events;
		});


    	return [
    		'areas' => $areas,
    		'priorities' => $priorities,
    		'events' => $events
    	];
    }
}
