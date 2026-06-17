---
title: Pipeline Overview
description: Memahami konsep arsitektur pipeline pada Kinetics.
---

Salah satu keunggulan utama dari Kinetics adalah arsitekturnya yang berbasis *Pipeline*. Alih-alih melakukan *query* ke database secara *monolithic*, proses memanipulasi data dipecah ke dalam beberapa tahapan (*pipes*) yang independen.

## Konsep Dasar

Ketika Anda memanggil metode `make()` pada `Table` class, data (berupa Eloquent Builder) akan dialirkan melewati serangkaian pipa (pipes). Setiap pipa bertanggung jawab atas satu tugas spesifik:

```text
Request 
  ↓
[SortPipe]      -> Menambahkan order_by ke query jika ada request sorting
  ↓
[SearchPipe]    -> Menambahkan where/orWhere jika ada kata kunci pencarian
  ↓
[FilterPipe]    -> Menambahkan kondisi spesifik berdasarkan filter aktif
  ↓
[PaginatePipe]  -> Mengeksekusi query dan melakukan paginasi
  ↓
TableResult (JSON)
```

## Keuntungan

1. **Modularitas**: Setiap fitur terisolasi. Logika pencarian tidak akan mengganggu logika paginasi.
2. **Kustomisasi**: Anda bisa mengubah urutan *pipes*, menghapus *pipe* bawaan, atau menyisipkan *pipe* kustom buatan Anda sendiri di tengah-tengah alur.
3. **Kinerja Terprediksi**: Karena semua manipulasi data terjadi di level database melalui Eloquent, Anda terhindar dari masalah kinerja (seperti menarik semua data ke memori RAM untuk diproses).

Di halaman selanjutnya, kita akan membahas masing-masing *pipe* bawaan yang disediakan oleh Kinetics secara mendetail.
