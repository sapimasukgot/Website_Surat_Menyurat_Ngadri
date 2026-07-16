<?php
namespace App\Http\Controllers;

use App\Models\JenisSurat;
use App\Models\Penduduk;
use App\Models\Setting;
use App\Models\Surat;
use Carbon\CarbonImmutable;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_penduduk' => Penduduk::count(),
            'total_surat' => Surat::count(),
            'surat_hari_ini' => Surat::whereDate('tanggal_surat', today())->count(),
            'total_jenis' => JenisSurat::where('is_active', true)->count(),
        ];

        return view('dashboard.index', [
            'stats' => $stats,
            'chartBulanan' => $this->suratPerBulan(),
            'chartJenis' => $this->jenisTerbanyak(),
            'aktivitas' => Surat::with(['jenisSurat', 'penduduk', 'user'])
                ->latest()->limit(8)->get(),
            'kepalaDesa' => Setting::get('kepala_desa', ''),
            'sekretarisDesa' => Setting::get('sekretaris_desa', ''),
<<<<<<< Updated upstream
=======
            'logoKabupaten' => Setting::get('logo_kabupaten'),
            'logoDesa' => Setting::get('logo_desa'),
>>>>>>> Stashed changes
        ]);
    }

    private function suratPerBulan(): array
    {
        $labels = [];
        $data = [];
        $bulanId = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($i = 11; $i >= 0; $i--) {
            $bulan = CarbonImmutable::now()->subMonths($i);
            $labels[] = $bulanId[$bulan->month].' '.$bulan->year;
            $data[] = Surat::whereYear('tanggal_surat', $bulan->year)
                ->whereMonth('tanggal_surat', $bulan->month)
                ->count();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function jenisTerbanyak(): array
    {
        $rows = Surat::selectRaw('jenis_surat_id, COUNT(*) as total')
            ->groupBy('jenis_surat_id')
            ->with('jenisSurat:id,nama_surat,kode_surat')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'labels' => $rows->map(fn ($r) => $r->jenisSurat->kode_surat ?? '-')->all(),
            'data' => $rows->pluck('total')->map(fn ($v) => (int) $v)->all(),
        ];
    }
}
