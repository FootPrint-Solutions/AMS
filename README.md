# Panduan Tata Cara Pengerjaan Aplikasi AaMS

Selamat datang di panduan tata cara pengerjaan aplikasi AaMS. Panduan ini bertujuan untuk memastikan konsistensi dalam penulisan kode dan menciptakan lingkungan kerja yang kondusif bagi pengembangan aplikasi AaMS.

## Gaya Penulisan Kode

- **Bahasa**: Semua kode ditulis dalam bahasa Inggris.
- **Penamaan Database**: Gunakan snake case untuk menamai tabel dan kolom di dalamnya.
  - Contoh: `vehicle`, `vehicle_brand`, `id_brand`.
- **Script**: 
  - Gunakan snake case untuk nama fungsi.
    - Contoh: `get_data`.
  - Gunakan Pascal case untuk penamaan kelas.
    - Contoh: `BookController`.
- **Dokumentasi**:
  - Tulis dokumentasi dengan jelas dan rapi.
  - Untuk fungsi dan kelas, gunakan format phpdoc.
    - Jelaskan parameter apa saja yang diterima, tujuan masing-masing parameter, serta output yang dihasilkan (jika ada).
- **IDE Formatter**: Gunakan formatter di IDE, seperti Prettier di VSC.

## Keterangan Database

- Pastikan menggunakan perintah `php artisan storage:link` untuk menghubungkan penyimpanan.
- Jangan lupa selalu optimize aplikasi ini setiap anda mendapat perubahan dari github menggunakan `php artisan optimize`
- Jangan lupa selalu migrate supaya anda tidak ketinggalan database `php artisan migrate`

## Tips Tambahan

- **Hapus Kode Tidak Terpakai**: Hapus semua kode yang tidak terpakai, jangan hanya di-comment saja.
- **Revert ke Commit Sebelumnya**: Jika terjadi masalah, revert ke commit sebelum terjadinya masalah.
- **Hindari Kode Tidak Terpakai**: Jangan biarkan ada fungsi, variabel, atau baris kode yang tidak terpakai, seperti hasil copy-paste dari tempat lain, perintah `var_dump`, `console.log`, dll.
- **Jika Raguan, Simpan Kode Tidak Terpakai di Bagian Paling Bawah**: Jika masih ragu atau terpaksa, komentari kode yang tidak terpakai dan simpan di bagian paling bawah.

Dengan mengikuti panduan ini, diharapkan pengembangan aplikasi AaMS dapat berjalan dengan lebih terstruktur dan efisien.
