<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Managers\FaqManager;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /** @var FaqManager  */
    private $faqManager;

    public function __construct(FaqManager $faqManager)
    {
        $this->faqManager = $faqManager;
    }

    public function index()
    {
        return $this->faqManager->getList();
    }

    public function show(int $id)
    {
        return $this->faqManager->getItem($id);
    }

    public function updateOrCreate(Request $request)
    {
        return $this->faqManager->updateOrCreate($request->all());
    }

    public function delete(int $id)
    {
        return $this->faqManager->delete($id);
    }
    public function uploadFile(Request $request) {
        $result = "";
        if ($request->has('file')) {
            $result = $this->faqManager->uploadFaqFile($request->file('file'));
        }
        return response()->success($result);
    }
}
