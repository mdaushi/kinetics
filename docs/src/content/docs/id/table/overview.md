---
title: Table Overview
description: Panduan komprehensif mengenai class Table, inisialisasi, dan konfigurasinya.
---

Class `Table` adalah orkestrator sentral dari Kinetics. Ia bertindak sebagai jembatan yang menghubungkan model Eloquent Anda, saluran pemrosesan data (*pipeline*), dan komponen React di *frontend*.

## Inisialisasi

Ada dua cara untuk menginisialisasi instansiasi `Table` tergantung pada kebutuhan Anda.

### Menggunakan Model

Jika Anda hanya me-*query* model secara langsung, gunakan metode statis `model()`.

```php
use Kinetics\Table;
use App\Models\User;

$table = Table::model(User::class)->make();
```

### Menggunakan Query Builder

Jika Anda sudah memiliki *query builder* dasar yang cukup kompleks (misalnya dengan *eager loading*, atau batasan spesifik), Anda dapat melemparkan instansiasi *query builder* tersebut secara langsung ke metode statis `query()`.

```php
$query = User::with('company')->where('is_active', true);

$table = Table::query($query)->make();
```

## Mendaftarkan Komponen

Class `Table` adalah tempat di mana Anda mendaftarkan ketiga komponen visual utama dari *datatable* Anda: Kolom, Filter, dan Aksi (*Actions*).

```php
$table = Table::model(User::class)
    ->columns([
        // Daftarkan TextColumn, ActionColumn, dll di sini
    ])
    ->filters([
        // Daftarkan TextFilter, SelectFilter, dll di sini
    ])
    ->actions([
        // Daftarkan Aksi tabel global (seperti Export) di sini
    ])
    ->make();
```

## Konfigurasi Tingkat Lanjut

Class `Table` menyediakan banyak metode berantai (*chaining methods*) yang luar biasa kuat untuk mengonfigurasi eksekusi *query* dan perilaku paginasi.

### Injeksi Query (`tap`)

Metode `tap()` memungkinkan Anda menerapkan batasan *query* Eloquent dasar (*scopes*) sebelum *pipeline* (pencarian, *sorting*, filter) dieksekusi. Ini sangat berguna untuk *multi-tenancy*.

```php
Table::model(User::class)
    ->tap(function ($query) {
        $query->where('tenant_id', auth()->user()->tenant_id);
    })
    ->make();
```

### Pengaturan Paginasi (`perPage`)

Anda dapat menimpa batas paginasi spesifik untuk tabel tersebut menggunakan metode `perPage()`. 

```php
// Mengatur pagination default ke 50 baris, 
// dan batas pengambilan maksimal hingga 500 baris per halaman.
Table::model(Post::class)
    ->perPage(default: 50, max: 500)
    ->make();
```

### Pengurutan Bawaan (`defaultSort`)

Atur kolom mana yang akan otomatis diurutkan ketika halaman pertama kali dimuat.

```php
Table::model(Post::class)
    ->defaultSort('created_at', 'desc')
    ->make();
```

### Menunda Request (`debounce`)

Secara bawaan, *frontend* Kinetics akan menunggu 500 milidetik setelah pengguna berhenti mengetik di input pencarian atau filter sebelum menembakkan *request* HTTP. Anda bisa mengubah durasi tundaan ini untuk tabel yang *query*-nya berat.

```php
Table::model(Log::class)
    ->debounce(1000) // Tunggu 1 detik
    ->make();
```

### Mematikan Pipa Bawaan (`withoutPipes`)

Jika Anda ingin mematikan fitur bawaan secara paksa (misalnya Anda menangani *sorting* secara manual), Anda dapat membuang pipa bawaan tersebut.

```php
use Kinetics\Pipes\SortPipe;

Table::model(User::class)
    ->withoutPipes([SortPipe::class])
    ->make();
```

## Kembalian Lanjutan: `get()` vs `make()`

Di sepanjang dokumentasi ini, kita selalu menggunakan `->make()` yang langsung mengeksekusi *pipeline* dan mengembalikan *Array* murni. Ini sangat sempurna untuk dikonsumsi oleh Inertia.js.

Namun, jika Anda sedang membangun API murni atau sedang menulis Unit Test, Anda mungkin butuh menginspeksi hasil jadinya. Gunakan metode `get()` untuk mendapatkan objek `TableResult`.

```php
$result = Table::model(User::class)->get();

// Sekarang Anda bisa menginspeksi state internalnya
$rawData = $result->getData();
$paginationMeta = $result->getMeta();
$paginator = $result->getPaginator(); // Objek LengthAwarePaginator murni Laravel

// Anda tetap bisa mengembalikannya sebagai respons JSON murni!
return response()->json($result);
```
