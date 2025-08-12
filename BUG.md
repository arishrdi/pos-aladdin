# Bug pada Sistem

## Quality Control, 12 Agustus 2025

### POS **(critical)**

`resources/views/pos/index.blade.php`

- Saat berhasil melakukan transaksi, stok (qty) salah. misal stoknya saat ini adalah 10, dikeranjang 2 jadi stoknya sekarang 8. tetapi ketika berhasil melakukan transaksi, stoknya kembali ke 10. ketika di refresh baru akan tampil stok aktual (8)
- bonus tidak mampu mengurangi stok pada daftar produk dan real stok (cuma mampu mengurangi 1)

### Modal Pembayaran **(medium)**

`resources/views/partials/pos/payment-modal.blade.php`

- Saat modal pembayaran muncul maka metode pembayaran nya adalah Cash (default) tetapi komponen untuk upload bukti pembayaran tidak muncul dan akan muncul jika user telah memilih metode pembayaran lain.
- Saat memilih NonPKP maka akan tampil NonPKP rekening, tetapi ketika modal di tutup dan diganti jadi PKP maka yang tampil masih rekening NonPKP dan metode pembayaran adalah Cash/Tunai

### Detail Transaksi **(medium)**

`resources/views/dashboard/closing/riwayat-transaksi.blade.php`, `resources/views/partials/pos/modal/modal-history-transaksi.blade.php`

- Detail transaksi kurang lengkap, tampilkan bonus, kategori transaksi dan mungkin informasi lain yang berkaitan

### Riwayat Transaksi (Kasir) **(high)**

`resources/views/partials/pos/history-modal.blade.php`

- Tombol Cetak Nota/Receipt harusnya hanya muncul jika transaksi sukses.
- Tombol pelunasan DP terlalu kecil

### Riwayat Transaksi (Admin) **(medium)**

`resources/views/dashboard/closing/riwayat-transaksi.blade.php`

- Tombol-tombol pada aksi kurang menjelaskan fungsinya

### Email **(critical)**

`app/Mail/ApprovalRequest.php`

- Email tidak terkirim walau pada Log, informasi nya terkirim

``` log
[2025-08-12 13:44:41] local.INFO: Sending email to supervisor: ini.alternatif.email@gmail.com for PEMBATALAN TRANSAKSI of order INV-1754974211-LDFSI0
[2025-08-12 13:44:41] local.DEBUG: From: POS Aladdin Karpet <no-reply@demowebjalan.com>
To: ini.alternatif.email@gmail.com
Subject: Permintaan Persetujuan POS Aladdin
MIME-Version: 1.0
Date: Tue, 12 Aug 2025 13:44:41 +0700
Message-ID: <5dbc22e7600719cc40d3b82e864974d3@demowebjalan.com>
Content-Type: text/html; charset=utf-8
Content-Transfer-Encoding: quoted-printable
```

### Produk **(critical)**

`app/Http/Controllers/ProductController.php`

- Jika saya punya produk dengan distribusi outlet: 1, 2, 3. Lalu saya mengedit distribusi Outlet ke 2 dan 3. dan mengembalikan lagi distribusinya ke 1, 2, 3 maka stok yang ada pada outlet 1 jadi 0. Padahal stok sebelum di edit distribusinya bukan 0

```php
foreach ($request->outlet_ids as $outletId) {
    $currentQuantity = Inventory::where('product_id', $product->id)
        ->where('outlet_id', $outletId)
        ->value('quantity') ?? 0;
    Inventory::updateOrCreate(
        [
            'product_id' => $product->id,
            'outlet_id' => $outletId
        ],
        [
            'quantity' => $currentQuantity,
            'min_stock' => $request->min_stock
        ]
    );
}
Inventory::where('product_id', $product->id)
    ->whereNotIn('outlet_id', $request->outlet_ids)
    ->delete();
```

### Kas **(high)**

`resources/views/dashboard/closing/approval-kas.blade.php`

- Pada riwayat kas, saldo akhir yang tidak menghitung dari penjualan sehingga nilainya beda dengan kas yang sebenarnya

### Sidebar **(low)**

`resources/views/layouts/sidebar.blade.php`

- Tampilan sidebar yang membuat aplikasi jadi kurang responsive
- Label pada menu sidebar tidak ada
- Harus menekan tombol pada kanan bawah untuk menutup sidebar, padahal tombol ini seharusnya dihapus

### Stok **(high)**

`resources/views/pos/index.blade.php`,  `app/Http/Controllers/InventoryController.php`, `app/Http/Controllers/InventoryHistoryController.php`

- Stok harus benar-benar tampil secara desimal (tambah/kurang)
- User dapat mengedit stok secara desimal

### Tambahan **(low)**

- Bisa di tambahkan polling pada beberapa bagian agar user tidak perlu refresh untuk melihat data terbaru

## *File ini akan terus diupdate jika ada bug*