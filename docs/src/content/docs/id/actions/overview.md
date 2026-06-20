---
title: Table Actions Overview
description: Pengenalan aksi pada tingkat tabel (Toolbar dan Bulk actions) di Kinetics.
---

Sementara aksi tingkat baris (seperti Edit atau Hapus untuk sebuah data spesifik) ditangani secara individual di dalam `ActionColumn`, Kinetics juga menyediakan **aksi tingkat tabel** (table-level actions) yang sangat kuat. Ini adalah aksi-aksi yang berlaku untuk keseluruhan tabel secara kolektif, atau untuk beberapa baris yang dipilih sekaligus.

Pada bagian ini, kita akan membahas dua jenis aksi tingkat tabel yang disediakan oleh Kinetics:

## 1. Toolbar Actions

Tombol global yang berlaku untuk seluruh halaman atau kumpulan data, seperti "Buat Baru" atau "Impor CSV". Aksi ini tidak bergantung pada seleksi baris dan dirender secara konstan di bagian atas tabel di dalam **Toolbar**.

Anda mendaftarkan *toolbar actions* dengan memanggil metode `actions()` langsung pada *instance* `Table` Anda.

```php
use Kinetics\Table;
use Kinetics\Actions\ToolbarAction;

$table = Table::model(User::class)
    ->bulkActions([
        ToolbarAction::make('create')
            ->label('Buat User')
            ->href(route('users.create'))
    ])
    ->columns([
        // ...
    ])
    ->make();
```

Untuk mempelajari lebih lanjut tentang konfigurasi dan jalan pintas (*presets*), baca dokumentasi [Toolbar Actions](/id/actions/toolbar).

## 2. Bulk Actions

Operasi massal yang berlaku untuk beberapa baris yang dipilih sekaligus, seperti "Hapus yang Dipilih" atau "Ekspor yang Dipilih". Aksi ini otomatis muncul di dalam bilah melayang (*floating bar*) atau di dalam *toolbar* setiap kali pengguna mencentang kotak pilihan (*checkbox*) di tabel.

*Bulk actions* juga didaftarkan di dalam metode `actions()` yang sama pada *instance* `Table`.

```php
use Kinetics\Table;
use Kinetics\Actions\BulkAction;

$table = Table::model(User::class)
    ->bulkActions([
        BulkAction::delete('users.bulk-delete')
    ])
    ->columns([
        // ...
    ])
    ->make();
```

Untuk mempelajari lebih lanjut tentang pemrosesan *payload* dan status seleksi, baca dokumentasi [Bulk Actions](/id/actions/bulk).

---
> [!NOTE]
> Mencari aksi yang spesifik untuk baris (seperti tombol "Edit" individual di sebelah suatu rekaman data)? Silakan merujuk ke dokumentasi [Action Column](/id/columns/action).
