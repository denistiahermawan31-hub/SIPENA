# SIPENA

Sistem Penilaian Pembiasaan Harian. Catatan pembiasaan positif keagamaan sekolah: sholat, Al-Qur'an, dan tadarus — diisi perwakilan kelas setiap hari sekolah (Senin–Jumat).

## Stack

- PHP 8.3+ / **Laravel 13** (Blade + Tailwind CSS v4)
- MySQL (Laragon), timezone `Asia/Jakarta` (WIB)

## Menjalankan

```sh
composer install
npm install && npm run build
php artisan migrate:fresh --seed   # buat database "sipena" bila belum ada
php artisan serve                  # atau via Laragon: http://sipena.test
```

## Akun dummy (seed)

- Perwakilan kelas: `x1`–`x4`, `xi1`–`xi3`, `xii1`–`xii3` — password `password`
- SuperAdmin (fase berikutnya): `superadmin` — password `password`

Dua contoh laporan kelas ditandai **Perlu Perbaikan** (kelas XI.2 dan XII.3) untuk demo alur verifikasi.

## Alur saat ini (fase user)

1. Perwakilan kelas login, mengisi penilaian harian (Senin–Jumat) di hari yang sama.
2. **Kirim Data Harian** → status *Terkirim* (data terkunci), atau *Simpan Draf* dulu.
3. SuperAdmin memverifikasi: *Diterima* / *Perlu Perbaikan* (hari ditandai bisa dibuka lagi).
4. Semua formulir terkunci otomatis setelah **Jumat 12.30 WIB**.
5. Rekap mingguan terbentuk otomatis dari kiriman harian; skor dikumpulkan untuk fase SuperAdmin (statistik/juara per tingkat X, XI, XII).

## Pengujian

```sh
php artisan test
```
