---
title: Table Overview
description: Panduan komprehensif untuk class Table, inisialisasi, dan konfigurasinya.
---

*Class* `Table` adalah orkestrator sentral dari Kinetics. Ia bertindak sebagai jembatan antara model Eloquent Anda, saluran pemrosesan data (pipeline), dan komponen React di frontend.

## Inisialisasi

Terdapat dua cara untuk menginisialisasi *instance* `Table` tergantung pada kebutuhan Anda.

### Menggunakan Model

Jika Anda melakukan *query* langsung ke sebuah model, gunakan metode statis `model()`.

```php
use Kinetics\Table;
use App\Models\User;

$table = Table::model(User::class)->make();
```

### Menggunakan Query Builder

Jika Anda sudah memiliki *query* dasar yang kompleks (seperti menggunakan *eager loading*, batasan spesifik, atau *scopes*), Anda dapat melewatkan *instance query builder* secara langsung ke metode statis `query()`.

```php
$query = User::with('company')->where('is_active', true);

$table = Table::query($query)->make();
```

## Mendaftarkan Komponen

*Class* `Table` adalah tempat Anda mendaftarkan komponen visual dari datatable Anda: Columns, Filters, dan Actions.

```php
$table = Table::model(User::class)
    ->columns([
        // Definisikan TextColumn, ActionColumn, dll.
    ])
    ->filters([
        // Definisikan TextFilter, SelectFilter, dll.
    ])
    ->actions([
        // Definisikan instansiasi ToolbarAction
    ])
    ->bulkActions([
        // Definisikan instansiasi BulkAction (aktif saat baris dicentang)
    ])
    ->make();
```

## Konfigurasi Lanjutan

*Class* `Table` menyediakan beberapa metode perantaian (*chaining*) canggih yang memungkinkan Anda mengonfigurasi eksekusi kueri dan perilaku paginasi secara mendalam.

### Injeksi Kueri (`tap`)

Metode `tap()` memungkinkan Anda menerapkan batasan kueri tambahan sebelum *pipeline* (pencarian, pengurutan, filter) dieksekusi. Ini sangat berguna untuk fitur multi-penyewa (*multi-tenancy*).

```php
Table::model(User::class)
    ->tap(function ($query) {
        $query->where('tenant_id', auth()->user()->tenant_id);
    })
    ->make();
```

### Menimpa Request (`withRequest`)

Secara bawaan, Table menggunakan *request* HTTP global. Jika Anda sedang melakukan *testing* atau perlu menyuntikkan *mock request*, gunakan `withRequest()`.

```php
Table::model(User::class)->withRequest($customRequest)->make();
```

### Pengaturan Paginasi (`perPage`)

Anda dapat menimpa batas paginasi bawaan secara spesifik untuk tabel ini menggunakan metode `perPage()`.

```php
// Menetapkan batas bawaan 50 baris per halaman, dengan batas maksimal 500 baris.
Table::model(Post::class)
    ->perPage(default: 50, max: 500)
    ->make();
```

### Pengurutan Bawaan (`defaultSort`)

Tentukan kolom mana yang harus diurutkan secara otomatis saat halaman pertama kali dimuat.

```php
Table::model(Post::class)
    ->defaultSort('created_at', 'desc')
    ->make();
```

### Jeda Permintaan (`debounce`)

Secara *default*, *frontend* Kinetics menunggu 500 milidetik setelah pengguna berhenti mengetik di kotak pencarian atau filter sebelum mengirimkan *request* HTTP. Anda dapat menimpa jeda ini untuk tabel yang lebih berat.

```php
Table::model(Log::class)
    ->debounce(1000) // Tunggu 1 detik
    ->make();
```

### Menyesuaikan Pipeline (`pipes` dan `withoutPipes`)

Kinetics menjalankan kueri Anda melalui serangkaian "pipa" (*Search, Filter, Sort, Paginate*). Anda dapat menyuntikkan *pipe* kustom Anda sendiri, atau menonaktifkan yang bawaan.

```php
use Kinetics\Pipes\SortPipe;
use App\Pipes\MyCustomExportPipe;

Table::model(User::class)
    ->withoutPipes([SortPipe::class]) // Nonaktifkan pengurutan bawaan
    ->pipes([MyCustomExportPipe::class]) // Suntikkan pipe kustom sebelum paginasi
    ->make();
```

## Format Keluaran (`make` vs `get` vs `paginate`)

Di sepanjang dokumentasi ini, kita menggunakan `->make()` yang segera memproses *pipeline* dan mengembalikan *Array* murni. Ini sempurna untuk Inertia.js.

```php
return Inertia::render('Users/Index', [
    'table' => Table::model(User::class)->make()
]);
```

Namun, jika Anda membangun API atau menulis Unit Test, Anda mungkin ingin memeriksa hasil yang dihasilkan. Gunakan metode `get()` untuk mendapatkan objek `TableResult`.

```php
$result = Table::model(User::class)->get();

// Anda sekarang dapat memeriksa status internal
$rawData = $result->getData();
$paginationMeta = $result->getMeta();
$paginator = $result->getPaginator(); // Raw Laravel LengthAwarePaginator

// Anda masih dapat mengembalikannya sebagai JSON secara natif!
return response()->json($result);
```

Jika Anda hanya menginginkan Paginator Laravel mentah dan ingin melewati pemformatan Kinetics sepenuhnya, gunakan `paginate()`:

```php
$paginator = Table::model(User::class)->paginate();
```

---

## Referensi API

| Metode | Deskripsi |
|--------|-----------|
| `model(string $modelClass)` | **[Statis]** Menginisialisasi tabel dari *class* Model Eloquent. |
| `query(Builder $query)` | **[Statis]** Menginisialisasi tabel dari instansiasi Query Builder yang sudah ada. |
| `columns(array $columns)` | Mendaftarkan definisi kolom. |
| `filters(array $filters)` | Mendaftarkan definisi filter. |
| `actions(array $actions)` | Mendaftarkan *toolbar actions* global. |
| `bulkActions(array $actions)` | Mendaftarkan *bulk actions* (aktif ketika baris dicentang). |
| `perPage(int $default, int $max)`| Mengatur batas *item* per halaman bawaan dan maksimal. |
| `debounce(int $ms)` | Menetapkan jeda waktu respons (debounce) frontend. |
| `defaultSort(string $col, string $dir)`| Menetapkan kolom dan arah pengurutan bawaan. |
| `pipes(array $pipes)` | Menyuntikkan *pipe* kustom ke dalam alur pemrosesan kueri. |
| `withoutPipes(array $pipes)` | Menghapus *pipe* bawaan (misal: untuk menonaktifkan fungsi sort bawaan). |
| `tap(\Closure $callback)` | Menerapkan batasan tambahan pada *query builder*. |
| `withRequest(Request $request)`| Menimpa objek *request* HTTP yang digunakan oleh tabel. |
| `make()` | Mengeksekusi *pipeline* dan mengembalikan Array yang sudah diformat (ideal untuk Inertia). |
| `get()` | Mengeksekusi *pipeline* dan mengembalikan objek `TableResult`. |
| `paginate()` | Mengeksekusi *pipeline* dan mengembalikan `LengthAwarePaginator` mentah. |