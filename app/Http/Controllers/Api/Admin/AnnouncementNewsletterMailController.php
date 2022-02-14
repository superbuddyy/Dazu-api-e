<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnnouncementNewsletterMails;
use Illuminate\Http\Request;
use App\Jobs\AnnouncementNewsletterMailJob;
use Symfony\Component\HttpFoundation\Response;

class AnnouncementNewsletterMailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = AnnouncementNewsletterMails::query()->paginate(config('dazu.pagination.per_page'));

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
        $result = AnnouncementNewsletterMails::create(['title' => $request->title, 'content' => $request->get('content')]);
        dispatch(new AnnouncementNewsletterMailJob($result));

        return response()->success($result, Response::HTTP_CREATED);
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
}
