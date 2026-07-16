<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePejabatRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{
    public function updatePejabat(UpdatePejabatRequest $request): RedirectResponse
    {
        Setting::set('kepala_desa', $request->input('kepala_desa'));
        Setting::set('sekretaris_desa', $request->input('sekretaris_desa'));

        return back()->with('success', 'Nama perangkat desa berhasil diperbarui.');
    }
}
