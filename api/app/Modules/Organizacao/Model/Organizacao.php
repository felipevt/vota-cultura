<?php

namespace App\Modules\Organizacao\Model;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Helper\Telefone as TelefoneHelper;

class Organizacao extends Model
{
    protected $table = 'tb_organizacao';
    protected $primaryKey = 'co_organizacao';

    protected $fillable = [
        'nu_cnpj',
        'no_organizacao',
        'ds_email',
        'nu_telefone',
        'co_segmento',
        'co_usuario',
        'co_endereco',
        'co_representante',
        'ds_sitio_eletronico',
        'st_inscricao',
    ];

    public $timestamps = false;

    public function criterios()
    {
        return $this->belongsToMany(
            \App\Modules\Organizacao\Model\Criterio::class,
            'rl_organizacao_criterio',
            'co_organizacao',
            'co_criterio'
        )->as('rl_organizacao_criterio');
    }

    public function segmento()
    {
        return $this->belongsTo(
            \App\Modules\Organizacao\Model\Segmento::class,
            'co_segmento',
            'co_segmento'
        );
    }

    public function usuario()
    {
        return $this->belongsTo(
            \App\Modules\Conta\Model\Usuario::class,
            'co_usuario',
            'co_usuario'
        );
    }

    public function endereco()
    {
        return $this->hasOne(
            \App\Modules\Localidade\Model\Endereco::class,
            'co_endereco',
            'co_endereco'
        );
    }

    public function representante()
    {
        return $this->hasOne(
            \App\Modules\Representacao\Model\Representante::class,
            'co_representante',
            'co_representante'
        );
    }

    public function getTelefoneFormatadoAttribute()
    {
        return TelefoneHelper::adicionarMascara($this->nu_telefone);
    }

}
