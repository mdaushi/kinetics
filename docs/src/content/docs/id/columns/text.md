---
title: Text Column
description: Menampilkan dan memformat data teks, tanggal, dan badge pada tabel.
---

`TextColumn` adalah tipe kolom yang paling serbaguna dan sering digunakan di Kinetics. Kolom ini digunakan untuk menampilkan string, angka, tanggal, atau bahkan mengubah teks menjadi *badge* visual.

## Penggunaan Dasar

Gunakan metode statis `make()` dengan nama atribut dari database Anda.

```php
use Kinetics\Columns\TextColumn;

TextColumn::make('title')
```

## Relasi

Anda bisa mengakses properti dari relasi Eloquent menggunakan *dot notation*. Misalnya, jika model `Post` Anda memiliki relasi `author()`, dan Anda ingin menampilkan nama penulisnya:

```php
TextColumn::make('author.name')->label('Penulis')
```

## Pencarian dan Pengurutan

`TextColumn` adalah kandidat utama untuk fitur pencarian (`searchable()`) dan pengurutan (`sortable()`).

```php
TextColumn::make('title')
    ->sortable()
    ->searchable()
```

---

## Format Tanggal & Waktu

Kinetics menyediakan metode bawaan untuk memformat kolom `created_at`, `updated_at`, atau kolom tanggal lainnya dengan mudah tanpa perlu membuat *closure* manual. Fitur ini secara otomatis menggunakan Carbon di balik layar.

### `date()`
Memformat kolom sebagai tanggal. Format bawaannya adalah `Y-m-d`.

```php
TextColumn::make('created_at')->date('d M Y') // Output: 17 Jun 2026
```

### `time()`
Memformat kolom sebagai waktu. Format bawaannya adalah `H:i:s`.

```php
TextColumn::make('created_at')->time('H:i') // Output: 13:00
```

### `dateTime()`
Memformat kolom sebagai tanggal dan waktu sekaligus. Format bawaannya adalah `Y-m-d H:i:s`.

```php
TextColumn::make('created_at')->dateTime('d M Y H:i') 
```

---

## Badges dan Warna

Alih-alih membuat tipe kolom terpisah untuk status, Anda dapat mengubah `TextColumn` apa pun menjadi komponen *Badge* (Label).

### Mengaktifkan Mode Badge
Gunakan metode `badge()`. Anda juga dapat menyertakan sebuah kondisi *boolean* untuk menentukan apakah kolom tersebut harus dirender sebagai *badge* atau teks biasa.

```php
TextColumn::make('status')
    ->label('Status Pesanan')
    ->badge()
```

### Warna Dinamis

Kekuatan utama dari *badge* terletak pada metode `color()`. Metode ini memungkinkan Anda memberikan warna varian `shadcn/ui` spesifik berdasarkan nilai data pada baris tersebut.

Varian warna yang didukung: `'default'`, `'secondary'`, `'destructive'`, `'outline'`, `'ghost'`, `'link'`.

#### 1. Warna Statis
Menerapkan satu warna untuk semua *badge* di kolom ini.

```php
TextColumn::make('role')
    ->badge()
    ->color('secondary')
```

#### 2. Pemetaan Warna (Value-based Mapping)
Masukkan *array* untuk memetakan nilai kolom tertentu ke warna yang spesifik. Ini sangat sempurna untuk kolom status!

```php
TextColumn::make('status')
    ->badge()
    ->color([
        'published' => 'default',
        'draft'     => 'secondary',
        'deleted'   => 'destructive',
    ])
```

## Referensi API

| Metode | Deskripsi |
|--------|-------------|
| `make(string $key)` | Creates a new column instance. |
| `label(string $label)` | Sets the column header label. |
| `as(string $outputKey)` | Sets an alias for the output key. |
| `sortable(bool $value = true)` | Enables sorting for this column. |
| `searchable(bool $value = true)` | Enables searching for this column. |
| `hidden(bool $value = true)` | Hides the column from the table. |
| `formatUsing(\Closure $cb)`| Customizes the output formatting via closure. |
| `badge(bool $condition = true)`| Renders the column data as a badge. |
| `color(string\|array $color)`| Sets the badge color. |
| `date(string $format)` | Formats the data as a date. |
| `time(string $format)` | Formats the data as time. |
| `dateTime(string $format)` | Formats the data as datetime. |
