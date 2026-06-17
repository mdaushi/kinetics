---
title: Table Component
description: Komponen utama untuk merender tabel di antarmuka pengguna (Frontend).
---

Salah satu keunggulan Kinetics adalah filosofi *"Zero-boilerplate Frontend"*. Seluruh logika pemrosesan data terjadi di sisi *server*, sehingga *frontend* Anda cukup menampilkan hasilnya.

Untuk tujuan ini, Kinetics menyediakan komponen React bernama `<Table />`.

## Penggunaan Komponen

Impor komponen `<Table />` dari *package* React Kinetics dan berikan **nama key Inertia prop** (sebagai string) yang berisi data tabel. Anda bahkan tidak perlu mengekstrak datanya dari props halaman!

```tsx
import { Table } from "@mdaushi/kinetics-react";

export default function MyPage() {
  return (
    <div className="p-4">
      {/* "tableData" adalah key dari Laravel controller -> Inertia::render */}
      <Table table="tableData" />
    </div>
  );
}
```

## Apa yang Ditangani Komponen `<Table />`?

Komponen ini akan mem-*parsing* `tableData` yang diterima dan secara otomatis:
1. Merender kolom dan label header (beserta kemampuan klik untuk *sorting*).
2. Merender bilah pencarian (jika setidaknya ada satu kolom yang *searchable*).
3. Merender baris data tabel.
4. Menampilkan *badge* secara otomatis jika diatur melalui konfigurasi kolom (misal: `TextColumn::make()->badge()`).
5. Merender tombol *Actions* dan kelompok *Dropdown Menu* untuk *Action Groups* pada setiap baris.
6. Menampilkan bilah navigasi *Pagination* di bagian bawah.

Seluruh *state* (seperti halaman aktif atau *query* pencarian saat ini) secara otomatis disinkronisasi ke dalam URL melalui fitur pembaruan URL dari Inertia.js (metode `router.get` atau `router.visit` bawaan Inertia). Hal ini menjamin bahwa setiap halaman dan state pencarian dapat langsung dibagikan atau di-*bookmark*.
