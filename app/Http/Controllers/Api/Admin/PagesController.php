<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pages;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = Pages::query()->paginate(config('dazu.pagination.per_page'));

        return response()->success($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $checkExist = Pages::where('page_key', $request->get('page_key'))->count();
        if ($checkExist) {
            $err = [
                "error" => true,
                "msg" => 'Page Routing Key already exist'
            ];
            return response()->success($err);
        }
        $result = Pages::create(['page_key' => $request->get('page_key'), 'name' => $request->get('name'), 'content' => $request->get('content')]);
        return response()->success($result, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): Response
    {
        return response()->success(Pages::findorFail($id)->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pages $page): Response
    {
        $checkExist = Pages::where('page_key', $request->get('page_key'))->where('id', '!=', $request->id)->count();
        if ($checkExist) {
            $err = [
                "error" => true,
                "msg" => 'Page Routing Key already exist'
            ];
            return response()->success($err);
        }
        $update = Pages::findorFail($request->id)->update([
            'page_key' => $request->get('page_key'),
            'name' => $request->get('name'),
            'content' => $request->get('content')
        ]);
        return response()->success($update, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Pages $page): Response
    {        
        return response()->success(Pages::findorFail($request->id)->delete());
    }
}
