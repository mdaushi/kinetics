---
title: Date Filter
description: Menggunakan komponen DateFilter untuk tanggal spesifik atau rentang tanggal.
---

`DateFilter` merender pemilih tanggal (*date picker*) di antarmuka UI dan secara otomatis mengaplikasikan logika *query timestamp* yang tepat ke database Anda.

## Penggunaan Dasar

Secara default, `DateFilter` berfungsi sebagai penyeleksi tanggal tunggal. Ia hadir dengan tiga operator bawaan:
- `equals` (Tanggal sama persis)
- `<` (Sebelum tanggal ini)
- `>` (Setelah tanggal ini)

```php
use Kinetics\Filters\DateFilter;

DateFilter::make('created_at')
```

## Pemilihan Rentang Tanggal (Date Range)

Alih-alih memilih satu tanggal tunggal, Anda dapat mengubah filter ini agar meminta dua tanggal: **Tanggal Mulai** (*Start Date*) dan **Tanggal Selesai** (*End Date*).

Untuk mengaktifkannya, panggil metode `range()`.

```php
use Kinetics\Filters\DateFilter;

DateFilter::make('created_at')->range()
```

Ketika `range()` diaktifkan:
1. Antarmuka *frontend* akan berubah menjadi dua kalender pemilih (*start* dan *end*).
2. Operatornya akan dikunci secara paksa menjadi `between` (di antara).
3. *Query* SQL yang dihasilkan akan menggunakan logika `WHERE ... BETWEEN`.

## Referensi API

| Metode | Deskripsi |
|--------|-------------|
| `make(string $key)` | Creates a new filter instance. |
| `label(string $label)` | Sets the filter label. |
| `column(string $column)`| Specifies the database column name if different from the key. |
| `relation(string $relation, string $relationKey)`| Explicitly sets the relationship to query. |
| `operators(array $operators)`| Overrides the allowed operators for this filter. |
| `range(bool $condition = true)`| Sets this filter to act purely as a date range filter (from-to). |
