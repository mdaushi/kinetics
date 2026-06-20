---
title: Action Column
description: Kolom khusus untuk menampung tombol-tombol aksi pada setiap baris.
---

Berbeda dengan kolom biasa yang menampilkan teks atau data dari database Anda, `ActionColumn` adalah kolom khusus yang dirancang eksklusif untuk menampung tombol-tombol interaktif (Aksi) untuk baris tertentu.

## Konsep Dasar

Karena aksi baris (seperti "Edit", "Hapus", atau "Lihat") berlaku untuk satu record spesifik, tombol-tombol tersebut harus di-render sejajar dengan data di dalam tabel. Oleh karena itu, Kinetics memperlakukannya sebagai sebuah kolom.

### Perbedaan dengan Kolom Biasa
`ActionColumn` memiliki beberapa karakteristik unik:
- Secara default, ia tidak memiliki label *header*.
- Ia tidak dapat diurutkan (`sortable()`) atau dicari (`searchable()`).
- Tujuan utamanya hanyalah bertindak sebagai wadah (*container*) untuk komponen `Action` atau `ActionGroup`.

## Penggunaan

Anda meletakkan `ActionColumn` di dalam *array* `columns()` Anda, biasanya di urutan paling akhir agar tombol-tombol tersebut muncul di sisi paling kanan tabel.

```php
use Kinetics\Table;
use Kinetics\Columns\TextColumn;
use Kinetics\Columns\ActionColumn;
use Kinetics\Actions\Action;

$table = Table::model(User::class)
    ->columns([
        TextColumn::make('name'),
        TextColumn::make('email'),
        
        // Wadah Action Column
        ActionColumn::make()
            ->actions([
                // Anda menaruh objek-objek Action Anda di sini
                Action::edit('users.edit'),
                Action::delete('users.destroy'),
            ])
    ])
    ->make();
```

## Menambahkan Aksi

Metode `->actions([...])` pada `ActionColumn` menerima sebuah *array* berisi objek-objek Action. 
Untuk mempelajari cara membuat dan mengonfigurasi objek-objek Action ini (termasuk tombol, warna, ikon, dan grup), silakan baca panduan lengkapnya di bagian [Action Buttons](/id/columns/action-buttons).

## Referensi API

| Metode | Deskripsi |
|--------|-------------|
| `make(string $key = '__actions')` | Creates a new action column instance. |
| `actions(array $actions)` | Registers the action objects for this column. |
| `hidden(bool $value = true)` | Hides the column from the table. |
