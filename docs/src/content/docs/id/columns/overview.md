---
title: Columns Overview
description: Pengenalan tentang sistem kolom di Kinetics.
---

Kolom (`Columns`) adalah pondasi utama dalam mendefinisikan apa yang ditampilkan di dalam tabel Anda. Di Kinetics, semua konfigurasi kolom dilakukan secara eksklusif di backend (Laravel).

## Cara Kerja Kolom

Kinetics menggunakan class-class kolom spesifik untuk menentukan bagaimana sebuah data di-render. Ketika Anda mendaftarkan sebuah kolom menggunakan `Table` class, Kinetics akan secara otomatis:

1. Mengekstrak data dari database berdasarkan nama kolom.
2. Menerapkan *formatting* spesifik jika diperlukan (misalnya menampilkan sebagai badge).
3. Memberi sinyal ke *pipeline* apakah kolom tersebut dapat diurutkan (`sortable`) atau dicari (`searchable`).

## Mendefinisikan Kolom

Kolom didefinisikan dengan memanggil metode `columns()` pada instance `Table` class.

```php
use Kinetics\Table;
use Kinetics\Columns\TextColumn;

$table = Table::model(User::class)
    ->columns([
        TextColumn::make('name'),
        TextColumn::make('email'),
    ])
    ->make();
```

## Fitur Global Kolom

Hampir semua tipe kolom di Kinetics mendukung metode berantai (*chaining methods*) berikut ini:

### Mengatur Label Header
Secara default, Kinetics akan membuat label header berdasarkan nama kolom (misalnya `first_name` menjadi "First Name"). Anda bisa menimpanya menggunakan `label()`.

```php
TextColumn::make('name')->label('Nama Lengkap')
```

### Mengaktifkan Sorting
Gunakan metode `sortable()` untuk mengizinkan pengguna mengurutkan tabel berdasarkan kolom ini.

```php
TextColumn::make('created_at')->sortable()
```

### Mengaktifkan Pencarian Global
Gunakan metode `searchable()` agar nilai dari kolom ini ikut dicari ketika pengguna mengetikkan sesuatu di kotak pencarian global.

```php
TextColumn::make('email')->searchable()
```

### Relasi Data (Relationships)

Kinetics menyediakan dua cara untuk mengambil dan menampilkan data dari relasi Eloquent.

#### 1. Dot Notation (Otomatis)
Cara paling mudah adalah dengan menggunakan *dot notation* langsung pada metode `make()`. Kinetics akan otomatis memformat *key* JSON *output* (contoh: `company_name`) dan mengambil relasinya.

```php
// Menampilkan nama dari relasi 'company'
TextColumn::make('company.name')->label('Perusahaan')
```

#### 2. Metode `relation()` (Eksplisit)
Jika Anda ingin kunci JSON *output* berbeda dari jalur relasinya, Anda dapat menggunakan metode `relation()`. Ini berguna ketika Anda ingin menamai kolom secara spesifik untuk *frontend*.

```php
// Frontend akan menerima data ini dengan key 'author_name', 
// namun ia mengambilnya dari relasi 'user' kolom 'name'.
TextColumn::make('author_name')
    ->relation('user', 'name')
    ->label('Penulis')
```

### Pemformatan di Server (`formatUsing`)
Anda dapat mengubah bentuk data sebelum dikirim ke *frontend* menggunakan sebuah *closure*. Fungsi ini menerima tiga argumen: nilai sel, *array* sebaris utuh, dan instansiasi model Eloquent aslinya.

```php
TextColumn::make('price')->formatUsing(function ($value, $row, $model) {
    return 'Rp ' . number_format($value, 0, ',', '.');
})
```

### Membuat Alias Kolom (`as`)
Terkadang Anda perlu mengambil kolom *database* yang sama dua kali namun menampilkannya dengan cara berbeda (misal: satu tanggal mentah, satu tanggal format formatan). Gunakan `as()` untuk mengubah *key* *output* JSON.

```php
TextColumn::make('created_at')->date('Y-m-d'),
TextColumn::make('created_at')
    ->as('created_at_human')
    ->formatUsing(fn($val) => \Carbon\Carbon::parse($val)->diffForHumans()),
```

### Menyembunyikan Kolom (`hidden`)
Jika Anda ingin men-*query* sebuah kolom namun tidak ingin menampilkannya secara *default*, gunakan `hidden()`. Datanya akan tetap dikirim ke *frontend* namun TanStack Table akan menyembunyikan kolom tersebut.

```php
TextColumn::make('secret_id')->hidden()
```

### Menyuntikkan Metadata (`meta`)
Anda dapat mengirimkan data acak kustom apa pun langsung ke komponen tabel *frontend*. Ini sangat berguna untuk mengirimkan *class* CSS atau *flag* format kondisional ke komponen React Anda.

```php
TextColumn::make('status')->meta(['className' => 'text-red-500 font-bold'])
```

Pelajari lebih lanjut tentang tipe-tipe kolom spesifik di halaman berikutnya.
