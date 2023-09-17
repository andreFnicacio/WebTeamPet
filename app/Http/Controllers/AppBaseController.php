<?php

namespace App\Http\Controllers;

use App\Helpers\API\Zenvia\Message;
use App\Helpers\Utils;
use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogPriority;
use App\Models\Clientes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent as Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;

/**
 * @SWG\Swagger(
 *   basePath="/api/v1",
 *   @SWG\Info(
 *     title="Laravel Generator APIs",
 *     version="1.0.0",
 *   )
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
class AppBaseController extends Controller
{
    /**
     * @param Request $request
     * @return array
     */
    protected static function getDates(Request $request) {
        if($request->filled('start')) {
            $start = Carbon::createFromFormat('d/m/Y', $request->get('start'));
            if(!$request->get('end')) {
                $end = $start->copy()->lastOfMonth();
            } else {
                $end = Carbon::createFromFormat('d/m/Y', $request->get('end'));
            }
        } else {
            $start = new Carbon('first day of this month');
            $end = new Carbon('last day of this month');
        }
        $start->setTime(0,0,0);
        $end->setTime(23,59,59);

        return [
            'start' => $start,
            'end'   => $end
        ];
    }

    public static function setSelected($find, $params, $key) {
        $selected = "selected=selected";
        if(!isset($params[$key])) {
            return null;
        }
        if(is_array($params[$key])) {
            if(in_array($find, $params[$key])) {
                return $selected;
            }
        } else {
            if($find === $params[$key]) {
                return $selected;
            }
        }

        return null;
    }

    public static $ufs = [
        "AC",
        "AL",
        "AP",
        "AM",
        "BA",
        "CE",
        "DF",
        "ES",
        "GO",
        "MA",
        "MT",
        "MS",
        "MG",
        "PA",
        "PB",
        "PR",
        "PE",
        "PI",
        "RJ",
        "RN",
        "RS",
        "RO",
        "RR",
        "SC",
        "SP",
        "SE",
        "TO",
    ];

    public static function notAllowed($message = "Você não é autorizado a acessar essa área ou praticar essa ação.", $type = 'html') {
        if(strtolower($type) === 'json') {
            return [
                'status' => false,
                'message' => $message,
                'code'    => 403
            ];
        } else if (strtolower($type) === 'html') {
            return view('403');
        }
    }

    public function sendResponse($result, $message, $code = 200)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result), $code);
    }

    public function sendError($error, $code = 404)
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }

    public function csvToArray($filename = '', $delimiter = ',')
    {
        return Utils::csvToArray($filename, $delimiter);
    }

    public function pagination(Request $request, $pageTotal, $searchTotal, $limit) {
        $page = $request->get('page') ?: 1;
        $params = $_GET;
        $step = 2;
        $maxPages = ceil($searchTotal/$limit);
        if(isset($params['page'])) {
            unset($_GET['page']);
        }
        $givenQuery = http_build_query($_GET);
        if($page > $maxPages) {
            $page = 1;
        }
        if($pageTotal == $searchTotal) {
            return [
                'before' => [],
                'page' => 1,
                'after' => [],
            ];
        } else {
            $nextPages = $page+$step;
            if($nextPages > $maxPages) {
                $nextPages = $maxPages;
            }
            $pagesAfter = [];
            for ($i = $page+1; $i <=$nextPages; $i++) {
                $query = empty($givenQuery) ? "?page=$i" : "?page=$i&$givenQuery";
                $pagesAfter[] = [
                    'number' => $i,
                    'query' => $query
                ];
            }

            if($page-$step < 1) {
                $before = 1;
            } else {
                $before = $page-$step;
            }
            $pagesBefore = [];
            for($j = $before; $j < $page; $j++) {
                $query = empty($givenQuery) ? "?page=$j" : "?page=$j&$givenQuery";
                $pagesBefore[] = [
                    'number' => $j,
                    'query' => $query
                ];
            }

            return [
                'before' => $pagesBefore,
                'page' => $page,
                'after' => $pagesAfter,
            ];
        }
    }

    /**
     * Coloca um sweet alert na tela.
     * @param string $text
     * @param string $type
     * @param string $title
     */
    public static function setMessage($text, $type = 'success', $title = 'Sucesso') {
        if(($type == 'error' || $type == 'warning') && $title == 'Sucesso') {
            $title = "Oops...";
        }
        if($type == 'info' && $title == 'Sucesso') {
            $title = 'Alerta!';
        }

        $messages = session('messages', []);
        $messages = array_merge($messages, [
            [
                'style' => 'swal',
                'type' => $type,
                'title' => $title,
                'text' => $text
            ]
        ]);

        Session::flash("messages", $messages);
    }

    public static function setError($text, $title = 'Erro') {
        static::setMessage($text, 'error', $title);
    }

    public static function setSuccess($text, $title = 'Sucesso') {
        static::setMessage($text, 'success', $title);
    }

    public static function setInfo($text, $title = 'Informação') {
        static::setMessage($text, 'info', $title);
    }

    public static function setWarning($text, $title = 'Alerta') {
        static::setMessage($text, 'warning', $title);
    }

    public static function toast($intro, $status = null, $colorClass = 'font-blue')
    {
        $title = $intro;
        if($status) {
            $title .= '&nbsp; <span class=\"' . $colorClass . '\">' . $status . '</span>';
        }
        Session::flash("message", [
            'style' => 'toast',
            'title' => $title,
        ]);
    }

    /**
     * Fornece o cliente logado, caso seja um.
     * @return \Illuminate\Database\Eloquent\Model|null|static
     *
     */
    public static function loggedClient()
    {
        if(\Entrust::hasRole(['CLIENTE'])) {
            $client = Clientes::where('id_usuario', auth()->user()->id)->first();
            return $client;
        }
    }

    /**
     * Fornece o vendeedor logado, caso seja um.
     * @return \Illuminate\Database\Eloquent\Model|null|static
     *
     */
    public static function loggedVendedor()
    {
        if(\Entrust::hasRole(['INSIDE_SALES']) || \Entrust::hasRole(['ADMINISTRADOR'])) {
            $vendedor = (new \App\Models\Vendedores)->where('id_usuario', auth()->user()->id)->get()->first();
            return $vendedor;
        }
    }

    /**
     * Informa se estamos no horário de emergência
     * @return bool
     */
    public static function isEmergencia() {
        $today = new \Carbon\Carbon();

        if($today->dayOfWeek == 6) {
            $emergencia = ($today->hour >= 14);
        } else if ($today->dayOfWeek == 0) {
            $emergencia = true;
        } else {
            $emergencia = ($today->hour >= 20 || $today->hour < 8) ;
        }


        return $emergencia;
    }

    public static function sms($celular, $mensagem, $id = null)
    {
        $message = new Message($celular, $mensagem);
        return $message->send($id, null, true);
    }

    public static function colorBlendOpacity( $foreground, $opacity, $background=null )
    {
        static $colors_rgb=array(); // stores colour values already passed through the hexdec() functions below.
        $foreground = str_replace('#','',$foreground);
        if( is_null($background) )
            $background = 'FFFFFF'; // default background.

        $pattern = '~^[a-f0-9]{6,6}$~i'; // accept only valid hexadecimal colour values.
        if( !@preg_match($pattern, $foreground)  or  !@preg_match($pattern, $background) )
        {
            trigger_error( "Invalid hexadecimal colour value(s) found", E_USER_WARNING );
            return false;
        }

        $opacity = intval( $opacity ); // validate opacity data/number.
        if( $opacity>100  || $opacity<0 )
        {
            trigger_error( "Opacity percentage error, valid numbers are between 0 - 100", E_USER_WARNING );
            return false;
        }


        if( $opacity==100 )    // $transparency == 0
            return strtoupper( $foreground );
        if( $opacity==0 )    // $transparency == 100
            return strtoupper( $background );
        // calculate $transparency value.
        $transparency = 100-$opacity;

        if( !isset($colors_rgb[$foreground]) )
        { // do this only ONCE per script, for each unique colour.
            $f = array(  'r'=>hexdec($foreground[0].$foreground[1]),
                'g'=>hexdec($foreground[2].$foreground[3]),
                'b'=>hexdec($foreground[4].$foreground[5])    );
            $colors_rgb[$foreground] = $f;
        }
        else
        { // if this function is used 100 times in a script, this block is run 99 times.  Efficient.
            $f = $colors_rgb[$foreground];
        }

        if( !isset($colors_rgb[$background]) )
        { // do this only ONCE per script, for each unique colour.
            $b = array(  'r'=>hexdec($background[0].$background[1]),
                'g'=>hexdec($background[2].$background[3]),
                'b'=>hexdec($background[4].$background[5])    );
            $colors_rgb[$background] = $b;
        }
        else
        { // if this FUNCTION is used 100 times in a SCRIPT, this block will run 99 times.  Efficient.
            $b = $colors_rgb[$background];
        }

        $add = array(    'r'=>( $b['r']-$f['r'] ) / 100,
            'g'=>( $b['g']-$f['g'] ) / 100,
            'b'=>( $b['b']-$f['b'] ) / 100    );

        $f['r'] += intval( $add['r'] * $transparency );
        $f['g'] += intval( $add['g'] * $transparency );
        $f['b'] += intval( $add['b'] * $transparency );

        return sprintf( '%02X%02X%02X', $f['r'], $f['g'], $f['b'] );
    }

    public static $checklistProposta = [
        'Em hipótese alguma o Pet será atendido sem o microchip (ainda que seja caso de emergência);',
        'Para a realização da michochipagem é necessário que o Pet esteja com a carteira de vacinação em dia. Sendo filhote, deve estar sadio e ter mais de 60 dias; Não será considerada como "vacina em dia" a imunização viral (cinomose, leptospirose, parvorirose...) de fabricação nacional.',
        'Os atendimentos em caso de urgência e emergência só poderão ser realizados 72h após a microchipagem do Pet;',
        'Coberturas e Carências constam na Área do Cliente que está disponível no site: www.lifepet.com.br;',
        'Doenças e males preexistentes terão cobertura após 12 meses de contrato ininterrupto (Cobertura Parcial Temporária - CPT);',
        'No caso de atraso de pagamento, o serviço é automaticamente SUSPENSO (inclusive para urgências e emergências);',
        'Os pagamentos realizados em boleto ou cartão podem demorar até 72 horas para serem reconhecidos pelo banco;',
        'No caso de atraso superior a 60 dias, o plano é automaticamente CANCELADO, permanecendo as cobranças de mensalidades vencidas;',
        'Após 60 dias de inadimplência o débito em aberto poderá ser inscrito no SPC e SERASA (desde que previamente comunicado);',
        'REDE CREDENCIADA: www.lifepet.com.br/rede;',
        'No prazo de até 7 dias após a assinatura dessa proposta será enviado ao e-mail de cadastro, os seguintes documentos: i) Carta de boas vindas; ii) Login e senha da Área do Cliente;',
        'A vigência do contrato se inicia conforme data da assiantura dessa proposta.',
        'Você será contactado ou poderá solicitar a microchipagem 72h após assinatura do contrato.',
        'Caso o animal venha a óbito ou haja desistência do plano não haverá restituição da mensalidade paga(em caso de planos com recorrência mensal) e nem dos valores já pagos para planos anuais (caso seja beneficiado com algum desconto);',
        'Fidelidade: o contrato possui fidelidade de 12 meses, podendo ser cancelado sem multa rescisória caso não tenha utilizado qualquer procedimento;',
        'O contrato será automaticamente renovado por tempo indeterminado, caso nenhuma das partes faça a comunicação de cancelamento formal nos 30 (trinta) dias que antecedem seu término inicial.',
        'Caso ocorra rescisão contratual antes do decurso da fidelidade, já tendo utilizado o/a CONTRATANTE qualquer procedimento em benefício de seu animal, arcará com multa no valor das mensalidades vincendas acrescidas de 50% (cinquenta por cento).',
    ];

    public static $checklistPropostaPlanoFree = [
        'O Plano FREE não tem cobertura para atendimentos de urgência e emergência;',
        'Coberturas e Carências constam na Área do Cliente que está disponível no site: www.lifepet.com.br e no aplicativo da Lifepet;',
        'Doenças e males preexistentes terão cobertura após 12 meses de contrato ininterrupto (Cobertura Parcial Temporária - CPT);',
        'No caso de atraso de pagamento, o serviço é automaticamente SUSPENSO;',
        'Os pagamentos realizados em boleto ou cartão podem demorar até 72 horas para serem reconhecidos pelo banco;',
        'No caso de atraso superior a 60 dias, o plano é automaticamente CANCELADO, permanecendo as cobranças de mensalidades vencidas;',
        'Após 60 dias de inadimplência o débito em aberto poderá ser inscrito no SPC e SERASA (desde que previamente comunicado);',
        'REDE CREDENCIADA: www.lifepet.com.br/rede e aplicativo Lifepet;',
        'No primeiro acesso ao aplicativo será possível gerar login e senha;',
        'A vigência do contrato se inicia conforme data da assiantura dessa proposta.',
        'Caso o animal venha a óbito ou haja desistência do plano não haverá restituição da mensalidade paga(em caso de planos com recorrência mensal) e nem dos valores já pagos para planos anuais (caso seja beneficiado com algum desconto);',
        'Fidelidade: o contrato possui fidelidade de 12 meses, podendo ser cancelado sem multa rescisória caso não tenha utilizado qualquer procedimento;',
        'O contrato será automaticamente renovado por tempo indeterminado, caso nenhuma das partes faça a comunicação de cancelamento formal nos 30 (trinta) dias que antecedem seu término inicial.',
        'Caso ocorra rescisão contratual antes do decurso da fidelidade, já tendo utilizado o/a CONTRATANTE qualquer procedimento em benefício de seu animal, arcará com multa no valor das mensalidades vincendas acrescidas de 50% (cinquenta por cento).',
    ];

    public static $checklistDoencasProposta = [
        'Sofre(u) de alguma doença infecciosa ou parasitária: erlichia ou anaplasma (doença do carrapato), hepatite, meningite, infecções virais, entre outros?',
        'Sofre(u) de alguma tipo de neoplasia (câncer)?',
        'Sofre(u) de alguma doença no sangue (anemias)?',
        'É portador(a) de alguma doença endócrina (diabetes, hiperadrenocorticismo, hipotireoidismo, entre outras)?',
        'Sofre(u) de alguma doença do sistema nervoso (convulsões, ataxias, entre outras)?',
        'Alguma afecção dermatológica? (atopia, DAPE, Sarna)?',
        'É portador de alguma enfermidade circulatória(sopro, arritmia, hipertensão)?',
        'Sofre(u) algum problema em ouvido?',
        'Sofre(u) alguma afecção do aparelho respiratório (colapso de traqueia, bronquite, pneumonia, estenose de narinas, (palato alongado)?',
        'Sofre(u) de doenças do aparelho digestivo (gastrite, úlceras, diarreias, corpo estranho)?',
        'Sofre(u) de doença do aparelho genito-urinário (piometras, hiperplasia prostática, mastites, hematúria, obstruções, cistite, cálculo, fimose, insuficiência renal)?',
        'Sofre(u) algum tipo de fratura ou traumatismo?',
        'Realizou algum procedimento cirúrgico para correção ortopédica (fratura ou traumatismo)?',
        'Realizou algum tipo de procedimento cirúrgico?',
        'Sofre de alguma má formação congênita?',
        'Sofre(u) algum tipo de doença não relacionada acima?'
    ];

    /**
     * @param $message
     * @param array $data
     */
    protected static function log($message, $data = []) {
        $logger = new Logger();
        $logger->log(
            self::getLogData($data, 'event') ?: LogEvent::NOTICE,
            self::getLogData($data, 'area') ?: 'geral',
            self::getLogData($data, 'priority') ?: LogPriority::MEDIUM,
             $message,
            self::getLogData($data, 'executor') ?: null,
            self::getLogData($data, 'related') ?: null,
            self::getLogData($data, 'related_id') ?: null
            );
    }

    protected static function fillLogData(&$data, $args) {
        foreach($args as $k => $v) {
            if($v) {
                $data[$k] = $v;
            }
        }
    }

    protected static function getLogData($data, $index)
    {
        if(isset($data[$index])) {
            return $data[$index];
        }

        return null;
    }

    protected static function notice($message, $area = null, $related = null, $related_id = null, $executor = null) {
        $data = [];
        static::fillLogData($data, [
           'area' => $area,
           'related' => $related,
           'related_id' => $related_id,
           'executor' => $executor
        ]);

        static::log($message, $data);
    }

    protected static function warning($message, $area = null, $related = null, $related_id = null, $executor = null) {
        $data = [];
        static::fillLogData($data, [
            'event' => LogEvent::WARNING,
            'priority' => LogPriority::MEDIUM,
            'area' => $area,
            'related' => $related,
            'related_id' => $related_id,
            'executor' => $executor
        ]);

        static::log($message, $data);
    }

    protected static function error($message, $area = null, $related = null, $related_id = null) {
        $data = [];
        static::fillLogData($data, [
            'event' => LogEvent::ERROR,
            'priority' => LogPriority::HIGH,
            'area' => $area,
            'related' => $related,
            'related_id' => $related_id
        ]);

        static::log($message, $data);
    }

    protected static function creation($message, $area = null, $related = null, $related_id = null, $executor = null) {
        $data = [];
        static::fillLogData($data, [
            'event' => LogEvent::CREATE,
            'priority' => LogPriority::HIGH,
            'area' => $area,
            'related' => $related,
            'related_id' => $related_id,
            'executor' => $executor
        ]);

        static::log($message, $data);
    }

    protected static function alter($message, $area = null, $related = null, $related_id = null, $executor = null) {
        $data = [];
        static::fillLogData($data, [
            'event' => LogEvent::UPDATE,
            'priority' => LogPriority::HIGH,
            'area' => $area,
            'related' => $related,
            'related_id' => $related_id,
            'executor' => $executor
        ]);

        static::log($message, $data);
    }

    protected static function exclusion($message, $area, $related, $related_id, $executor) {
        $data = [];
        static::fillLogData($data, [
            'event' => LogEvent::DELETE,
            'priority' => LogPriority::HIGH,
            'area' => $area,
            'related' => $related,
            'related_id' => $related_id,
            'executor' => $executor
        ]);

        static::log($message, $data);
    }

    /**
     * @param $request
     * @param $model
     */
    protected static function getChanges($request, Model\Model $model)
    {
        $originalAtrributes = $model->getAttributes();
        $changedAttributes = $model->fill($request->all())->getMutatedAttributes();
        $changes = [];
        $changes['old'] = array_diff($originalAtrributes, $changedAttributes);
        $changes['new'] = array_diff($changedAttributes, $originalAtrributes);

        $changelog = self::formatModelChanges($changes);

        return $changelog;
    }

    public static function formatModelChanges($changes = [])
    {
        $message = "";
        if(empty($changes['old'])) {
            return '';
        }

        $jsonChanges = [];

        foreach($changes['old'] as $key => $old) {
            $new = ' - ';
            if(isset($changes['new'][$key])) {
                $new = $changes['new'][$key];
            }
            //$message .= "'{$key}': {$old} -> {$new}\n";

            $jsonChanges[$key] = "{$old} -> {$new}";
        }

        return json_encode($jsonChanges);
    }
}
