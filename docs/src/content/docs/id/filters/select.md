---
title: Select Filter
description: Menggunakan komponen SelectFilter.
---

`SelectFilter` merender menu *dropdown* (input *select*) di antarmuka depan. Ini sangat ideal untuk kolom-kolom yang nilainya sudah pasti, seperti "status" atau "hak akses" (role).

## Penggunaan Dasar

Anda mendefinisikan sebuah `SelectFilter` dan mengirimkan sebuah *array* pilihan ke dalam metode `options()`.

```php
use Kinetics\Filters\SelectFilter;

SelectFilter::make('status')
    ->options([
        'active' => 'Aktif',
        'inactive' => 'Non-aktif',
        'banned' => 'Diblokir',
    ])
```

## Format Opsi (Options)

Metode `options()` ini sangat fleksibel dan dapat menerima berbagai bentuk struktur *array*:

### 1. Associative Array
Memetakan nilai asli dari database (*key*) ke label yang akan muncul di UI (*value*).
```php
->options([
    1 => 'Sudah Terbit',
    0 => 'Draf'
])
```

### 2. Simple List
Jika nilainya dan labelnya sama persis, Anda bisa memberikan *array* rata (*flat array*).
```php
->options(['Admin', 'Editor', 'User'])
```

### 3. Formatted Array
Anda juga bisa melempar *array* asosiatif yang memuat parameter baku `value` dan `label`, yang mana bentuk ini adalah format murni yang digunakan oleh TanStack Table.
```php
->options([
    ['value' => 'active', 'label' => 'Akun Aktif'],
    ['value' => 'banned', 'label' => 'Akun Diblokir'],
])
```

## Operator

Secara logis, `SelectFilter` akan otomatis membatasi operator pencariannya hanya pada perintah `is` (harus sama persis) dan `is_not` (kecualikan nilai ini).
