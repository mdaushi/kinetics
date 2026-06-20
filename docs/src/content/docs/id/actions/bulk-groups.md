---
title: Bulk Action Groups
description: Mengelompokkan beberapa Bulk Actions ke dalam menu dropdown.
---

Ketika Anda memiliki terlalu banyak aksi massal yang tersedia, *floating action bar* Anda dapat dengan cepat menjadi berantakan. Kinetics memecahkan masalah ini dengan menyediakan **Action Groups**, yang memungkinkan Anda untuk menggabungkan beberapa `BulkAction` ke dalam satu menu *dropdown* yang rapi.

Sebuah `BulkActionGroup` membungkus beberapa komponen `BulkAction` menjadi satu tombol pemicu *dropdown* yang dirender di dalam *floating action bar* (atau *toolbar*) saat ada baris yang dipilih/dicentang.

## Penggunaan Dasar

Anda memberikan array berupa instansiasi `BulkAction` ke dalam metode `actions()` milik grup tersebut.

```php
use Kinetics\Actions\BulkActionGroup;
use Kinetics\Actions\BulkAction;

Table::model(User::class)
    ->bulkActions([
        BulkActionGroup::make('more_options')
            ->label('Lainnya')
            ->icon('ellipsis-horizontal')
            ->bulkActions([
                BulkAction::archive(route('users.archive')),
                BulkAction::restore(route('users.restore')),
                BulkAction::make('ban')
                    ->label('Blokir Pilihan')
                    ->variant('destructive')
                    ->method('post'),
            ])
    ])
```

## Posisi Tampilan (UI Positions)

Sama seperti aksi massal individual, `BulkActionGroup` secara bawaan akan tampil mengambang (`floating`). Anda dapat mengubahnya menjadi `toolbar` jika Anda lebih suka *dropdown* tersebut muncul di *toolbar* atas saat baris dipilih.

```php
BulkActionGroup::make('more')->toolbar()->bulkActions([...])
```

## Menyesuaikan Tombol Pemicu

*Class* grup mewarisi fungsi dari `BaseActionGroup`, yang berarti tombol pemicu *dropdown* itu sendiri juga mendukung visibilitas dinamis dan mode tampilan responsif!

### Mode Tampilan

Untuk lebih menghemat ruang, Anda dapat menyembunyikan teks pada tombol pemicu *dropdown* sehingga hanya ikonnya saja yang terlihat (gaya klasik menu "kebab" atau "meatball").

```php
BulkActionGroup::make('more')
    ->icon('more-vertical')
    ->iconOnly()
    ->bulkActions([...])
```

### Visibilitas Dinamis

Anda dapat sepenuhnya menyembunyikan keseluruhan grup *dropdown* ini jika pengguna tidak memiliki izin untuk melihat satupun aksi di dalamnya.

```php
BulkActionGroup::make('danger_zone')
    ->visibleWhen(fn () => auth()->user()->isSuperAdmin())
    ->bulkActions([...])
```

---

## Referensi API

| Metode | Deskripsi |
|--------|-----------|
| `make(string $key)` | Membuat instansiasi bulk action group baru. |
| `actions(array $actions)` | Mendaftarkan anak-anak objek `BulkAction`. |
| `label(string $label)` | Menetapkan label tombol pemicu dropdown. |
| `icon(string $icon)` | Menetapkan ikon tombol pemicu dropdown. |
| `variant(ActionVariant|string $variant)`| Menetapkan varian warna tombol pemicu. |
| `position(string $position)`| Menentukan lokasi grup dirender (`toolbar` atau `floating`). |
| `toolbar()`| Menempatkan grup di dalam *toolbar*. |
| `floating()`| Menempatkan grup di panel mengambang (*floating*). |
| `iconOnly(bool $value = true)` | Hanya menampilkan ikon untuk tombol pemicu. |
| `textOnly(bool $value = true)` | Hanya menampilkan teks untuk tombol pemicu. |
| `iconOnlyOnMobile(bool $value = true)` | Menyembunyikan teks pada layar perangkat seluler. |
| `visibleWhen(bool\|\Closure $condition)`| Menampilkan atau menyembunyikan seluruh grup secara dinamis. |
| `disabledWhen(bool\|\Closure $condition)`| Menonaktifkan tombol pemicu secara dinamis. |
| `meta(array $meta)` | Mengirimkan metadata arbitrer ke *frontend*. |