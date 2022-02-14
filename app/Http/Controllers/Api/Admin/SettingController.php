<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Managers\SettingManager;
use App\Models\Setting;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingController
{
    /** @var SettingManager */
    private $settingManager;

    public function __construct(SettingManager $settingManager)
    {
        $this->settingManager = $settingManager;
    }

    public function index(): Response
    {
        return response()->success(Setting::all());
    }

    public function show(Setting $setting): Response
    {
        return response()->success($setting);
    }

    public function update(Request $request, Setting $setting): Response
    {
        $setting = $this->settingManager->update($setting, $request->value);
        return response()->success($setting);
    }
}
