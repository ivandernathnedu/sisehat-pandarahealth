<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=pandara_health", "root", "");
    echo "Koneksi Berhasil!";
} catch (PDOException $e) {
    echo "Koneksi Gagal: " . $e->getMessage();
}
