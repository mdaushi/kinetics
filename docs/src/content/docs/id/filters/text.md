---
title: Text Filter
description: Menggunakan komponen TextFilter.
---

`TextFilter` adalah jenis filter yang paling sederhana. Ia merender sebuah *input* teks standar dan memungkinkan pengguna mencari pencocokan teks persis, pencocokan sebagian, atau menggunakan operator perbandingan dasar.

## Penggunaan Dasar

Berikan nama kolom ke dalam metode statis `make()`.

```php
use Kinetics\Filters\TextFilter;

TextFilter::make('email')
```

## Operator yang Tersedia

Secara bawaan, `TextFilter` mendukung banyak sekali operator *string* dan numerik:
- `equals` (Sama persis)
- `not_equals`
- `contains` (Mengandung kata, setara dengan `LIKE %value%`)
- `starts_with`
- `ends_with`
- `<`, `<=`, `>`, `>=` (Berguna jika *field* teks tersebut mengandung angka atau untuk logika alfabet)

Antarmuka *frontend* akan menyajikan opsi-opsi ini dalam sebuah *dropdown* di sebelah *input* teks, sehingga pengguna bisa mengganti operator pencariannya dengan mudah.
