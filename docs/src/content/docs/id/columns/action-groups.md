---
title: Action Groups
description: Mengelompokkan beberapa aksi ke dalam sebuah dropdown menu.
---

Ketika tabel Anda memiliki terlalu banyak aksi untuk satu baris (misalnya: Lihat, Edit, Hapus, Duplikat, Arsipkan), menampilkannya sejajar akan membuat tabel terlihat penuh dan merusak *layout*. `ActionGroup` memecahkan masalah ini dengan menggabungkan aksi-aksi tersebut ke dalam sebuah *Dropdown Menu*.

## Penggunaan

Gunakan `ActionGroup::make()` untuk mengatur labelnya (secara bawaan 'Actions'), lalu berikan sebuah *array* berisi objek-objek `Action` ke dalam metode `actions()`.

```php
use Kinetics\Actions\ActionGroup;
use Kinetics\Actions\Action;

ActionGroup::make('Opsi Lainnya')
    ->actions([
        Action::make('view')
            ->label('Lihat')
            ->variant('default')
            ->href(fn ($record) => route('users.show', $record)),
            
        Action::make('edit')
            ->label('Edit')
            ->variant('default')
            ->href(fn ($record) => route('users.edit', $record)),
            
        Action::delete()
            ->label('Hapus') // Menimpa label default
            ->href(fn ($record) => route('users.destroy', $record))
    ])
```

## Varian yang Diizinkan (Allowed Variants)

Secara bawaan (*default*), sebuah `Action` yang dibuat menggunakan `Action::make()` akan memiliki varian `outline`. Namun, `ActionGroup` me-render anak-anaknya di dalam menu dropdown. Karena batasan antarmuka (*UI constraints*) ini, **sebuah `ActionGroup` secara ketat mengharuskan setiap `Action` di dalamnya untuk memiliki varian `default` atau `destructive`**. Varian selain itu akan memicu *error* berupa `InvalidArgumentException`.

Jika Anda menggunakan *preset action* seperti `Action::delete()`, variannya sudah otomatis diatur menjadi `destructive`. Tetapi jika Anda menggunakan `Action::make()`, selalu ingat untuk secara eksplisit mengatur variannya:

```php
Action::make('duplicate')
    ->label('Duplikat')
    ->variant('default') // Wajib di dalam ActionGroup
```

## Hasil di Frontend

Kinetics akan mengonversi `ActionGroup` di atas menjadi komponen Dropdown (memanfaatkan komponen *DropdownMenu* dari `shadcn/ui`). Pengguna akan melihat tombol dengan ikon titik tiga (*ellipsis* atau *more*), dan saat diklik, opsi-opsi yang didaftarkan akan muncul dalam menu *popover*.

## Evaluasi Kondisional di dalam Grup

Jika Anda menggunakan fungsi kondisional seperti `visibleWhen()` pada salah satu `Action` di dalam `ActionGroup`, Kinetics akan tetap mengevaluasinya dengan benar.

Jika pengguna tidak memiliki akses untuk "Menghapus" sebuah baris (karena evaluasi `visibleWhen()` mengembalikan `false`), maka hanya opsi "Lihat" dan "Edit" yang akan muncul di *dropdown*.

```php
ActionGroup::make('Lainnya')
    ->actions([
        // ...
        Action::delete()
            ->label('Hapus')
            ->href(fn ($record) => route('users.destroy', $record))
            // Opsi hapus akan hilang dari dropdown jika baris data ini terkunci
            ->visibleWhen(fn ($record) => !$record->is_locked) 
    ])
```

## Referensi API

| Metode | Deskripsi |
|--------|-------------|
| `make(string $key)` | Creates a new action group instance. |
| `label(string $label)` | Sets the dropdown trigger label. |
| `icon(string $icon)` | Sets the dropdown trigger icon. |
| `actions(array $actions)` | Registers the child Action objects inside the dropdown. |
| `variant(ActionVariant\|string $variant)`| Sets the trigger button variant. |
| `iconOnly(bool $value = true)` | Shows only the icon for the trigger button. |
| `textOnly(bool $value = true)` | Shows only the text for the trigger button. |
| `iconOnlyOnMobile(bool $value = true)` | Collapses text on mobile screens. |
