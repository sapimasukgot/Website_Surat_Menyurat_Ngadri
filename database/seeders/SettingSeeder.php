<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
<<<<<<< Updated upstream
<<<<<<< Updated upstream
            'kepala_desa' => 'Sugeng Riyadi',
            'sekretaris_desa' => 'Administrator Desa',
=======
            'kepala_desa' => 'NURYASIN',
            'sekretaris_desa' => 'Sekretaris Desa',
>>>>>>> Stashed changes
=======
            'kepala_desa' => 'NURYASIN',
            'sekretaris_desa' => 'Sekretaris Desa',
>>>>>>> Stashed changes
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
