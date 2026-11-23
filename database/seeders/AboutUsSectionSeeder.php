<?php

namespace Database\Seeders;

use App\Models\AboutUsSection;
use Illuminate\Database\Seeder;

class AboutUsSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'section_key' => 'tentang',
                'title' => 'Tentang Sudut Flora',
                'content' => "Sudut Flora adalah aplikasi florist modern yang menyediakan rangkaian bunga bermakna untuk setiap momen spesialmu.\nKami menghadirkan bunga sebagai bahasa perasaan—sederhana, indah, dan penuh makna.",
                'sort_order' => 1,
            ],
            [
                'section_key' => 'layanan',
                'title' => 'Layanan Kami',
                'content' => "1. Bucket bunga berbagai warna & ukuran.\n2. Custom rangkaian sesuai kebutuhan.\n3. Bunga untuk ulang tahun, wisuda, anniversary, pernikahan, hingga duka cita.",
                'sort_order' => 2,
            ],
            [
                'section_key' => 'keunggulan',
                'title' => 'Keunggulan',
                'content' => "1. Desain modern & mudah digunakan.\n2. Bunga segar dan berkualitas\n3. Pemesanan cepat & praktis.\n4. Layanan pelanggan responsif.",
                'sort_order' => 3,
            ],
            [
                'section_key' => 'filosofi',
                'title' => 'Filosofi',
                'content' => "Setiap sudut menyimpan rasa.\nKami percaya setiap bunga punya cerita.",
                'sort_order' => 4,
            ],
            [
                'section_key' => 'komitmen',
                'title' => 'Komitmen',
                'content' => 'Dirangkai dengan penuh perhatian, dikirim dengan tanggung jawab.',
                'sort_order' => 5,
            ],
        ];

        foreach ($sections as $section) {
            AboutUsSection::create($section);
        }
    }
}
