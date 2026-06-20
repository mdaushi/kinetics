---
title: Toolbar Action Groups
description: Mengelompokkan beberapa Toolbar Actions ke dalam menu dropdown.
---

Ketika Anda memiliki terlalu banyak aksi yang tersedia di tingkat tabel, antarmuka pengguna (UI) Anda dapat dengan cepat menjadi berantakan. Kinetics memecahkan masalah ini dengan menyediakan **Action Groups**, yang memungkinkan Anda untuk menggabungkan beberapa `ToolbarAction` ke dalam satu menu *dropdown* yang rapi.

Sebuah `ToolbarActionGroup` membungkus beberapa komponen `ToolbarAction` menjadi satu tombol pemicu *dropdown* yang dirender di *toolbar* bagian atas tabel.

## Penggunaan Dasar

Anda memberikan array berupa instansiasi `ToolbarAction` ke dalam metode `actions()` milik grup tersebut.

```php
use Kinetics\Actions\ToolbarActionGroup;
use Kinetics\Actions\ToolbarAction;

Table::model(User::class)
    ->actions([
        ToolbarActionGroup::make('export_options')
            ->label('Ekspor Data')
            ->icon('download')
            ->actions([
                ToolbarAction::make('csv')
                    ->label('Ekspor ke CSV')
                    ->href(route('export.csv')),
                    
                ToolbarAction::make('pdf')
                    ->label('Ekspor ke PDF')
                    ->href(route('export.pdf')),
            ])
    ])
```

## Menyesuaikan Tombol Pemicu

*Class* grup mewarisi fungsi dari `BaseActionGroup`, yang berarti tombol pemicu *dropdown* itu sendiri juga mendukung visibilitas dinamis dan mode tampilan responsif!

### Mode Tampilan

Untuk lebih menghemat ruang, Anda dapat menyembunyikan teks pada tombol pemicu *dropdown* sehingga hanya ikonnya saja yang terlihat (gaya klasik menu "kebab" atau "meatball").

```php
ToolbarActionGroup::make('more')
    ->icon('more-vertical')
    ->iconOnly()
    ->actions([...])
```

### Visibilitas Dinamis

Anda dapat sepenuhnya menyembunyikan keseluruhan grup *dropdown* ini jika pengguna tidak memiliki izin untuk melihat satupun aksi di dalamnya.

```php
ToolbarActionGroup::make('danger_zone')
    ->visibleWhen(fn () => auth()->user()->isSuperAdmin())
    ->actions([...])
```

---

## Referensi API

| Metode | Deskripsi |
|--------|-----------|
| `make(string $key)` | Membuat instansiasi toolbar action group baru. |
| `actions(array $actions)` | Mendaftarkan anak-anak objek `ToolbarAction`. |
| `label(string $label)` | Menetapkan label tombol pemicu dropdown. |
| `icon(string $icon)` | Menetapkan ikon tombol pemicu dropdown. |
| `variant(ActionVariant\|string $variant)`| Menetapkan varian warna tombol pemicu. |
| `iconOnly(bool $value = true)` | Hanya menampilkan ikon untuk tombol pemicu. |
| `textOnly(bool $value = true)` | Hanya menampilkan teks untuk tombol pemicu. |
| `iconOnlyOnMobile(bool $value = true)` | Menyembunyikan teks pada layar perangkat seluler. |
| `visibleWhen(bool\|\Closure $condition)`| Menampilkan atau menyembunyikan seluruh grup secara dinamis. |
| `disabledWhen(bool\|\Closure $condition)`| Menonaktifkan tombol pemicu secara dinamis. |
| `meta(array $meta)` | Mengirimkan metadata arbitrer ke *frontend*. |