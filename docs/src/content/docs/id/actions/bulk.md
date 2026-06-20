---
title: Bulk Actions
description: Operasi pada beberapa baris yang dipilih sekaligus.
---

*Bulk Actions* memungkinkan pengguna untuk memilih beberapa baris sekaligus di tabel melalui kotak centang (*checkboxes*) dan melakukan sebuah operasi massal (batch operation) terhadap baris-baris tersebut.

Saat sebuah aksi massal dieksekusi, Kinetics akan mengirimkan *payload* ke server yang berisi:
- `ids` (array string/integer): *Primary keys* dari baris-baris yang dipilih.
- `select_all` (boolean): `true` jika pengguna menekan tombol untuk memilih seluruh rekaman data melintasi semua halaman.

## Penggunaan Dasar

Gunakan *class* `BulkAction`.

```php
use Kinetics\Actions\BulkAction;

Table::model(User::class)
    ->bulkActions([
        BulkAction::make('archive')
            ->label('Arsipkan Pilihan')
            ->icon('archive')
            ->href(route('users.bulk-archive'))
            ->method('post')
    ])
```

## Posisi Tampilan (UI Positions)

Secara bawaan (*default*), *Bulk Actions* muncul dalam sebuah panel melayang (**floating**) di bagian bawah-tengah layar setiap kali ada satu baris atau lebih yang dicentang.

Namun, Anda juga dapat mengaturnya agar muncul langsung di dalam **toolbar** atas tabel dengan menggunakan metode `->toolbar()`:

```php
BulkAction::export(route('users.bulk-export'))->toolbar()
```
*Catatan: Saat diposisikan di dalam toolbar, bulk actions akan menggantikan tombol Toolbar Actions reguler selama ada baris yang dicentang.*

## Preset Actions (Jalan Pintas)

Kinetics menyertakan *preset* statis bawaan untuk operasi-operasi massal yang umum. *Preset* ini secara otomatis menetapkan ikon, label, warna (varian), dan dialog konfirmasi (bila diperlukan) yang sesuai:

```php
BulkAction::delete(route('users.bulk-delete'));
BulkAction::export(route('users.bulk-export'));
BulkAction::archive(route('users.bulk-archive'));
BulkAction::restore(route('users.bulk-restore'));
```

## Visibilitas Dinamis (Kondisional)

Sama seperti aksi biasa, Anda dapat membatasi kapan sebuah operasi massal (*bulk action*) tersedia untuk digunakan.

```php
BulkAction::delete('users.bulk-delete')
    ->visibleWhen(fn () => auth()->user()->isAdmin())
```

## Kustomisasi Tampilan

Anda dapat mengubah varian warna dan ikon dari *bulk action*. Perlu dicatat bahwa *bulk action* model melayang (*floating*) secara *default* akan bersifat `iconOnly` untuk menghemat ruang, namun Anda bebas menimpanya.

```php
BulkAction::make('mark_paid')
    ->label('Tandai Dibayar')
    ->icon('check-circle')
    ->variant('default')
    ->textOnly() // Memaksa agar teks tetap ditampilkan meskipun di dalam floating bar
```

## Interaktivitas Lanjutan

### Dialog Konfirmasi (`confirm`)

Operasi massal yang bersifat destruktif harus selalu membutuhkan konfirmasi. *Preset* `BulkAction::delete()` sudah menyertakannya secara otomatis, namun Anda juga bisa menambahkannya ke aksi kustom apa pun:

```php
BulkAction::make('ban')
    ->label('Blokir Pengguna')
    ->variant('destructive')
    ->href(route('users.bulk-ban'))
    ->method('post')
    ->confirm('Blokir pengguna terpilih?', 'Mereka tidak akan dapat login lagi.')
```

### Metode HTTP (`method`)

Karena *bulk actions* pada umumnya mengubah (*mutate*) data, mereka seharusnya hampir selalu menggunakan *method* HTTP `post`, `put`, atau `delete`.

```php
BulkAction::make('approve')->method('post')
```

## Referensi API

| Metode | Deskripsi |
|--------|-------------|
| `make(string $key)` | Creates a new bulk action instance. |
| `delete(?string $route)` | **[Preset]** Creates a 'Delete' button with trash icon and confirmation dialog. |
| `export(?string $route)` | **[Preset]** Creates an 'Export' button. |
| `archive(?string $route)`| **[Preset]** Creates an 'Archive' button with confirmation dialog. |
| `restore(?string $route)`| **[Preset]** Creates a 'Restore' button. |
| `label(string $label)` | Sets the button label. |
| `icon(string $icon)` | Sets the button icon. |
| `variant(ActionVariant\|string $variant)` | Sets the button variant (e.g., default, outline, destructive). |
| `href(string\|\Closure $href)` | Sets the target URL or route name. |
| `method(string $method)`| Sets the HTTP method (get, post, put, delete, etc.). |
| `confirm(...)` | Attaches a confirmation dialog to the action. |
| `visibleWhen(bool\|\Closure $condition)`| Dynamically shows or hides the action. |
| `disabledWhen(bool\|\Closure $condition)`| Dynamically enables or disables the action. |
| `position(string $position)`| Defines where the action appears (`toolbar` or `floating`). |
| `toolbar()`| Short method to place the action in the toolbar instead of a floating bar. |
| `floating()`| Short method to explicitly place the action in a floating bar. |
| `iconOnly(bool $value = true)` | Hides the text and only shows the icon. |
| `textOnly(bool $value = true)` | Hides the icon and only shows the text. |
| `iconOnlyOnMobile(bool $value = true)` | Collapses text on mobile screens. |
| `meta(array $meta)` | Sends arbitrary metadata to the frontend. |
