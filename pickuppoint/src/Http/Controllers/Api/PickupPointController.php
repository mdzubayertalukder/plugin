<?php

namespace Plugin\PickupPoint\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plugin\PickupPoint\Resources\PickupPointCollection;
use Plugin\PickupPoint\Http\Repositories\PickupPointRepository;

class PickupPointController extends Controller
{
    protected $repository;
    public function __construct(PickupPointRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Will get active pickup points
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activePickupPoints(Request $request)
    {
        return new PickupPointCollection($this->repository->getActivePickupPoint($request));
    }
}
