<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\DailyReport;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $perwakilan;

    private Carbon $today;

    protected function setUp(): void
    {
        parent::setUp();

        // Selalu uji pada hari Rabat 09.00 WIB minggunya
        $this->today = Carbon::now('Asia/Jakarta')->startOfWeek()->addDays(2)->setTime(9, 0);
        Carbon::setTestNow($this->today);

        $this->seed(DatabaseSeeder::class);

        $this->perwakilan = User::where('username', 'x1')->firstOrFail();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function payload(string $aksi): array
    {
        $students = $this->perwakilan->classRoom->students;

        $nilai = [];
        foreach ($students as $i => $student) {
            $nilai[$student->id] = [
                'duha' => ['hadir', 'munfarid', 'tidak_sekolah'][$i % 3],
                'dzuhur' => ['hadir', 'haid', 'tidak_sholat'][$i % 3],
                'quran' => ['bawa', 'tidak_bawa', 'tidak_sekolah'][$i % 3],
                'tadarus' => 'tidak',
            ];
        }

        return ['aksi' => $aksi, 'nilai' => $nilai];
    }

    public function test_halaman_penilaian_hari_ini_menampilkan_15_siswa(): void
    {
        $this->actingAs($this->perwakilan)
            ->get('/penilaian')
            ->assertOk()
            ->assertSee('15 siswa')
            ->assertSee('Penilaian Harian Kelas X.1')
            ->assertSee('Sholat Duha')
            ->assertSee('Sholat Dzuhur')
            ->assertSee("Al-Qur'an");
    }

    public function test_kirim_data_harian_mengunci_data(): void
    {
        $today = $this->today->toDateString();

        $response = $this->actingAs($this->perwakilan)
            ->post(route('penilaian.save', $today), $this->payload('kirim'));

        $response->assertRedirect(route('riwayat.index'));

        $report = DailyReport::where('class_id', $this->perwakilan->class_id)
            ->whereDate('report_date', $today)
            ->firstOrFail();

        $this->assertSame('terkirim', $report->status);
        $this->assertNotNull($report->submitted_at);
        $this->assertSame(15, Assessment::where('daily_report_id', $report->id)->count());

        // Skor baris pertama: Duha hadir (+2) + Dzuhur hadir (+2) + bawa (+2) + tadarus tidak (0) = +6
        $first = Assessment::where('daily_report_id', $report->id)
            ->where('student_id', $this->perwakilan->classRoom->students->first()->id)
            ->first();
        $this->assertSame(6, $first->total_score);
        $this->assertSame('hadir', $first->duha);
        $this->assertSame('hadir', $first->dzuhur);
        $this->assertSame(2, $first->duha_score);
        $this->assertSame(2, $first->dzuhur_score);
    }

    public function test_data_terkirim_tidak_bisa_diubah_lagi(): void
    {
        $today = $this->today->toDateString();

        $this->actingAs($this->perwakilan)
            ->post(route('penilaian.save', $today), $this->payload('kirim'))
            ->assertRedirect(route('riwayat.index'));

        $before = Assessment::where('student_id', $this->perwakilan->classRoom->students->first()->id)->first()->duha;

        $this->actingAs($this->perwakilan)
            ->post(route('penilaian.save', $today), $this->payload('simpan'))
            ->assertRedirect();

        $this->assertSame('terkirim', DailyReport::whereDate('report_date', $today)->first()->status);
        $this->assertSame($before, Assessment::where('student_id', $this->perwakilan->classRoom->students->first()->id)->first()->duha);
    }

    public function test_setelah_jumat_1230_formulir_terkunci(): void
    {
        $friday = Carbon::now('Asia/Jakarta')->startOfWeek()->addDays(4)->setTime(13, 30);
        Carbon::setTestNow($friday);

        $response = $this->actingAs($this->perwakilan)->post(
            route('penilaian.save', $friday->toDateString()),
            $this->payload('kirim')
        );

        $response->assertRedirect(route('dashboard'))
            ->assertSessionHas('message', 'Penilaian hanya bisa diisi pada hari yang bersangkutan, sebelum Jumat 12.30 WIB.');

        $this->assertSame(
            0,
            DailyReport::where('class_id', $this->perwakilan->class_id)
                ->whereDate('report_date', $friday->toDateString())
                ->count()
        );
    }

    public function test_riwayat_dan_rekap_tersedia(): void
    {
        $this->actingAs($this->perwakilan)
            ->get('/riwayat')
            ->assertOk()
            ->assertSee('Riwayat Kiriman');

        $this->actingAs($this->perwakilan)
            ->get('/rekap')
            ->assertOk()
            ->assertSee('Rekap Mingguan');
    }
}
