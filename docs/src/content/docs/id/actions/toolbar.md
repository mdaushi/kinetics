---
title: Toolbar Actions
description: Aksi global yang ditampilkan di toolbar tabel.
---

*Toolbar Actions* adalah tombol aksi global yang muncul di *toolbar* bagian atas tabel Anda. Aksi ini tidak terikat pada satu baris data tertentu (*row context*) dan biasanya digunakan untuk aksi pada tingkat halaman, seperti membuat data baru, mengimpor data, atau mengekspor keseluruhan tabel.

## Penggunaan Dasar

Gunakan *class* `ToolbarAction`. Anda dapat menetapkan label, ikon, rute, dan aturan visibilitas sama persis seperti cara Anda mengatur aksi baris (*row actions*) biasa.

```php
use Kinetics\Actions\ToolbarAction;

Table::model(User::class)
    ->actions([
        ToolbarAction::make('create')
            ->label('Buat Pengguna')
            ->icon('plus')
            ->href(route('users.create')),

        ToolbarAction::make('import')
            ->label('Impor CSV')
            ->icon('upload')
            ->href(route('users.import'))
            ->method('post'),
    ])
```

## Preset Actions (Jalan Pintas)

Kinetics menyediakan fungsi *preset* statis bawaan untuk *toolbar action* yang paling umum digunakan. *Preset* ini secara otomatis mengatur label dan ikon yang sesuai.

```php
ToolbarAction::create(route('users.create')); 
// Secara otomatis menambahkan ikon 'plus' dan label 'Create'

ToolbarAction::import(route('users.import')); 
// Secara otomatis menambahkan ikon 'upload' dan label 'Import'
```

## Visibilitas Dinamis (Kondisional)

Aksi *toolbar* dapat ditampilkan atau disembunyikan secara dinamis. Karena *toolbar action* tidak berlaku untuk baris tertentu, *closure* yang dikirimkan pada kondisi ini *tidak* menerima data baris (*record*).

### Menyembunyikan Aksi

Gunakan metode `visibleWhen()` untuk memunculkan aksi secara kondisional.

```php
ToolbarAction::make('import')
    ->label('Impor Data')
    ->href(route('users.import'))
    ->visibleWhen(fn () => auth()->user()->can('import_users'))
```

### Menonaktifkan Aksi

Gunakan metode `disabledWhen()` untuk membuat tombol tersebut tidak dapat diklik (berwarna keabu-abuan) alih-alih menyembunyikannya secara penuh.

```php
ToolbarAction::make('sync')
    ->label('Sinkronisasi')
    ->disabledWhen(fn () => Cache::has('sync_in_progress'))
```

## Kustomisasi Tampilan

Anda dapat menyesuaikan tampilan dan nuansa tombol di *toolbar* sepenuhnya.

```php
ToolbarAction::make('create')
    ->label('Dokumen Baru')
    ->icon('file-text')
    ->variant('default') // Pilihan: 'default', 'outline', 'destructive', 'ghost'
```

### Mode Tampilan (Display Modes)

Jika *toolbar* Anda memiliki banyak tombol dan ruangnya terbatas, Anda dapat menyesuaikan mode tampilannya.

```php
ToolbarAction::make('refresh')
    ->icon('refresh-cw')
    ->iconOnly() // Menyembunyikan teks label, hanya menampilkan ikon

ToolbarAction::make('create')
    ->label('Buat Baru')
    ->icon('plus')
    ->iconOnlyOnMobile() // Menampilkan teks di desktop, namun menyusut menjadi khusus ikon di layar kecil
```

## Interaktivitas Lanjutan

### Dialog Konfirmasi (`confirm`)

Jika sebuah aksi melakukan operasi destruktif atau operasi besar (seperti "Hapus Semua"), Anda sebaiknya meminta konfirmasi terlebih dahulu.

```php
ToolbarAction::make('truncate')
    ->label('Hapus Semua Data')
    ->variant('destructive')
    ->href(route('users.truncate'))
    ->method('delete')
    ->confirm('Apakah Anda benar-benar yakin?', 'Ini akan mengosongkan seluruh tabel database.')
```

### Membuka di dalam Modal (`modal`)

Jika Anda ingin *toolbar action* tersebut memunculkan dialog *modal* Inertia alih-alih berpindah halaman secara utuh, Anda dapat memanggil metode `->modal()`.

```php
ToolbarAction::create(route('users.create'))->modal()
```

## Referensi API

| Metode | Deskripsi |
|--------|-------------|
| `make(string $key)` | Creates a new toolbar action instance. |
| `create(?string $route)` | **[Preset]** Creates a 'Create' button with a plus icon. |
| `import(?string $route)` | **[Preset]** Creates an 'Import' button with an upload icon. |
| `label(string $label)` | Sets the button label. |
| `icon(string $icon)` | Sets the button icon. |
| `variant(ActionVariant\|string $variant)` | Sets the button variant (e.g., default, outline, destructive). |
| `href(string\|\Closure $href)` | Sets the target URL or route name. |
| `method(string $method)`| Sets the HTTP method (get, post, put, delete, etc.). |
| `confirm(...)` | Attaches a confirmation dialog to the action. |
| `visibleWhen(bool\|\Closure $condition)`| Dynamically shows or hides the action. |
| `disabledWhen(bool\|\Closure $condition)`| Dynamically enables or disables the action. |
| `modal(bool $value = true)` | Opens the target link inside a modal. |
| `iconOnly(bool $value = true)` | Hides the text and only shows the icon. |
| `textOnly(bool $value = true)` | Hides the icon and only shows the text. |
| `iconOnlyOnMobile(bool $value = true)` | Collapses text on mobile screens. |
| `meta(array $meta)` | Sends arbitrary metadata to the frontend. |
