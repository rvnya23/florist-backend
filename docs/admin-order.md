# Rancangan Fitur Order pada Panel Admin

## Tujuan

Panel admin digunakan untuk memantau order, memverifikasi pembayaran transfer manual, mengelola status order, dan mengelola rekening bank tujuan.

## Menu Admin

Tambahkan dua menu pada sidebar admin:

1. **Data Order**
2. **Rekening Bank**

## Data Order

### Daftar order

Halaman daftar order menampilkan:

- Kode order
- Nama member
- Tanggal order
- Total harga
- Status order
- Status pembayaran
- Tombol detail

Daftar dapat difilter berdasarkan status order dan status pembayaran.

### Detail order

Halaman detail order menampilkan:

- Kode dan tanggal order
- Nama, email, dan nomor telepon member
- Alamat pengiriman
- Catatan order
- Daftar produk
- Harga satuan, jumlah, dan subtotal setiap produk
- Total harga order
- Status order
- Rekening bank tujuan
- Data dan bukti pembayaran

### Status order

Status yang digunakan:

- `pending_payment`: menunggu pembayaran
- `confirmed`: pembayaran sudah diverifikasi dan order dikonfirmasi
- `completed`: order selesai
- `cancelled`: order dibatalkan

Order tidak dapat dihapus agar riwayat penjualan tetap tersimpan.

## Verifikasi Pembayaran

Pembayaran menggunakan transfer bank manual.

Alurnya:

1. Member membuat order.
2. Member memilih rekening bank tujuan yang aktif.
3. Member melakukan transfer.
4. Member mengunggah bukti transfer pada order miliknya.
5. Sistem membuat data `transaksi_order` dengan status `pending`.
6. Admin memeriksa detail dan bukti transfer.
7. Admin menerima atau menolak pembayaran.

Status pembayaran:

- `pending`: menunggu verifikasi admin
- `verified`: pembayaran diterima
- `rejected`: pembayaran ditolak

Jika pembayaran diterima, status order berubah menjadi `confirmed`. Jika pembayaran ditolak, admin wajib mengisi catatan penolakan agar member dapat memperbaiki atau mengunggah ulang bukti.

Admin dapat mengunggah bukti pembayaran atas nama member sebagai fitur cadangan.

## Data Transaksi Order

Tabel `transaksi_order` menyimpan:

- Relasi ke order
- Relasi ke rekening bank tujuan
- Kode pembayaran
- Nama pengirim
- Tanggal pembayaran
- Jumlah pembayaran
- Bukti pembayaran
- Status pembayaran
- Admin yang memverifikasi
- Waktu verifikasi
- Catatan penolakan
- Waktu pembuatan dan perubahan data

Satu order maksimal memiliki satu `transaksi_order`.

## Rekening Bank

Admin dapat:

- Melihat daftar rekening
- Menambah rekening
- Mengubah rekening
- Mengaktifkan rekening
- Menonaktifkan rekening

Data rekening terdiri dari:

- Nama bank
- Nomor rekening
- Nama pemilik rekening
- Status aktif

Hanya rekening aktif yang ditampilkan kepada member. Rekening yang sudah pernah digunakan pada pembayaran tidak dihapus, tetapi dinonaktifkan agar riwayat pembayaran tetap valid.

## Dashboard Admin

Tambahkan ringkasan:

- Total order
- Order menunggu pembayaran
- Pembayaran menunggu verifikasi
- Order yang sudah dikonfirmasi

## Rencana Endpoint Admin

| Method | Endpoint | Fungsi |
| --- | --- | --- |
| `GET` | `/api/v1/admin/orders` | Daftar order |
| `GET` | `/api/v1/admin/orders/{order}` | Detail order |
| `PATCH` | `/api/v1/admin/orders/{order}/status` | Mengubah status order |
| `POST` | `/api/v1/admin/orders/{order}/payment` | Menambah pembayaran atas nama member |
| `POST` | `/api/v1/admin/orders/{order}/payment/verify` | Menerima pembayaran |
| `POST` | `/api/v1/admin/orders/{order}/payment/reject` | Menolak pembayaran |
| `GET` | `/api/v1/admin/bank-accounts` | Daftar rekening bank |
| `POST` | `/api/v1/admin/bank-accounts` | Menambah rekening bank |
| `PUT` | `/api/v1/admin/bank-accounts/{bankAccount}` | Mengubah rekening bank |
| `PATCH` | `/api/v1/admin/bank-accounts/{bankAccount}/status` | Mengaktifkan atau menonaktifkan rekening |

Semua endpoint admin dilindungi oleh autentikasi Sanctum dan hanya dapat digunakan oleh user dengan role `admin`.
