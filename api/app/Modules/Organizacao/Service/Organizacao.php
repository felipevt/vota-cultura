<?php

namespace App\Modules\Organizacao\Service;

use App\Core\Service\AbstractService;
use App\Modules\Localidade\Service\Endereco;
use App\Modules\Organizacao\Mail\Organizacao\CadastroComSucesso;
use App\Modules\Organizacao\Model\Organizacao as OrganizacaoModel;
use App\Modules\Representacao\Service\Representante;
use App\Modules\Representacao\Model\Representante as RepresentanteModel;
use App\Modules\Upload\Model\Arquivo;
use App\Modules\Upload\Service\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Organizacao extends AbstractService
{
    public function __construct(OrganizacaoModel $model)
    {
        parent::__construct($model);
    }

    public function cadastrar(array $dados): ?Model
    {
        try {

            DB::beginTransaction();
            $organizacao = $this->getModel()->where([
                'ds_email' => $dados['ds_email']
            ])->orWhere([
                'no_organizacao' => $dados['no_organizacao']
            ])->orWhere([
                'nu_cnpj' => $dados['nu_cnpj']
            ])->first();

            if ($organizacao) {
                throw new \Exception(
                    'Organizacao já cadastrada.',
                    Response::HTTP_NOT_ACCEPTABLE
                );
            }

            $serviceRepresentante = app()->make(Representante::class);
            $representante = $serviceRepresentante->cadastrar($dados['representante']);

            if (!$representante) {
                throw new \Exception('Não foi possível cadastrar o representante.');
            }
            $dados['co_representante'] = $representante->co_representante;

            $serviceEndereco = app()->make(Endereco::class);
            $endereco = $serviceEndereco->cadastrar($dados['endereco']);

            if (!$endereco) {
                throw new \Exception('Não foi possível cadastrar o endereço.');
            }

            $dados['co_endereco'] = $endereco->co_endereco;
            $organizacao = parent::cadastrar($dados);

            foreach (array_values($dados['criterios']) as $criterioId) {
                $organizacao->criterios()->attach($criterioId);
            }

            Mail::to($representante->ds_email)
                ->bcc(env('EMAIL_ACOMPANHAMENTO'))
                ->send(
                    new CadastroComSucesso($organizacao)
                );

            DB::commit();
            return $organizacao;
        } catch (\Exception $queryException) {
            DB::rollBack();
            throw $queryException;
        }
    }

}
