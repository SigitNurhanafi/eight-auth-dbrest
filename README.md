# 🔐 Eight Auth DB-REST API

REST API dengan autentikasi token (Laravel Sanctum), manajemen user (Service-Repository Pattern), dan pengambilan data eksternal secara real-time.

---

## 🛠️ Persiapan (Prerequisites)

Sebelum instalasi, pastikan environment Anda memiliki:
- **PHP** >= 8.1
- **Composer** (Dependency Manager)
- **MySQL** atau **MariaDB**
- **Git** (untuk cloning)

---

## 🚀 Langkah Instalasi di Environment Baru

Ikuti langkah berikut untuk menjalankan project ini dari nol:

### 1. Clone Project
```bash
git clone <url-repository> eight-auth-dbrest
cd eight-auth-dbrest
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Setup Environment
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan bagian ini:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_db_anda
DB_USERNAME=username_db
DB_PASSWORD=password_db

# Nama aplikasi dan URL
APP_URL=http://localhost:8787

# URL sumber data eksternal (WAJIB DIISI)
EXTERNAL_DATA_URL=https://example.com/data.txt
```

### 4. Generate App Key
```bash
php artisan key:generate
```

### 5. Database & Seeding
Jalankan migrasi untuk membuat tabel dan seeder untuk data awal (termasuk user admin):
```bash
php artisan migrate --seed
```
> **Note:** User admin default biasanya: `admin@mail.com` / `password123` (Cek `DatabaseSeeder.php`).

### 6. Jalankan Server
```bash
php artisan serve --port=8787
```
Aplikasi bisa diakses di `http://localhost:8787`.

---

## 🏗️ Arsitektur Project (Service-Repository)

Project ini menggunakan pola **Service-Repository** untuk menjaga kode tetap bersih:

- **UserRepository**: Menangani akses database untuk model User.
- **UserService**: Menangani logika bisnis user (hashing, logic update).
- **ExternalDataRepository**: Mengambil data melalu HTTP Client.
- **ExternalDataService**: Memproses filtering dan parsing data eksternal.

---

## 🛡️ Fitur Utama User Management
- **Soft Deletes**: User yang dihapus dialihkan ke status "terhapus" (data tidak hilang).
- **Self-Deletion Protection**: User tidak boleh menghapus akunnya sendiri.
- **Last Admin Guard**: Mencegah sistem kehilangan admin terakhir.
- **PUT vs PATCH**: Pemisahan validasi untuk update total dan update parsial.

---

## 🔍 Cara Cari Data
Endpoint: `GET /api/data`
Header: `Authorization: Bearer <token>`
Parameter (Uppercase):
- `NAMA` (Min 3 karakter)
- `NIM`
- `YMD`

---

## 📖 Testing
Daftar lengkap perintah cURL tersedia di file `curl_commands.md`.
