<?php

namespace Modules\Veterinaries\Entities;

use Carbon\Carbon;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Guides\Entities\HistoricoUso;

class Prestadores extends Model
{
    use SoftDeletes;

    public $table = 'prestadores';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'tipo_pessoa',
        'cpf',
        'nome',
        'email',
        'telefone',
        'crmv',
        'crmv_uf',
        'especialista',
        'id_especialidade',
        'data_formacao',
        'ativo',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tipo_pessoa' => 'string',
        'cpf' => 'string',
        'nome' => 'string',
        'email' => 'string',
        'telefone' => 'string',
        'crmv' => 'string',
        'crmv_uf' => 'string',
        'especialista' => 'boolean',
        'id_especialidade' => 'integer',
        'data_formacao' => 'date',
        'ativo' => 'boolean',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'cpf' => 'unique:prestadores'
    ];

    public function setDataFormacaoAttribute($value) {
        $this->attributes['data_formacao'] = \DateTime::createFromFormat('d/m/Y', $value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function clinicas()
    {
        return $this->belongsToMany(
            \Modules\Clinics\Entities\Clinicas::class,
            'clinicas_prestadores',
            'id_prestador',
            'id_clinica'
        )->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function especialidade()
    {
        return $this->belongsTo(\App\Models\Especialidades::class, 'id_especialidade');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function historicoUsos()
    {
        return $this->hasMany(\Modules\Guides\Entities\HistoricoUso::class);
    }

    public function tempoFormacao() {
        return (new Carbon())->diffInYears($this->data_formacao);
    }

    public function getCRMV(){
        return $this->crmv ? $this->crmv . '-' . $this->crmv_uf : 'Sem CRMV';
    }

    public function avaliacoes($id_clinica = null) {
        $avaliacoes = (new AvaliacoesPrestadores())->where('id_prestador', $this->id);
        if ($id_clinica) {
            $avaliacoes = $avaliacoes->where('id_clinica', $id_clinica);
        }
        return $avaliacoes;
    }

    public function rating($id_clinica = null) {
        $avaliacoes = $this->avaliacoes($id_clinica);
        if ($avaliacoes->count() >= 10) {
            return $avaliacoes->avg('nota');
        } else {
            return 0;
        }
    }

    public function ratingBadge()
    {
        $rating = $this->rating();
        $tooltip = '';

        if ($rating == 0) {
            $class = 'default';
            $rating = '--';
            $tooltip = 'Prestador sem informações disponíveis';
        } else {
            if ($rating < 4) {
                $class = 'danger';
            } elseif ($rating >= 4 && $rating < 4.8) {
                $class = 'warning';
            } else {
                $class = 'success';
            }
            $rating = number_format($rating, 2, ',', '');
        }

        return '<span class="badge badge-'.$class.' btn-sm btn-circle" data-toggle="tooltip" data-title="'.$tooltip.'" style="margin-right:4px; margin-top:-2px;">
                    <i class="fa fa-star"></i> '.$rating.'
                </span>';
    }

    public function checkSenhaCrmv($senha){
        if ($senha == $this->crmv.$this->crmv_uf) {
            return true;
        }
        return false;
    }

    public function assinarFichaAvaliacao() {

    }

    public function assinarGuia($numero_guia, $senha_prestador) {
        if ($numero_guia) {

            $hu = HistoricoUso::where('numero_guia', $numero_guia)->first();
            $prestador = $this;

            if ($senha_prestador) {
                if ($hu) {
                    if ($prestador->checkSenhaCrmv($senha_prestador)) {
                        $guias = (new HistoricoUso())->where('numero_guia', $numero_guia)->get()->map(function ($guia) {
                            $guia->gerarAssinaturaPrestador();
                        });
                        $data = [
                            'status' => true,
                            'http' => 200,
                            'msg' => 'Guia(s) assinada(s) com sucesso!'
                        ];
                    } else {
                        $data = [
                            'status' => false,
                            'http' => 401,
                            'msg' => 'O CRMV não confere! A guia não foi assinada!'
                        ];
                    }
                } else {
                    $data = [
                        'status' => false,
                        'http' => 401,
                        'msg' => 'A guia não foi encontrada!'
                    ];
                }
            } else {
                $data = [
                    'status' => false,
                    'http' => 401,
                    'msg' => 'O CRMV é obrigatória!'
                ];
            }
        } else {
            $data = [
                'status' => false,
                'http' => 401,
                'msg' => 'O número da guia é obrigatório!'
            ];
        }
        return $data;
    }
}
