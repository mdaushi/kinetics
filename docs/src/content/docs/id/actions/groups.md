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
            ->href(fn ($record) => route('users.show', $record)),
            
        Action::make('edit')
            ->label('Edit')
            ->href(fn ($record) => route('users.edit', $record)),
            
        Action::make('delete')
            ->label('Hapus')
            ->href(fn ($record) => route('users.destroy', $record))
    ])
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
        Action::make('delete')
            ->label('Hapus')
            ->href(fn ($record) => route('users.destroy', $record))
            // Opsi hapus akan hilang dari dropdown jika baris data ini terkunci
            ->visibleWhen(fn ($record) => !$record->is_locked) 
    ])
```
