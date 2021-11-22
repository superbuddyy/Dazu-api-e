<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Managers\RecentSearchManager;
use App\Models\RecentSearch;
use App\Http\Resources\RecentSearch\RecentSearchResource;
use App\Http\Resources\RecentSearch\RecentSearchCollection;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class RecentSearchController extends Controller
{
    protected $recentSearchManager;

    public function __construct(
        RecentSearchManager $recentSearchManager
    ) {
        $this->recentSearchManager = $recentSearchManager;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $search = $this->recentSearchManager->getList();
        return response()->success(new RecentSearchCollection($search));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, RecentSearch $search): Response
    {
        // print_r($search);
        // echo $search->display_name;
        echo $request->display_name;
        $result = $this->recentSearchManager->store($request->display_name,$request->lat,$request->lon);
        $count = $this->recentSearchManager->getCount();
        echo $count;
        if ($count > 5) {
            $tmpCount = $count - 5;
            $this->recentSearchManager->delete(true,$tmpCount);
        }
        // print_r($result);
        // return response()->success(new RecentSearchResource($result), Response::HTTP_OK);
        return response()->success('', Response::HTTP_OK);
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
    public function destroy($id)
    {
        //
    }
    /**
     * 
     * clear all recent search
     */
    public function delete(Request $request): Response
    {
        $delted = $this->recentSearchManager->delete(false);
        return response()->success('', Response::HTTP_OK);
    }
}
