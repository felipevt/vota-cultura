<?php

namespace App\Modules\Conselho\Model;

use App\Modules\Core\Helper\Telefone as TelefoneHelper;
use Illuminate\Database\Eloquent\Model;

class Conselho extends Model
{
    protected $table = 'tb_conselho';
    protected $primaryKey = 'co_conselho';

    protected $fillable = [
        'no_orgao_gestor',
        'no_conselho',
        'ds_email',
        'nu_telefone',
        'nu_cnpj',
        'tp_governamental',
        'co_endereco',
        'co_representante',
        'co_usuario',
        'ds_sitio_eletronico',
        'st_inscricao',
    ];

    public $timestamps = false;

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

    public function usuario()
    {
        return $this->hasOne(\App\Modules\Conta\Model\Usuario::class,
            'co_usuario',
            'co_usuario'
        );
    }

    public function getTelefoneFormatadoAttribute()
    {
        return TelefoneHelper::adicionarMascara($this->nu_telefone);
    }
}
