<?php

namespace App\Modules\Conselho\Http\Controllers;

use App\Modules\Conselho\Model\Conselho;
use App\Modules\Conselho\Service\Conselho as ConselhoService;
use App\Modules\Core\Http\Controllers\AApiResourceController;
use App\Modules\Core\Http\Controllers\Traits\TApiResourceDestroy;
use App\Modules\Core\Http\Controllers\Traits\TApiResourceUpdate;
use App\Modules\Organizacao\Model\Organizacao;

class ConselhoApiResourceController extends AApiResourceController
{
    use TApiResourceUpdate,
        TApiResourceDestroy;

    public function __construct(ConselhoService $service)
    {
        $this->service = $service;
    }

    public function show($identificador): \Illuminate\Http\JsonResponse
    {
        throw new \Exception("Método não disponível");
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        throw new \Exception("Método não disponível");
    }
}
