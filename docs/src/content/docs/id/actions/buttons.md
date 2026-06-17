---
title: Action Buttons
description: Mengonfigurasi tombol aksi individual pada baris tabel.
---

`Action` adalah komponen dasar untuk membuat tombol per-baris. Aksi ini dirender sebagai tombol interaktif (atau link Anchor yang berbentuk tombol) di kolom paling kanan dari tabel.

## Penggunaan Dasar

Setiap aksi membutuhkan nama unik (sebagai *identifier*) dan biasanya sebuah URL target. Anda mendaftarkan *row actions* ini di dalam sebuah `ActionColumn`.

```php
use Kinetics\Columns\ActionColumn;
use Kinetics\Actions\Action;

ActionColumn::make()
    ->actions([
        Action::make('publish')
            ->label('Publikasikan')
            ->href(fn ($record) => route('posts.publish', $record))
    ])
```

*Closure* pada metode `href()` atau `href()` akan menerima model Eloquent untuk baris saat ini (dalam contoh di atas, `$record`), sehingga Anda dapat mengekstrak ID atau *slug*-nya untuk menghasilkan URL yang dinamis.

## Preset Actions (Jalan Pintas)

Kinetics menyediakan metode statis bawaan untuk tiga aksi yang paling sering digunakan: `view()`, `edit()`, dan `delete()`. *Preset* ini secara otomatis mengonfigurasi label, ikon, varian warna, dan bahkan dialog konfirmasi (untuk *delete*) yang sesuai.

Terlebih lagi, Anda cukup mengirimkan **Nama Route Laravel** sebagai *string*, dan Kinetics akan secara cerdas menyisipkan ID baris saat ini ke dalam parameter *route* tersebut secara otomatis!

```php
ActionColumn::make()
    ->actions([
        Action::view('users.show'),
        // Secara otomatis sama dengan:
        // Action::make('view')->label('View')->icon('eye')->variant('outline')->href(fn($r) => route('users.show', $r['id']))
        
        Action::edit('users.edit'),
        
        Action::delete('users.destroy'),
        // Secara otomatis menambahkan ikon "trash", warna merah (destructive), 
        // HTTP method 'delete', dan peringatan dialog konfirmasi!
    ])
```

## Visibilitas Dinamis (Kondisional)

Salah satu fitur paling kuat dari Action di Kinetics adalah kemampuannya untuk ditampilkan atau disembunyikan berdasarkan kondisi data pada baris tersebut.

### Menyembunyikan Aksi

Gunakan metode `visibleWhen()` dengan mengirimkan sebuah *closure function* yang mengembalikan nilai *boolean* (`true` untuk memunculkan).

```php
Action::make('publish')
    ->label('Terbitkan')
    ->href(fn ($post) => route('posts.publish', $post))
    ->visibleWhen(fn ($post) => $post->status !== 'published')
```
*Pada contoh di atas, tombol "Terbitkan" hanya akan muncul pada artikel yang belum diterbitkan.*

### Menonaktifkan Aksi

Berbeda dengan `visibleWhen()`, metode `disabledWhen()` akan tetap menampilkan tombol tersebut, namun membuatnya tidak dapat diklik (berwarna keabu-abuan).

```php
Action::make('delete')
    ->label('Hapus')
    ->href(fn ($post) => route('posts.destroy', $post))
    ->disabledWhen(fn ($post) => $post->is_locked)
```

## Kustomisasi Tampilan

Meskipun logikanya berada di *backend*, Anda dapat mengirimkan metadata spesifik seperti ikon untuk dirender oleh *frontend*.

```php
Action::make('edit')
    ->label('Edit')
    ->href(fn ($post) => route('posts.edit', $post))
    ->icon('pencil') // Menginstruksikan frontend untuk menampilkan ikon pensil
```

## Interaktivitas Lanjutan

### Dialog Konfirmasi (`confirm`)

Butuh memberi peringatan kepada pengguna sebelum mereka mengeksekusi sebuah aksi? Gunakan `confirm()`. Metode ini akan secara otomatis memunculkan peringatan dialog di *frontend* sebelum klik diproses.

```php
Action::make('ban_user')
    ->label('Blokir')
    ->variant('destructive')
    ->href(fn($row) => route('users.ban', $row['id']))
    ->confirm('Apakah Anda yakin?', 'Pengguna ini akan kehilangan semua akses secara instan.')
```

### Metode HTTP (`method`)

Jika aksi Anda memicu perubahan _state_ (seperti menghapus atau memperbarui data), Anda harus mengubah metode HTTP-nya. *Frontend* akan secara otomatis mengeksekusi *request* non-GET.

```php
Action::delete('users.destroy')->method('delete')
```

### Membuka di dalam Modal (`modal`)

Jika *href* Anda mengarah ke rute Inertia yang seharusnya di-render di dalam *popup* modal (alih-alih berpindah halaman secara utuh), cukup tambahkan metode `modal()`!

```php
Action::make('quick_edit')
    ->label('Edit Cepat')
    ->href(fn($row) => route('users.edit', $row['id']))
    ->modal()
```
