<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /** @var array<int, string> */
    private array $firstNames = [
        'Ahmad', 'Aditya', 'Aisyah', 'Alif', 'Amira', 'Annisa', 'Aulia', 'Bagas', 'Bayu', 'Bilal',
        'Bunga', 'Cahya', 'Damar', 'Dewi', 'Dimas', 'Eka', 'Fahri', 'Farhan', 'Fatimah', 'Fitri',
        'Galih', 'Gita', 'Hanif', 'Hana', 'Hasan', 'Husna', 'Ilham', 'Indah', 'Iqbal', 'Jasmine',
        'Kevin', 'Khadijah', 'Laila', 'Luthfi', 'Mahmud', 'Malik', 'Maryam', 'Maulana', 'Maya', 'Mira',
        'Nadia', 'Naufal', 'Nayla', 'Nabil', 'Oki', 'Puspita', 'Rahma', 'Rafi', 'Raihan', 'Rakha',
        'Randi', 'Reza', 'Rizka', 'Salsabila', 'Salman', 'Sari', 'Satria', 'Sausan', 'Siti', 'Sultan',
        'Syifa', 'Tariq', 'Tiara', 'Umar', 'Umi', 'Vina', 'Wahyu', 'Widya', 'Yasin', 'Yoga',
        'Yumna', 'Zahra', 'Zaid', 'Zulfikar',
    ];

    /** @var array<int, string> */
    private array $lastNames = [
        'Ananda', 'Anggraini', 'Assegaf', 'Basri', 'Batubara', 'Cahyani', 'Darmawan', 'Fadhilah',
        'Fadli', 'Gani', 'Hamzah', 'Harahap', 'Hasibuan', 'Hidayat', 'Ihsan', 'Imron', 'Iskandar',
        'Jaafar', 'Kahfi', 'Kumalasari', 'Labib', 'Latifah', 'Mahendra', 'Mahmud', 'Muktadir',
        'Nabila', 'Nugraha', 'Nursalim', 'Pratama', 'Putri', 'Rahmadani', 'Ramadhan', 'Rasyid',
        'Rohmah', 'Sabrina', 'Sanjaya', 'Saputra', 'Setiawan', 'Sihombing', 'Simanjuntak', 'Siregar',
        'Situmorang', 'Suharto', 'Sukmana', 'Suryana', 'Tanjung', 'Wibowo', 'Wijaya', 'Yusuf', 'Zahid',
    ];

    public function run(): void
    {
        ClassRoom::query()->orderBy('name')->get()->each(function (ClassRoom $class, int $classIndex) {
            for ($i = 0; $i < 15; $i++) {
                $first = $this->firstNames[($classIndex * 9 + $i * 5) % count($this->firstNames)];
                $last = $this->lastNames[($classIndex * 11 + $i * 7 + $classIndex) % count($this->lastNames)];

                Student::firstOrCreate([
                    'class_id' => $class->id,
                    'name' => "{$first} {$last}",
                    'sort_order' => $i,
                ]);
            }
        });
    }
}
