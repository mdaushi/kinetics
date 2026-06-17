---
title: Customization
description: Panduan untuk melakukan penyesuaian gaya (styling) tabel.
---

Kinetics dibangun di atas pondasi **TanStack Table** (sebagai *headless UI core*) dan merender komponen bawaan dengan desain berbasis **shadcn/ui** menggunakan utilitas **Tailwind CSS**.

## Tailwind CSS Integration

Pastikan bahwa file `tailwind.config.js` atau `app.css` Anda (untuk Tailwind v4) telah dikonfigurasi untuk memindai kelas-kelas utilitas di dalam paket `@mdaushi/kinetics-react`. Tanpa langkah ini, gaya *shadcn/ui* tidak akan terbaca dan tabel Anda akan tampil "telanjang" tanpa gaya.

*(Lihat bagian [Instalasi](/getting-started/installation) untuk panduan konfigurasi Tailwind secara detail).*

## Kustomisasi Global

Secara *default*, tabel mengambil warna bawaan dari variabel CSS standar *shadcn/ui*. Jika *project* Anda sudah menerapkan sistem tema *(theming)* dengan menggunakan variabel seperti `--primary`, `--border`, atau `--background`, komponen `<Table />` Kinetics akan secara mulus beradaptasi dengan palet warna aplikasi Anda.

## Kustomisasi Sel Tingkat Lanjut (Akan Datang)

Saat ini, fokus utama Kinetics adalah *zero-boilerplate*, di mana komponen `<Table />` menangani hampir semua kasus penggunaan secara *out-of-the-box*.

Kinetics menyediakan akses penuh ke *underlying table instance* dari TanStack Table melalui mekanisme *Hook* (`useTable`) atau properti akses, yang dalam rilis-rilis mendatang akan didokumentasikan lebih lanjut. Ini akan memungkinkan pengembang melakukan *override* render sel secara kustom, bila fitur-fitur seperti `TextColumn` dan `Badge` standar tidak mencukupi kebutuhan kompleksitas UI spesifik aplikasi Anda.
