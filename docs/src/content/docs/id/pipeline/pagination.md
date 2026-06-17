---
title: Pagination
description: Paginasi data otomatis menggunakan PaginatePipe.
---

Paginasi di Kinetics ditangani sebagai tahap paling akhir dalam alur *pipeline*. Setelah query difilter, dicari, dan diurutkan, `PaginatePipe` akan mengeksekusi *query* tersebut menggunakan metode paginasi bawaan Eloquent.

## Bagaimana PaginatePipe Bekerja

Tidak ada konfigurasi tambahan yang diperlukan dari sisi Anda. `PaginatePipe` dimasukkan secara *default* ke dalam setiap *pipeline* `Table` class.

```php
// Paginasi sudah berjalan secara otomatis di belakang layar.
$table = Table::model(User::class)->columns([...])->make();
```

`PaginatePipe` akan:
1. Membaca parameter request saat ini (misalnya `?page=2`).
2. Menambahkan `->paginate()` (atau metode yang kompatibel) ke *query builder*.
3. Menyematkan *metadata* paginasi (seperti total baris, jumlah halaman, dan *link* halaman sebelumnya/berikutnya) ke dalam *Response JSON* akhir.

Di sisi frontend, komponen `<Table />` akan secara otomatis membaca metadata ini dan merender kontrol navigasi paginasi di bawah tabel.
