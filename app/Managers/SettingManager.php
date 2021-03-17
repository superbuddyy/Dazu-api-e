<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\Setting;

class SettingManager
{
    public function update(Setting $setting, string $value): Setting
    {
        if ($setting->category === 'pricing') {
            $value = $value * 100;
        }

        $setting->update([
            'value' => $value,
        ]);

        return $setting->refresh();
    }
}
