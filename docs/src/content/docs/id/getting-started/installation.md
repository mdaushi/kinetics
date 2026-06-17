---
title: Installation
description: Panduan instalasi Kinetics untuk Laravel dan React.
---

Kinetics terdiri dari dua bagian utama: **Package Laravel** (Backend) dan **Package React** (Frontend). Keduanya harus diinstal dan dikonfigurasi agar dapat bekerja sama.

## 1. Backend (Laravel)

Instal package Kinetics untuk Laravel menggunakan Composer:

```bash
composer require mdaushi/kinetics
```

Package ini menyediakan antarmuka `Table` class untuk mengonfigurasi kolom, aksi, dan *pipes* di sisi server.

## 2. Frontend (React)

Kinetics saat ini mendukung React (melalui Inertia.js). Instal package frontend menggunakan NPM atau package manager pilihan Anda:

```bash
npm install @mdaushi/kinetics-react
```
atau
```bash
pnpm add @mdaushi/kinetics-react
```

## 3. Konfigurasi Tailwind CSS

Kinetics dibangun menggunakan komponen [shadcn/ui](https://ui.shadcn.com/) dan menggunakan Tailwind CSS untuk *styling*. Anda perlu memberi tahu Tailwind untuk memindai kelas-kelas utilitas di dalam package Kinetics agar tidak terhapus (purged) saat proses *build*.

### Tailwind v4

Jika Anda menggunakan Tailwind v4, tambahkan direktif `@source` ke dalam file CSS utama Anda (misalnya `resources/css/app.css`):

```css
@import "tailwindcss";

@source "../../node_modules/@mdaushi/kinetics-react/dist";
```

### Tailwind v3

Jika Anda masih menggunakan Tailwind v3, tambahkan path dist package ke dalam array `content` di file `tailwind.config.js` Anda:

```js
export default {
  content: [
    // ... path aplikasi Anda yang sudah ada
    "./node_modules/@mdaushi/kinetics-react/dist/**/*.js",
  ],
};
```

## Mempublikasikan Konfigurasi (Opsional)

Jika Anda ingin mengubah pengaturan *default* global (seperti ukuran paginasi standar, opsi jumlah paginasi, atau tema bawaan), Anda dapat mempublikasikan file konfigurasi Kinetics ke direktori config Laravel Anda:

```bash
php artisan vendor:publish --tag=kinetics-config
```

Perintah ini akan membuat sebuah file `config/kinetics.php` di dalam aplikasi Anda.

## Langkah Selanjutnya
Anda kini siap untuk membangun *datatable* pertama Anda! Lanjutkan ke panduan [Quick Start](/id/getting-started/quick-start).!
