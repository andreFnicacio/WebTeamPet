<?php

namespace Modules\Mobile\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Push
 * @package App\Models
 * @property $title
 * @property $message
 * @property $count
 * @property $status
 * @property $progress
 * @property $meta
 * @property $author
 * @property $checksum
 */
class Push extends Model
{

    public $table = 'pushes';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const STATUS_INICIADO = 'INICIADO';
    const STATUS_CONCLUIDO = 'CONCLUIDO';
    const STATUS_ABERTO = 'ABERTO';


    public $fillable = [
        'title',
        'message',
        'count',
        'status',
        'progress',
        'meta',
        'author'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'count' => 'integer',
        'status' => 'string',
        'progress' => 'integer',
        'meta' => 'string',
        'author' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'author', 'id');
    }

    public function start()
    {
        if($this->status !== self::STATUS_ABERTO) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_INICIADO
        ]);

        return true;
    }

    public function iterate($progress)
    {
        $this->update([
            'progress' => $progress
        ]);
    }

    public function end()
    {
        $this->update([
            'status' => self::STATUS_CONCLUIDO
        ]);
    }

    public function checksum()
    {
        $base = join(':', [
            $this->title,
            $this->message,
            $this->count,
            $this->meta
        ]);

        $this->checksum = md5($base);

        return !self::where('checksum', $this->checksum)->exists();
    }
}
