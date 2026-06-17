---
title: Custom Operators
description: Cara membatasi operator filter atau membuat custom operator SQL Anda sendiri.
---

Secara bawaan, setiap tipe filter di Kinetics hadir dengan sekumpulan operator logika (*seperti* `equals`, `contains`, `>`, dll). Namun, Kinetics memberikan Anda kendali penuh untuk memodifikasinya atau bahkan membuat operator baru secara mandiri.

## 1. Membatasi atau Mengubah Nama Operator

Terkadang Anda tidak ingin pengguna melihat semua pilihan operator yang ada. Anda dapat membatasi daftar operator untuk satu filter spesifik menggunakan metode `operators()`.

```php
use Kinetics\Filters\TextFilter;

TextFilter::make('email')
    // Hanya izinkan dua operator ini
    ->operators(['equals', 'contains'])
```

Anda juga dapat mengirimkan *associative array* untuk mengubah label teks operator yang akan muncul di *frontend*!

```php
TextFilter::make('email')
    ->operators([
        'equals' => 'Sama Persis',
        'contains' => 'Mengandung Kata'
    ])
```

## 2. Membuat Custom Operator Global

Jika operator bawaan (`equals`, `in`, `between`, dll) tidak cukup untuk kebutuhan SQL spesifik Anda, Anda dapat mendaftarkan operator global yang sepenuhnya baru.

Caranya adalah dengan memanggil metode statis `resolveOperator()` pada class dasar `Filter`, yang biasanya diletakkan di dalam metode `boot()` pada `AppServiceProvider` Anda.

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Kinetics\Filters\Filter;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Mendaftarkan custom operator bernama 'json_contains'
        Filter::resolveOperator('json_contains', function (Builder $query, string $column, mixed $value) {
            $query->whereJsonContains($column, $value);
        });
    }
}
```

Setelah didaftarkan secara global, Anda dapat langsung menggunakan operator kustom tersebut di filter mana pun dalam aplikasi Anda:

```php
TextFilter::make('settings')
    ->operators([
        'json_contains' => 'Memiliki Pengaturan',
        'equals' => 'Pengaturan Persis'
    ])
```
