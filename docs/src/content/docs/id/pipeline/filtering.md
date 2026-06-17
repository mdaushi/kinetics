---
title: Filtering
description: Menerapkan filter spesifik ke dalam data tabel.
---

*Filtering* adalah proses menyempitkan data berdasarkan kriteria tertentu (seperti kategori, status, rentang tanggal) yang bukan sekadar pencarian teks (*searching*). `FilterPipe` bertanggung jawab mengeksekusi logika ini.

## Filter Bawaan dan Kustom

Saat ini, Kinetics dirancang untuk memungkinkan Anda mendefinisikan logika filter Anda sendiri dengan sangat fleksibel. Daripada membatasi Anda pada tipe filter tertentu, fitur *filtering* umumnya diimplementasikan dengan menambahkan konfigurasi filter kustom ke tabel atau langsung menyisipkannya melalui *query builder* dasar.

*(Dokumentasi detail mengenai konfigurasi Filter kustom tingkat lanjut akan segera ditambahkan).*

## Perbedaan Filter dan Search

- **Search**: Mencari teks di beberapa kolom sekaligus menggunakan operasi `LIKE`. Biasanya hanya melibatkan satu *input box* sederhana di UI.
- **Filter**: Mencocokkan nilai persis (misalnya `status = 'published'`), atau menggunakan rentang (misalnya `created_at > '2023-01-01'`). Biasanya menggunakan elemen UI khusus seperti *dropdown*, *checkboxes*, atau *date picker*.
