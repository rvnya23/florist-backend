<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Isyarat Manis', 'price' => 367000, 'description' => 'Bucket mawar pink ukuran kecil yang manis dan hangat. Cocok sebagai hadiah sederhana untuk menyampaikan perhatian, rasa kagum, atau sekadar membuat hari seseorang lebih cerah.', 'source' => 'pink kecil.jpg'],
            ['name' => 'Semesta Rasa', 'price' => 999000, 'description' => 'Bucket mawar pink ukuran besar dengan nuansa lembut dan elegan. Cocok untuk momen spesial dan ungkapan kasih.', 'source' => 'pink besar.jpg'],
            ['name' => 'Langka Mimpi', 'price' => 780000, 'description' => 'Bucket mawar biru ukuran besar bernuansa unik dan penuh makna. Cocok untuk hadiah istimewa.', 'source' => 'biru besar.jpg'],
            ['name' => 'Teduh Rasa', 'price' => 345000, 'description' => 'Bucket mawar biru ukuran kecil bernuansa tenang dan romantis. Cocok untuk hadiah bermakna.', 'source' => 'biru kecil.jpg'],
            ['name' => 'Awal Pagi', 'price' => 199000, 'description' => 'Bucket mawar putih ukuran kecil bernuansa bersih dan sederhana. Cocok untuk ungkapan tulus.', 'source' => 'putih kecil.jpg'],
            ['name' => 'Tenang Abadi', 'price' => 600000, 'description' => 'Bucket mawar putih ukuran besar bernuansa anggun dan tulus. Cocok untuk pernikahan dan lamaran.', 'source' => 'putih besar.jpg'],
            ['name' => 'Api Cinta', 'price' => 1000000, 'description' => 'Bucket mawar merah ukuran besar bernuansa cinta yang kuat dan berani. Cocok untuk momen romantis.', 'source' => 'merah besar.jpg'],
            ['name' => 'Janji Senja', 'price' => 499000, 'description' => 'Bucket mawar merah ukuran kecil bernuansa romantis dan elegan. Cocok untuk hadiah sederhana tapi bermakna.', 'source' => 'merah kecil.jpg'],
            ['name' => 'Luruh Anggun', 'price' => 189000, 'description' => 'Bucket mawar ungu ukuran kecil bernuansa manis dan elegan. Cocok untuk ungkapan kagum dan rindu.', 'source' => 'ungu kecil.jpg'],
            ['name' => 'Anggun Raya', 'price' => 799000, 'description' => 'Bucket mawar ungu ukuran besar bernuansa elegan dan berkelas. Cocok untuk momen spesial.', 'source' => 'ungu besar.jpg'],
            ['name' => 'Elegi Malam', 'price' => 1399000, 'description' => 'Bucket mawar hitam ukuran besar bernuansa misterius dan eksklusif. Cocok untuk tampilan yang berani dan unik.', 'source' => 'hitam besar.jpg'],
            ['name' => 'Rahasia Sunyi', 'price' => 550000, 'description' => 'Bucket mawar hitam ukuran kecil bernuansa elegan dan berkarakter. Cocok untuk tampilan simpel namun berkelas.', 'source' => 'hitam kecil.jpg'],
        ];

        // Ensure the products directory exists in storage
        Storage::disk('public')->makeDirectory('products');

        foreach ($products as $productData) {
            $sourcePath = public_path('assets/images/'.$productData['source']);
            $storagePath = 'products/'.$productData['source'];

            // Copy image from public/assets/images to storage/app/public/products
            if (File::exists($sourcePath)) {
                Storage::disk('public')->put($storagePath, File::get($sourcePath));
            }

            Product::create([
                'name' => $productData['name'],
                'price' => $productData['price'],
                'description' => $productData['description'],
                'image' => $storagePath,
            ]);
        }
    }
}
