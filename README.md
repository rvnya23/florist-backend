# Sudut Flora Backend

Sudut Flora Backend adalah bagian aplikasi yang bekerja di belakang layar. Kalau Sudut Flora diibaratkan warung seblak, backend ini adalah area dapurnya: menerima pesanan data, memeriksa isi permintaan, menyimpan data, lalu memberikan hasilnya ke bagian depan aplikasi.

Backend ini dibuat dengan Laravel 13 dan dipakai oleh aplikasi `florist-frontend`. Pengunjung tidak membuka backend secara langsung melalui tampilan khusus.

## Tugas utama backend

Backend menangani:

- data produk dan gambar bunga;
- pendaftaran serta akun member;
- login dan logout admin maupun member;
- pesan dan kesan dari pengunjung;
- isi halaman Tentang Kami;
- keranjang, checkout, order, pembayaran transfer manual, dan rekening bank;
- ringkasan jumlah produk, member, testimoni, order, dan pembayaran untuk dashboard admin;
- pembatasan akses agar menu pengelolaan hanya dapat digunakan oleh akun admin.

## Cara backend dan frontend bekerja sama

Frontend mengirim permintaan melalui API. API adalah jalur komunikasi antaraplikasi, mirip pelayan yang membawa catatan pesanan dari meja pelanggan ke dapur. Alamat utama jalur tersebut adalah:

```text
http://sudut-flora.test/api/v1
```

Contoh alurnya:

1. Pengunjung membuka halaman Produk di frontend.
2. Frontend meminta daftar produk kepada backend.
3. Backend mengambil data dari database.
4. Backend mengirim nama, harga, deskripsi, dan alamat gambar.
5. Frontend merapikan data tersebut menjadi kartu produk yang bisa dilihat pengunjung.

## Fitur untuk pengunjung dan member

| Kegiatan | Hasilnya |
| --- | --- |
| Melihat produk | Backend mengirim seluruh produk, dari data terbaru. |
| Mendaftar member | Dibuatkan akun pelanggan sekaligus data member. Email dan nomor HP tidak boleh kembar. |
| Login member | Hanya akun dengan peran `customer` yang dapat masuk melalui halaman login member. |
| Membuka profil | Nama, email, nomor HP, dan alamat member dikirim ke frontend. |
| Mengelola keranjang | Member dapat menyimpan beberapa produk dan mengubah jumlahnya sebelum checkout. |
| Membuat order | Backend menghitung ulang harga produk, menyimpan alamat tujuan, lalu mengosongkan keranjang. |
| Mengirim bukti bayar | Member memilih rekening tujuan dan mengunggah bukti transfer untuk diperiksa admin. |
| Melihat riwayat order | Member hanya dapat melihat order miliknya sendiri. |
| Mengirim pesan dan kesan | Pesan disimpan agar dapat diperiksa melalui panel admin. |
| Membaca Tentang Kami | Isi ditampilkan sesuai urutan yang ditentukan admin. |

Password member minimal 8 karakter dan harus diketik dua kali saat pendaftaran. Form publik mengirim opsi `auto_login` agar pengguna langsung login. Jika endpoint dipanggil tanpa opsi tersebut, akun tetap dibuat tetapi tidak otomatis login.

## Fitur untuk admin

Admin dapat:

- melihat jumlah produk, member, dan testimoni di dashboard;
- menambah, melihat, mengubah, dan menghapus produk;
- mengelola data member;
- mengelola pesan dan kesan;
- mengatur bagian-bagian halaman Tentang Kami beserta urutan tampilnya;
- memeriksa order, memverifikasi atau menolak bukti bayar, dan mengubah status order;
- mengelola rekening bank tujuan transfer.

Gambar produk menerima format JPG, JPEG, PNG, atau WEBP dengan ukuran paling besar 2 MB. Saat gambar produk diganti atau produknya dihapus, file gambar lama juga dibersihkan.

Tidak ada pendaftaran admin dari halaman publik. Ini disengaja agar orang asing tidak bisa tiba-tiba mengambil celemek dan masuk ke dapur. Akun admin harus sudah disiapkan oleh pengelola database. Pada database lama yang menjalankan migrasi akun pelanggan, seluruh akun yang sudah ada saat itu ditandai sebagai admin.

## Catatan penting tentang data member

Ada dua cara data member masuk ke aplikasi, dan hasilnya berbeda:

- Pendaftaran dari halaman publik membuat akun login dan data member sekaligus.
- Penambahan member dari panel admin hanya membuat catatan member. Cara ini tidak membuat password atau akun yang bisa dipakai login.

Menghapus catatan member melalui panel admin juga tidak otomatis menghapus akun pengguna yang pernah terhubung dengannya. Bagian ini perlu diperhatikan saat membersihkan data.

## Struktur data yang disimpan

Database adalah lemari penyimpanan aplikasi. Bukan lemari yang isinya kerupuk seblak, tetapi tabel-tabel berikut:

| Data | Isi utama |
| --- | --- |
| `products` | Nama produk, harga, deskripsi, dan lokasi gambar. |
| `members` | Nama, email, nomor HP, alamat, dan hubungan ke akun pengguna. |
| `testimonials` | Nama, email, serta pesan dan kesan. |
| `about_us_sections` | Kunci bagian, judul, isi, dan urutan tampil. |
| `users` | Nama, email, password yang sudah diamankan, dan peran admin atau customer. |
| `sessions` | Catatan sesi pengguna yang sedang login. |
| `carts` dan `cart_items` | Keranjang aktif setiap member beserta produk dan jumlahnya. |
| `orders` dan `order_details` | Ringkasan order serta salinan harga produk ketika checkout. |
| `transaksi_order` | Bukti dan status pemeriksaan pembayaran untuk sebuah order. |
| `rekening_bank` | Daftar rekening bank tujuan transfer yang dikelola admin. |

## Jalur API

Bagian ini sedikit teknis, tetapi berguna saat mencari sumber masalah. Endpoint adalah alamat tujuan khusus, seperti nomor meja untuk setiap jenis pesanan.

### Jalur umum

| Metode dan alamat | Kegunaan |
| --- | --- |
| `GET /api/v1/products` | Mengambil produk. Parameter `limit` boleh dipakai untuk membatasi hasil. |
| `GET /api/v1/about-us` | Mengambil isi Tentang Kami. |
| `POST /api/v1/members` | Mendaftarkan akun member baru. |
| `POST /api/v1/testimonials` | Menyimpan pesan dan kesan. |
| `POST /api/v1/auth/customer/login` | Login khusus member. |
| `POST /api/v1/auth/login` | Login khusus admin. |
| `GET /api/v1/auth/user` | Memeriksa pengguna yang sedang login. |
| `POST /api/v1/auth/logout` | Mengakhiri sesi login. |
| `GET /api/v1/cart` | Mengambil keranjang member yang sedang login. |
| `POST /api/v1/cart/items` | Menambahkan produk ke keranjang. |
| `PATCH /api/v1/cart/items/{cartItem}` | Mengubah jumlah produk di keranjang. |
| `DELETE /api/v1/cart/items/{cartItem}` | Menghapus produk dari keranjang. |
| `GET /api/v1/orders` | Mengambil riwayat order milik member. |
| `POST /api/v1/orders` | Checkout keranjang menjadi order. |
| `GET /api/v1/orders/{order}` | Mengambil detail order milik member. |
| `POST /api/v1/orders/{order}/payment` | Mengunggah atau mengirim ulang bukti pembayaran. |
| `GET /api/v1/bank-accounts` | Mengambil rekening bank aktif untuk pembayaran. |

### Jalur admin

Semua jalur berikut diawali `/api/v1/admin` dan hanya menerima pengguna yang sudah login sebagai admin:

- `/dashboard`
- `/products`
- `/members`
- `/testimonials`
- `/about-us`
- `/orders`
- `/bank-accounts`

Data pada halaman daftar admin dibagi menjadi 15 baris per halaman supaya tabel tidak seperti panci yang kepenuhan topping.

## Menjalankan aplikasi

### Kebutuhan awal

- Laravel Herd untuk macOS;
- PHP 8.3 atau lebih baru yang dipilih melalui Herd;
- Composer yang sudah tersedia bersama Herd;
- SQLite. Tidak perlu menyalakan server database terpisah.

### 1. Membuka project melalui Laravel Herd

1. Buka aplikasi Laravel Herd.
2. Masuk ke bagian **Sites**.
3. Pilih **Add** atau tanda **+**, lalu pilih **Link Existing Project**.
4. Pilih folder `florist-backend`.
5. Gunakan nama situs `sudut-flora`.

Backend kemudian dapat dibuka melalui:

```text
http://sudut-flora.test
```

Herd bekerja sebagai server lokal. Artinya kita tidak perlu menjalankan `php artisan serve` setiap kali ingin membuka backend.

### 2. Menyiapkan SQLite

```bash
cp .env.example .env
touch database/database.sqlite
```

SQLite menyimpan seluruh database dalam satu file, yaitu `database/database.sqlite`. Analoginya seperti satu kotak bekal yang sudah memuat semua isi, jadi tidak perlu membuka dapur database lain seperti MySQL.

Pastikan bagian berikut ada di `.env`:

```env
APP_URL=http://sudut-flora.test

DB_CONNECTION=sqlite

FRONTEND_URLS=http://app.sudut-flora.test:5173
SANCTUM_STATEFUL_DOMAINS=app.sudut-flora.test:5173
SESSION_DOMAIN=.sudut-flora.test
```

Variabel `SQLITE_DATABASE` tidak wajib diisi. Jika dikosongkan, aplikasi otomatis memakai `database/database.sqlite`.

### 3. Memasang dan menyiapkan backend

```bash
composer setup
```

Perintah tersebut memasang kebutuhan Laravel, membuat kunci aplikasi, menyiapkan tabel SQLite, dan membuat sambungan folder gambar publik.

Jika konfigurasi pernah tersimpan sebelum `.env` diubah, bersihkan dengan:

```bash
php artisan config:clear
```

Setelah itu, pastikan Herd sedang aktif lalu buka `http://sudut-flora.test/up`. Jika tampil status aplikasi sehat, backend sudah berjalan.

Jika ingin memasukkan contoh produk dan isi Tentang Kami, jalankan:

```bash
php artisan db:seed
```

Seeder adalah pengisi data awal, seperti menyiapkan bahan sebelum warung dibuka. Perlu dicatat, gambar contoh produk hanya tersalin jika file sumbernya tersedia di `public/assets/images` pada backend.

## Pengaturan hubungan dengan frontend

Backend Herd dan frontend Vite menggunakan dua subdomain dari `sudut-flora.test`. Susunan ini penting karena Sanctum mengandalkan cookie sesi dan mengharuskan frontend serta API berada dalam satu keluarga domain.

Nilai backend yang dipakai adalah:

```env
APP_URL=http://sudut-flora.test
FRONTEND_URLS=http://app.sudut-flora.test:5173
SANCTUM_STATEFUL_DOMAINS=app.sudut-flora.test:5173
SESSION_DOMAIN=.sudut-flora.test
```

Sanctum adalah penjaga sesi login. Analoginya seperti stempel tangan setelah masuk acara: frontend membawa bukti sesi, lalu backend mengenali bahwa pengguna tersebut sudah login.

Jangan membuka frontend melalui `localhost:5173` ketika memakai susunan ini. Gunakan `http://app.sudut-flora.test:5173` agar cookie login terbaca dengan benar.

## Pemeriksaan sebelum dianggap siap

```bash
./vendor/bin/pint --test
php artisan route:list
```

`Pint` memeriksa kerapian penulisan kode. `route:list` memperlihatkan daftar alamat yang dilayani backend.

## Batas aplikasi saat ini

- Harga order hanya menghitung produk; ongkos kirim belum diterapkan.
- Testimoni yang dikirim pengunjung tersimpan untuk admin, tetapi tidak tersedia sebagai daftar publik dari API.
- Instalasi database yang benar-benar baru belum otomatis memiliki akun admin.
- Data member yang dibuat admin belum menjadi akun login member.

## Pasangan aplikasi

Tampilan yang digunakan pengunjung dan admin berada di repository `florist-frontend`. Kedua aplikasi perlu menyala bersamaan saat dipakai secara lokal.
