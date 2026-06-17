---
title: Searching
description: Cara kerja pencarian global di dalam tabel.
---

Fitur pencarian di Kinetics diatur oleh komponen yang disebut `SearchPipe`. Fitur ini memungkinkan pengguna untuk mencari kata kunci (*keyword*) di seluruh kolom tabel yang telah diatur sebagai `searchable`.

## Mengaktifkan Pencarian

Secara bawaan, kolom tidak akan disertakan dalam pencarian global untuk menghindari *query* database yang terlalu berat. Anda harus secara eksplisit mengaktifkannya pada masing-masing kolom dengan metode `searchable()`.

```php
use Kinetics\Table;
use Kinetics\Columns\TextColumn;

$table = Table::model(User::class)
    ->columns([
        // Kolom ini akan diabaikan oleh fitur pencarian
        TextColumn::make('id'),
        
        // Kolom ini akan dicari oleh SearchPipe
        TextColumn::make('name')->searchable(),
        TextColumn::make('email')->searchable(),
    ])
    ->make();
```

## Bagaimana SearchPipe Bekerja

Ketika request dari frontend mengirimkan parameter pencarian (misalnya: `?search=john`), `SearchPipe` akan:
1. Mendapatkan daftar semua kolom yang memiliki status `searchable`.
2. Menyatukannya ke dalam *query builder* Eloquent menggunakan klausa `LIKE` (atau setara, tergantung driver database).
3. Untuk kolom biasa, akan dibentuk query seperti: `WHERE name LIKE '%john%' OR email LIKE '%john%'`.
4. Untuk kolom relasi (misalnya `company.name`), akan dibentuk query `whereHas` atau mekanisme `join` otomatis agar pencarian dapat dilakukan menembus batas relasi.

> **Catatan Performa:** Semakin banyak kolom yang Anda atur sebagai `searchable()`, query yang dihasilkan akan semakin kompleks. Aktifkan fitur ini hanya pada kolom teks yang benar-benar relevan.
