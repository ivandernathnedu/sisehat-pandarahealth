#!/bin/sh

# Hentikan eksekusi jika terjadi error
set -e

echo "🚀 Memulai Konfigurasi Otomatis Deployment SiSehat..."

# Pastikan file database sqlite ada jika menggunakan sqlite
mkdir -p database
touch database/database.sqlite
chmod -R 777 database

# 1. Jalankan Migrasi Tabel
echo "⚙️ Menjalankan Database Migrations..."
php artisan migrate --force

# 2. Impor Data Master & Transaksi CSV
echo "📂 Mengimpor Data Master dari Folder Imports..."
php artisan import:csv

# 3. Optimalisasi Performa Laravel (Cache)
echo "⚡ Mengoptimalkan Cache Konfigurasi & Rute..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Konfigurasi berhasil! Server siap melayani pengunjung."
