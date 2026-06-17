---
title: Quick Start
description: Membuat tabel datatable pertama Anda dengan Kinetics.
---

Panduan ini akan menunjukkan cara membuat tabel sederhana untuk menampilkan daftar Post (Artikel) lengkap dengan fitur pencarian dan pengurutan bawaan.

## 1. Persiapan Model

Pastikan Anda memiliki model Eloquent (misalnya `Post`). Kinetics akan menggunakan model ini untuk melakukan query ke database secara otomatis.

## 2. Definisikan Tabel di Controller (Laravel)

Di dalam controller Anda, gunakan `Table` class dari Kinetics untuk mendefinisikan kolom-kolom apa saja yang ingin ditampilkan.

```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;
use Kinetics\Table;
use Kinetics\Columns\TextColumn;

class PostController extends Controller
{
    public function index()
    {
        $postsTable = Table::model(Post::class)
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Artikel')
                    ->sortable()
                    ->searchable(),
                
                // Mendukung relasi (dot notation)
                TextColumn::make('user.name')
                    ->label('Penulis')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->make();

        // Mengirim data ke komponen React via Inertia
        return Inertia::render('Posts/Index', [
            'postsTable' => $postsTable
        ]);
    }
}
```

> **Catatan:** Fungsi `->make()` di akhir *chain* akan mengeksekusi *pipeline* (sorting, searching, pagination) dan mengembalikan array data terstruktur yang siap dikonsumsi oleh frontend.

## 3. Render Tabel di Frontend (React)

Di sisi frontend, Anda hanya perlu memanggil komponen `<Table />` dan meneruskan data yang didapat dari server (Inertia props).

```tsx
import { Head } from '@inertiajs/react';
import { Table } from "@mdaushi/kinetics-react";

export default function PostIndex() {
  return (
    <div className="container mx-auto mt-10">
      <Head title="Daftar Artikel" />
      
      <div className="bg-white rounded-lg shadow p-6">
        <h1 className="text-2xl font-bold mb-6">Daftar Artikel</h1>
        
        {/* Render komponen tabel dengan memberikan nama key props Inertia */}
        <Table table="postsTable" />
      </div>
    </div>
  );
}
```

Itu saja! Dengan kode di atas, Anda sudah mendapatkan tabel yang sepenuhnya fungsional:
- **Pencarian global** untuk kolom `title` dan `user.name`
- **Pengurutan** (sorting) ketika mengklik header kolom
- **Paginasi** yang dikelola sepenuhnya oleh server

### Selanjutnya
Pelajari lebih lanjut tentang tipe-tipe kolom yang tersedia di halaman [Columns](/columns/overview).
