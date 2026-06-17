---
title: Sorting
description: Mengurutkan data berdasarkan kolom tabel.
---

Pengurutan (*Sorting*) dikelola secara otomatis oleh `SortPipe`. Ini memungkinkan pengguna untuk mengklik *header* pada antarmuka tabel untuk mengurutkan data (Ascending atau Descending).

## Mengaktifkan Sorting

Mirip dengan fitur pencarian, Anda harus secara eksplisit mengizinkan kolom untuk dapat diurutkan menggunakan metode `sortable()`.

```php
use Kinetics\Table;
use Kinetics\Columns\TextColumn;

$table = Table::model(Product::class)
    ->columns([
        TextColumn::make('name')->sortable(),
        TextColumn::make('price')->sortable(),
        TextColumn::make('created_at')->label('Tanggal Dibuat')->sortable(),
    ])
    ->make();
```

Ketika Anda mengaktifkan `sortable()`, komponen frontend `<Table />` akan merender *header* tersebut sebagai elemen interaktif yang bisa diklik.

## Bagaimana SortPipe Bekerja

1. `SortPipe` akan memeriksa parameter request untuk mengetahui apakah ada *request* pengurutan (misalnya `?sort=price&direction=desc`).
2. Ia kemudian memvalidasi apakah kolom `price` benar-benar dideklarasikan dengan `sortable()`. Jika tidak, request pengurutan diabaikan (mencegah manipulasi *query* dari klien).
3. Jika valid, ia menambahkan klausa `ORDER BY price DESC` ke dalam *query builder* Eloquent Anda.

Hal ini menjamin bahwa seluruh data yang telah disortir dan dipaginasi selalu diproses di sisi database.
