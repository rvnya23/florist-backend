# Rancangan Keranjang dan Checkout Publik

## Ketentuan Umum

- Pembelian hanya dapat dilakukan oleh member yang sudah login.
- Keranjang disimpan di database agar tetap tersedia setelah member logout atau menutup browser.
- Satu keranjang dapat berisi lebih dari satu produk.
- Checkout hanya menghitung harga produk, tanpa ongkos kirim.
- Pembayaran dilakukan melalui transfer bank manual.

## Tampilan Publik

Dropdown profile member menampilkan menu **Keranjang** beserta jumlah produk di dalamnya.

Halaman publik yang dibutuhkan:

1. Keranjang
2. Checkout
3. Riwayat order
4. Detail order
5. Form upload bukti pembayaran

## Struktur Keranjang

### Tabel `carts`

Satu member memiliki satu keranjang aktif.

- `id`
- `member_id`, foreign key dan unik
- `created_at`
- `updated_at`

### Tabel `cart_items`

- `id`
- `cart_id`, foreign key
- `product_id`, foreign key
- `quantity`
- `created_at`
- `updated_at`

Kombinasi `cart_id` dan `product_id` dibuat unik. Jika produk yang sama ditambahkan kembali, sistem menambah nilai `quantity`.

Harga tidak disimpan pada `cart_items`. Keranjang selalu menampilkan harga terbaru dari tabel `products`.

## Flow Keranjang

1. Member memilih produk.
2. Member menambahkan produk ke keranjang.
3. Member dapat menambahkan beberapa produk berbeda.
4. Member dapat mengubah jumlah atau menghapus produk.
5. Member dapat langsung checkout atau menundanya.

## Flow Checkout

1. Backend mengambil ulang seluruh produk dalam keranjang.
2. Backend menggunakan harga terbaru dari tabel `products`.
3. Member memasukkan alamat tujuan.
4. Backend menghitung subtotal setiap produk.
5. Backend menghitung total harga order.
6. Sistem membuat `orders` dan `order_details` dalam satu transaksi database.
7. Harga saat checkout disalin ke `order_details.harga_satuan` agar harga order tidak berubah ketika harga produk diperbarui.
8. Order dibuat dengan status `pending_payment`.
9. Keranjang dikosongkan setelah order berhasil dibuat.
10. Member dapat memilih bayar sekarang atau bayar nanti melalui riwayat order.

Rumus kalkulasi:

```text
subtotal item = harga produk × quantity
total order = jumlah seluruh subtotal item
```

Saat ini tidak ada perhitungan ongkos kirim.

## Flow Pembayaran

Rekening bank tidak dipilih saat checkout dan `rekening_bank_id` tidak disimpan pada tabel `orders`.

Saat member akan membayar:

1. Member membuka order dengan status `pending_payment`.
2. Member memilih salah satu rekening bank yang aktif.
3. Member memasukkan nama pengirim.
4. Member memasukkan jumlah pembayaran.
5. Member mengunggah bukti transfer.
6. Sistem membuat `transaksi_order` dengan status `pending`.
7. Admin memverifikasi atau menolak pembayaran.

Rekening yang dipilih disimpan melalui `transaksi_order.rekening_bank_id`.

## Tanggal Pembayaran

Field `tanggal_pembayaran` memiliki aturan:

- Otomatis terisi tanggal saat member membuka form upload bukti pembayaran.
- Menggunakan timezone aplikasi `Asia/Jakarta`.
- Tetap dapat diubah oleh member.
- Tidak boleh diisi dengan tanggal setelah hari ini.
- Jika tidak dikirim oleh frontend, backend menggunakan tanggal hari ini.

Rencana validasi backend:

```php
'tanggal_pembayaran' => ['nullable', 'date', 'before_or_equal:today'],
```

## Status

Status order:

- `pending_payment`
- `confirmed`
- `completed`
- `cancelled`

Status pembayaran:

- `pending`
- `verified`
- `rejected`
