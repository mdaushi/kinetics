---
title: Actions Overview
description: Pengenalan komponen Action dan Global Actions di Kinetics.
---

Aksi (*Actions*) adalah tombol interaktif yang memungkinkan pengguna melakukan operasi, seperti pindah ke halaman pembuatan data, mengedit baris, atau menghapus data. Di Kinetics, aksi sepenuhnya didefinisikan dan dikendalikan dari server (Laravel).

Komponen inti yang mengendalikan semua ini adalah class `Action`.

## Di mana meletakkan Actions?

Ada dua penempatan berbeda untuk Aksi di tabel Kinetics:

### 1. Global Actions (Aksi Jenderal/Umum)
*Global actions* adalah tombol yang berlaku untuk keseluruhan tabel, bukan untuk baris spesifik. Ini biasanya digunakan untuk tombol "Create New", "Export", dll, dan di-render di bagian atas tabel di dalam **Toolbar**.

Anda mendaftarkan *global actions* dengan memanggil metode `actions()` langsung pada instance `Table`.

```php
use Kinetics\Table;
use Kinetics\Actions\Action;

$table = Table::model(User::class)
    ->actions([
        // Ini adalah Global Action
        Action::make('create')
            ->label('Tambah User')
            ->href(route('users.create'))
    ])
    ->columns([
        // ... definisi kolom
    ])
    ->make();
```

### 2. Row Actions (Action Column)
*Row actions* adalah tombol yang berlaku untuk *record* (baris) spesifik, seperti "Edit" atau "Hapus". Untuk menambahkan aksi ini, Anda harus meletakkan komponen `Action` di dalam sebuah `ActionColumn`.

*(Untuk mempelajari cara mengatur wadah bagi aksi baris ini, silakan baca dokumentasi [Action Column](/id/columns/action)).*

## Mengonfigurasi Actions

Baik Anda meletakkan Action secara global di *toolbar*, maupun spesifik pada baris di dalam `ActionColumn`, cara Anda mengonfigurasi komponen `Action` tetap sama persis!

Pada halaman-halaman berikutnya, kita akan membahas semua fitur luar biasa dari class `Action`, seperti *shortcut* bawaan, visibilitas dinamis, dan grup *dropdown*.
