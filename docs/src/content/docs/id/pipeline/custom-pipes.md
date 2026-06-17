---
title: Custom Pipes
description: Membuat dan menyisipkan pipe Anda sendiri ke dalam pipeline tabel.
---

Arsitektur Kinetics memungkinkan Anda untuk membuat tahapan proses (*pipe*) Anda sendiri dan menyisipkannya ke dalam alur *query builder*.

## Kapan Membutuhkan Custom Pipes?

Anda mungkin membutuhkan *custom pipes* ketika:
- Anda ingin mengeksekusi logika *query* kompleks yang tidak ditangani oleh Search, Sort, atau Filter bawaan.
- Anda perlu menyuntikkan *scopes* spesifik berdasarkan peran pengguna (misalnya *tenant isolation*).
- Anda perlu menghitung aggregate sementara.

## Membuat Custom Pipe

Anda dapat men-generate class *pipe* baru dengan mudah menggunakan perintah *artisan* yang disediakan oleh Kinetics:

```bash
php artisan kinetics:pipe ActiveUsersOnlyPipe
```

Perintah tersebut akan membuat sebuah class baru di direktori `app/Pipes`. Secara alternatif, Anda juga bisa menulis class-nya secara manual. Karena arsitekturnya memanfaatkan `Illuminate\Pipeline\Pipeline` bawaan Laravel, class tersebut hanya membutuhkan satu metode `handle`:

```php
namespace App\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class ActiveUsersOnlyPipe
{
    public function handle(Builder $query, Closure $next)
    {
        // Modifikasi query untuk hanya menampilkan user yang aktif
        $query->where('is_active', true);
        
        // Lanjutkan ke pipe berikutnya dalam rantai
        return $next($query);
    }
}
```

## Menyisipkan Pipa ke dalam `Table`

Untuk menyisipkan pipa kustom Anda ke dalam alur eksekusi *query*, masukkan pipa-pipa tersebut ke dalam metode `pipes()` pada instance `Table` Anda. Pipa-pipa tersebut akan berjalan beriringan dengan pipa-pipa bawaan Kinetics.

```php
use Kinetics\Table;
use App\Pipes\ActiveUsersOnlyPipe;

$table = Table::model(User::class)
    ->pipes([
        new ActiveUsersOnlyPipe(),
    ])
    ->columns([
        // ...
    ])
    ->make();
```
