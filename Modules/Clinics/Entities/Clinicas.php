<?php

namespace Modules\Clinics\Entities;

use App\Models\Grupos;
use App\Models\Procedimentos;
use Illuminate\Database\Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Guides\Entities\HistoricoUso;

/**
 * Class Clinicas
 * @package App\Models
 * @property $id
 * @property $tipo_pessoa
 * @property $nome_clinica
 * @property $contato_principal
 * @property $email_contato
 * @property $cidade
 * @property $estado
 * @property $tipo
 * @property $ativo
 * @property
 */
class Clinicas extends Model\Model
{
    use SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const PAGINATION_SIZE = 20;

    protected $dates = ['deleted_at'];
    public $table = 'clinicas';

    public $fillable = [
        'tipo_pessoa',
        'cpf_cnpj',
        'nome_clinica',
        'contato_principal',
        'email_contato',
        'cep',
        'rua',
        'numero_endereco',
        'bairro',
        'cidade',
        'estado',
        'telefone_fixo',
        'celular',
        'email_secundario',
        'banco',
        'agencia',
        'numero_conta',
        'crmv',
        'tipo',
        'id_usuario',
        'id_tabela',
        'selecionavel',
        'ativo',
        'exibir_site',
        'nome_site',
        'email_site',
        'telefone_site',
        'celular_site',
        'lat',
        'lng',
        'id_urh',
        'aceite_urh'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tipo_pessoa' => 'string',
        'cpf_cnpj' => 'string',
        'nome_clinica' => 'string',
        'contato_principal' => 'string',
        'email_contato' => 'string',
        'cep' => 'string',
        'rua' => 'string',
        'numero_endereco' => 'string',
        'bairro' => 'string',
        'cidade' => 'string',
        'estado' => 'string',
        'telefone_fixo' => 'string',
        'celular' => 'string',
        'email_secundario' => 'string',
        'banco' => 'string',
        'agencia' => 'string',
        'numero_conta' => 'string',
        'crmv' => 'string',
        'tipo' => 'string',
        'id_usuario' => 'integer',
        'id_tabela' => 'integer',
        'ativo' => 'integer',
        'exibir_site' => 'integer',
        'nome_site' => 'string',
        'email_site' => 'string',
        'telefone_site' => 'string',
        'celular_site' => 'string',
        'lat' => 'string',
        'lng' => 'string',
        'id_urh' => 'integer',
        'aceite_urh' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * Validation Create rules
     *
     * @var array
     */
    public static $createRules = [
      //  'cpf_cnpj' => ['unique:clinicas,cpf_cnpj']
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function tabelaReferencia()
    {
        return $this->belongsTo(\App\Models\TabelasReferencia::class, 'id_tabela');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'id_usuario');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function historicoUsos()
    {
        return $this->hasMany(\Modules\Guides\Entities\HistoricoUso::class, 'id_clinica');
    }

    /**
     * @return Model\Relations\BelongsToMany
     */
    public function prestadores()
    {
        return $this->belongsToMany(
            \Modules\Veterinaries\Entities\Prestadores::class,
            'clinicas_prestadores',
            'id_clinica',
            'id_prestador'
        )->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function categorias()
    {
        return $this->belongsToMany(\App\Models\Categorias::class, 'clinicas_categorias', 'id_clinica', 'id_categoria');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function movimentacoes()
    {
        return $this->hasMany(\App\Models\MovimentacoesCredenciados::class, 'id_clinica');
    }

    public function planos()
    {
        return \App\Models\PlanosCredenciados::where('id_clinica', $this->id)
            ->join('planos', 'planos.id', '=', 'planos_credenciados.id_plano')
            ->where('habilitado', 1)
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function urh()
    {
        return $this->belongsTo(\App\Models\Urh::class, 'id_urh');
    }

    public function avatar()
    {
        return $this->foto ? route('clinicas.avatar', $this->id) : 'https://ui-avatars.com/api/?background=1980d5&color=fff&size=256&name='.$this->nome_clinica;
    }

    public function getNomeTabelaAttribute() {
        $tabela = $this->tabelaReferencia()->first();
        if($tabela) {
            $tabela = str_replace('Tabela', '', $tabela->nome);
            return $tabela;
        }

        return "";
    }

    public function getTelefonesAttribute() {
        $telefones = [];
        if(!empty(trim($this->telefone_fixo))) {
            $telefones[] =  $this->telefone_fixo;
        }
        if(!empty(trim($this->celular))) {
            $telefones[] =  $this->celular;
        }

        return join(" / ", $telefones);
    }

    public function getEnderecoCompletoAttribute() {
        return trim(trim(trim($this->rua ? $this->rua : '') .
            ($this->numero_endereco ? ', ' . $this->numero_endereco : '') .
            ($this->bairro ? ' - ' . $this->bairro : '') .
            ($this->cidade ? ', ' . $this->cidade : '') .
            ($this->estado ? ' - ' . $this->estado : '') .
            ($this->cep ? ', ' . $this->cep : '')));
    }

    public function checkPlanoCredenciado($id_plano)
    {
        $pc = \App\Models\PlanosCredenciados::where('id_clinica', $this->id)
            ->where('id_plano', $id_plano)
            ->get()
            ->first();
        if ($pc && $pc->habilitado) {
            return true;
        }
        return false;
    }

    public function checkCategoriaGuia($numero_guia)
    {
        $categorias_grupos = $this->categorias->map(function ($categoria) {
            return $categoria->grupos->pluck('id');
        });

        $clinica_grupos = [];
        foreach ($categorias_grupos as $cat_grupo) {
            foreach ($cat_grupo as $id_grupo) {
                $clinica_grupos[] = $id_grupo;
            }
        }

        $guia_grupos = (new \Modules\Guides\Entities\HistoricoUso())->where('numero_guia', $numero_guia)->get()->map(function ($guia) {
            return $guia->procedimento->grupo->id;
        })->toArray();

        foreach ($guia_grupos as $id_guia_grupo) {
            if (in_array($id_guia_grupo, $clinica_grupos)) {
                return true;
            }
        }
        return false;
    }

    public function getRedeCredenciadaMap()
    {
        $redeCredenciada = (new Clinicas())->where('ativo', 1)
            ->where('exibir_site', 1)
            ->whereNotNull('nome_site')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->with('tagsSelecionadas')
            ->get();

        return $this->mapRedeCredenciada($redeCredenciada);
    }

    public function mapRedeCredenciada(Model\Collection $redeCredenciada)
    {
        return $redeCredenciada->map(function(Clinicas $clinica) {
            $publicClinica = new \stdClass();
            $publicClinica->id = $clinica->id;
            $publicClinica->nome = $clinica->nome_site;
            $publicClinica->tipo = $clinica->tipo;
            $publicClinica->cidade = ucwords(mb_strtolower($clinica->cidade));
            $publicClinica->estado = ucwords(mb_strtolower($clinica->estado));

            $publicClinica->endereco = ($clinica->rua ? $clinica->rua : '') .
                ($clinica->numero_endereco ? ', ' . $clinica->numero_endereco : '') .
                ($clinica->bairro ? ' - ' . $clinica->bairro : '') .
                ($clinica->cidade ? ', ' . $clinica->cidade : '') .
                ($clinica->estado ? ' - ' . $clinica->estado : '') .
                ($clinica->cep ? ', ' . $clinica->cep : '');
            $publicClinica->endereco = trim(trim(trim($publicClinica->endereco, ','), '-'));

            $publicClinica->rua = $clinica->rua;
            $publicClinica->numero_endereco = $clinica->numero_endereco;
            $publicClinica->cep = $clinica->cep;
            $publicClinica->bairro = $clinica->bairro;

            $publicClinica->lat = $clinica->lat;
            $publicClinica->lng = $clinica->lng;

            $publicClinica->email_contato = $clinica->email_site ? $clinica->email_site : '';
            $publicClinica->telefone_fixo = $clinica->telefone_site ? $clinica->telefone_site : '';
            $publicClinica->celular = $clinica->celular_site ? $clinica->celular_site : '';

            return $publicClinica;
        });
    }

    /**
     * Obtém o valor do procedimento de tabela específica na qual a clínica está vinculada
     * @param Procedimentos $procedimento
     * @return int
     */
    public function getValorProcedimento(Procedimentos $procedimento)
    {
        /*
            Ordem de prioridade no cálculo do valor do procedimento:
            1º- Valor do procedimento na TABELA ESPECÍFICA (vinculada à clínica), se não
            2º- Valor do procedimento no PLANO, se não
            3º- Valor do procedimento na TABELA BASE
        */

        $valorMomento = 0;
        return $this->tabelaReferencia->valorProcedimento($procedimento);
    }

    /**
     * Obtém a lista de valores que o credenciados irão receber na competência selecionada
     * Valores = Sinistralidade + Gamification (Faixa e Tempo de Formação)
     * @param $competencia
     * @param $limite
     * @return array
     */
    public function getExtratoMensal($start = null, $end = null, $limite = null) {
        $start = $start ?: (new \Carbon\Carbon())->today()->startOfMonth();
        $end = $end ?: (new \Carbon\Carbon())->today()->endOfMonth();

        $guias = $this->historicoUsos()
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
            ->orderBy('id', 'desc');
        $extrato = $guias->get()->map(function ($guia) {
            return (object) [
                'numero_guia' => $guia->numero_guia,
                'plano' => $guia->plano->nome_plano,
                'descricao' => $guia->procedimento->nome_procedimento,
                'urh' => round($guia->valor_momento),
                'data' => $guia->dataGuia()->format('d/m/Y'),
                'prestador' => $guia->prestador ? $guia->prestador->nome : '-'
            ];
        });

        $extrato = $extrato->sortBy('numero_guia', SORT_REGULAR, true);

        if ($limite) {
            $guias = $guias->limit($limite);
        }

        return $extrato;
    }

    public function guiasPendentesAssinatura()
    {
        $guias = (new \Modules\Guides\Entities\HistoricoUso)::where('id_clinica', $this->id)
            ->whereNull('assinatura_prestador')
            ->whereIn('glosado', ['0','2'])
            ->whereNotNull('id_prestador')
            ->whereNull('meio_assinatura_cliente')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('tipo_atendimento', '!=', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO);
                    $query->where('status', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO);
                });
                $query->orWhere(function ($query) {
                    $query->where('tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO);
                    $query->whereNotNull('realizado_em');
                });
            })
            ->groupBy('numero_guia')
            ->orderByRaw('IFNULL(realizado_em, created_at)')
            ->get();
        return $guias;
    }

    public function tagsSelecionadas() {
        return $this->hasMany(\Modules\Clinics\Entities\ClinicaAtendimentoTagSelecionada::class, 'clinica_id');
    }

    public function limites() {
        return $this->belongsToMany(Grupos::class, 'clinicas_grupos_limites', 'id_clinica', 'id_grupo')
            ->withPivot('limite')
            ->using(ClinicasGruposLimite::class);
    }

    public function limitePorGrupo(Grupos $grupo) {
        $limite = $this->limites()->wherePivot('id_grupo', $grupo->id)->first();
        if(!$limite) {
            return null;
        }

        return $limite->pivot->limite;
    }

    public function atualizarLimite(Grupos $grupo, $limite = null) {
        if(is_null($limite)) {
            $this->limites()->detach($grupo->id);
            return null;
        }

        if($this->limites()->wherePivot('id_grupo', $grupo->id)->exists()) {
            $this->limites()->updateExistingPivot($grupo->id, ['limite' => $limite]);
        } else {
            $this->limites()->save($grupo, ['limite' => $limite]);
        }

        return $limite;
    }

    public static function getClinicsByTerms($searchTerm = null, $active = null)
    {
        $query = self::query();

        if ($searchTerm) {
            $query->where('nome_clinica', 'LIKE', '%' . trim($searchTerm) . '%');
        }

        if (!is_null($active)) {
            $query->where('ativo', $active);
        }

        $query->orderBy(self::CREATED_AT, 'DESC');

        return $query;
    }
}
