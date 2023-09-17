<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\AppBaseController;
use App\Models\PlanosCredenciados;
use Illuminate\Http\Request;
use Modules\Clinics\Entities\Clinicas;
use App\Http\Requests\API\CheckUserAPIRequest;
use App\Http\Requests\API\CreateUserAPIRequest;
use App\Http\Requests\API\LoginAPIRequest;
use App\Models\Clientes;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Utils\ResponseUtil;

class SiteAPIController extends AppBaseController
{
    const SITE_DEFAULT_MAP_ID = 1;

    public function index(Request $request)
    {
        $clinics = Clinicas::where('ativo', 1)
            ->where('exibir_site', 1)
            ->whereNotNull('nome_site')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get()->map(function(Clinicas $clinic) use ($request) {
                return $this->createMapNode($clinic, $request);
            })->toArray();

        return [
                'maps' => [
                    [
                        'id' => ($request['map_id'] ?: self::SITE_DEFAULT_MAP_ID),
                        'map_title' => "Rede Credenciada"
                    ]
                ],
                'markers' => array_values(array_filter($clinics))
        ];
    }

    private function createMapNode(Clinicas $clinic, Request $request)
    {
        $mapNode = new \stdClass();
        $mapNode->map_id = ($request['map_id'] ?: self::SITE_DEFAULT_MAP_ID);
        $mapNode->title = $clinic->nome_site;

        $addressNode = new \stdClass();
        $addressNode->address = ($clinic->rua ?: '') .
            ($clinic->numero_endereco ? ', ' . $clinic->numero_endereco : '') .
            ($clinic->bairro ? ' - ' . $clinic->bairro : '') .
            ($clinic->cidade ? ', ' . $clinic->cidade : '') .
            ($clinic->estado ? ' - ' . $clinic->estado : '') .
            ($clinic->cep ? ', ' . $clinic->cep : '');
        $mapNode->address = trim(trim(trim($addressNode->address, ','), '-'));

        $mapNode->lat = $clinic->lat;
        $mapNode->lng = $clinic->lng;

        $mapNode->other_data = "a:2:{s:10:\"hover_icon\";s:80:\"\/\/lifepet.com.br\/wp-content\/plugins\/wp-google-maps\/images\/spotlight-poi2.png\";s:12:\"hover_retina\";s:1:\"0\";}";

        $mapNode->description = $this->buildDescription($clinic);
        $mapNode->approved = 1;
        $mapNode->anim = 0;

        return $mapNode;
    }

    private function getPlans($clinic)
    {
        $plans = (new PlanosCredenciados())
            ->where('id_clinica', $clinic->id)
            ->whereIn('id_plano', [74, 75, 76, 79])
            ->where('habilitado', 1)
            ->with('plano')
            ->get()
            ->map(function ($planoCredenciado) {
                $plano = $planoCredenciado->plano;
                if ($plano) {
                    return $plano->display_nome ?: $plano->nome_plano;
                }
            })->toArray();
        $plans = array_filter($plans);
        if (!$plans) {
            return null;
        }

        return $plans;
    }

    private function buildDescription(Clinicas $clinic)
    {
        $tags = $clinic->tagsSelecionadas()->with('tag')->get()->pluck('tag.nome')->toArray();
        $plans = $this->getPlans($clinic);

        $description = "";

        if ($tags) {
            $description = "<b style='color:#6a67e8'>Especialidades</b><br>";
            foreach ($tags as $tag) {
                $description .= $tag . "<br>";
            }
            $description .= "<br>";
        }

        if ($plans) {
            $description .= "<b style='color:#6a67e8'>Planos</b><br>";
            foreach ($plans as $plan) {
                $description .= $plan . "<br>";
            }
            $description .= "<br>";
        }

        if ($clinic->telefone_site) {
            $description .= "Telefone: " . Utils::formataTelefone($clinic->telefone_site);
            $description .= "<br>";
        }

        if ($clinic->celular_site) {
            $description .= "Celular: " . Utils::formataTelefone($clinic->celular_site);
            $description .= "<br><br>";

            $wpNumber = Utils::numberOnly($clinic->celular_site);
            $description .= '<a href="https://api.whatsapp.com/send?phone=55' . $wpNumber . '&text=Ol%C3%A1.%20Gostaria%20de%20mais%20informa%C3%A7%C3%B5es." target="_blank" title="Entre em contato" class="link-apiwhats"><i class="fab fa-whatsapp"></i>Entre em contato</a>';
            $description .= "<br>";
        }

        return $description;
    }

    public function login(LoginAPIRequest $request)
    {
        if (Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')])) {

            $user = Auth::user();
            $client = (new Clientes())->where('id_usuario', $user->id)->first();

            if (!$client) {
                return Response::json(ResponseUtil::makeError(__("Cliente não encontrado!")), 404);
            }

            $token = $user->createToken("Lifepet Site Cliente", ['*']);

            return Response::json(ResponseUtil::makeResponse("", [
                'expires_in' => Carbon::now()->diffInSeconds(Carbon::now()->addDays(30)),
                'access_token' => $token
            ]), 200);

        } else {
            return Response::json(ResponseUtil::makeError(__("Email ou senha inválida!")), 401);
        }
    }

    public function register(CreateUserAPIRequest $request)
    {
        $email = $request->get('email');
        $name = $request->get('name');
        $password = $request->get('password');

        $user = (new User())->where('email', $email)->first();

        if ($user) {
            return Response::json(ResponseUtil::makeResponse(__("Usuário já existe"), $user), 301);
        }

        $user = (new User())->create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password)
        ]);

        return Response::json(ResponseUtil::makeResponse("Created", $user), 201);
    }
    public function checkUserExist(CheckUserAPIRequest $request)
    {
        $email = $request->get('email');

        $user = (new User())->where('email', $email)->first();

        if ($user) {
            return Response::json(ResponseUtil::makeResponse('', true), 200);
        }

        return Response::json(ResponseUtil::makeError(""), 404);
    }
}