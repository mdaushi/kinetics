---
title: Filters Overview
description: Pengenalan ke sistem filter yang powerful di Kinetics.
---

Filter memungkinkan pengguna mempersempit *dataset* berdasarkan kriteria spesifik. Berbeda dengan pencarian global (yang mencari teks secara luas menggunakan `LIKE`), filter menargetkan kolom yang spesifik dan dapat menggunakan berbagai operator logika (seperti pencocokan persis, rentang, atau perbandingan).

## Mendaftarkan Filter

Anda mendefinisikan filter pada tabel dengan melemparkan *array* objek filter ke dalam metode `filters()`.

```php
use Kinetics\Table;
use Kinetics\Filters\TextFilter;
use Kinetics\Filters\SelectFilter;
use Kinetics\Filters\DateFilter;

$table = Table::model(User::class)
    ->filters([
        TextFilter::make('name'),
        SelectFilter::make('status')->options(['active' => 'Aktif', 'banned' => 'Diblokir']),
        DateFilter::make('created_at')->range(),
    ])
    ->columns([
        // ...
    ])
    ->make();
```

## Cara Kerjanya

Di balik layar, `FilterPipe` secara otomatis membaca parameter *request* HTTP yang masuk. Jika ia mendeteksi adanya parameter filter yang cocok dengan *key* filter yang terdaftar, ia akan menyuntikkan *query* `WHERE` yang tepat ke Eloquent.

Kinetics juga otomatis menghasilkan komponen antarmuka (*frontend UI*) yang sesuai dengan jenis filternya (misal: *dropdown* untuk `SelectFilter`, pemilih tanggal untuk `DateFilter`).

## Relasi Data (Dot Notation)

Sama seperti sistem Kolom, sistem Filter mendukung relasi Eloquent secara penuh menggunakan *dot notation*! Jika Anda ingin memfilter *record* berdasarkan model relasinya, cukup gunakan nama relasinya.

Kinetics cukup cerdas untuk membungkus kondisi Anda di dalam blok query `whereHas` secara otomatis.

```php
// Otomatis membuat query whereHas('company')!
TextFilter::make('company.name')->label('Nama Perusahaan')
```

Di halaman selanjutnya, kita akan membedah setiap tipe filter secara spesifik.
