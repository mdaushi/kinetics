---
title: Number Filter
description: Menggunakan komponen NumberFilter untuk data numerik dan rentang angka.
---

`NumberFilter` didesain secara spesifik untuk kolom-kolom yang memuat angka, seperti harga, kuantitas, umur, atau ID. Filter ini merender *input* angka di antarmuka depan dan mendukung pencarian matematis.

## Penggunaan Dasar

Secara bawaan, `NumberFilter` menyediakan operator-operator matematis agar pengguna dapat mencari nilai angka yang persis atau relatif.

```php
use Kinetics\Filters\NumberFilter;

NumberFilter::make('price')
```

Operator bawaan yang tersedia adalah:
- `equals` (Sama persis)
- `not_equals` (Tidak sama dengan)
- `>` (Lebih besar dari)
- `>=` (Lebih besar atau sama dengan)
- `<` (Lebih kecil dari)
- `<=` (Lebih kecil atau sama dengan)

## Rentang Angka (Min & Max)

Sama seperti `DateFilter`, komponen `NumberFilter` juga mendukung metode `range()` yang sangat hebat. 

Metode ini memungkinkan pengguna mencari angka di antara nilai minimum dan maksimum secara serentak (sebagai contoh, mencari produk dengan harga di antara Rp50.000 hingga Rp100.000).

```php
use Kinetics\Filters\NumberFilter;

NumberFilter::make('price')->range()
```

Ketika `range()` diaktifkan:
1. Antarmuka *frontend* akan berubah menjadi dua input angka sekaligus (Minimum dan Maksimum).
2. Operatornya akan dikunci secara paksa menjadi `between` (di antara).
3. *Query* SQL yang dihasilkan akan menggunakan logika `WHERE ... BETWEEN`.
