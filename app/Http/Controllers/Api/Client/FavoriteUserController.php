<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\User\UserCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Managers\FavoriteUserManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class FavoriteUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $favoriteManager;

    public function __construct(FavoriteUserManager $favoriteManager)
    {
        $this->favoriteManager = $favoriteManager;
    }

    public function index(Request $request): JsonResponse
    {
        $role = $request->type ?? '';
        $favoriteOffers = $this->favoriteManager->getList($role);
        return response()->success(new UserCollection($favoriteOffers, true));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user): JsonResponse
    {
        $status = false;
        if ($this->favoriteManager->getItem($user->id)) {
            $this->favoriteManager->delete($user);
            $status = false;
        } else {
            $result = $this->favoriteManager->store($user);
            $status = true;
        }
        $response = [
            "status" => $status,
        ];
        return response()->success($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user): Response
    {
        if (!$this->favoriteManager->delete($user)) {
            return response()->error('fail_to_delete', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->noContent();
    }
}
