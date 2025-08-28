# 📰 Backend Portal Berita

Proyek ini merupakan implementasi Backend Portal Berita menggunakan Laravel 12.  
Fitur mencakup autentikasi, manajemen artikel, kategori, bookmark, notifikasi email, caching, hingga reset password dengan sistem queue.

---

## 🚀 Instalasi

1. Clone repository:
   ```
   git clone <repo-url>
   cd <repo-folder>
2. Install dependency:
   ```
   composer install
3. Copy file .env:
   ```
   cp .env.example .env

4. Generate application key
   ```
   php artisan key:generate

5. Jalankan migration
   ```
   php artisan migrate

# Konfigurasi Email
```
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=<Email_Anda>
MAIL_PASSWORD=<Password_App_Google>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@company.com"
MAIL_FROM_NAME="${APP_NAME}"

Pastikan sudah membuat App Password di Google Account, bukan password Gmail biasa.
```
# Menjalankan Queue
Beberapa fitur (seperti notifikasi email) berjalan di background.
Gunakan perintah berikut untuk menjalankan queue worker:
```
php artisan queue:work
```

# Autentikasi API
Header Wajib
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer <Token>
```

# Endpoint utama:
```
POST /register → Register user baru
POST /login → Login & dapatkan token
POST /logout → Logout user
GET /profile → Ambil profil user (beserta postingan & bookmark)
```
Jika menggunakan form-data, untuk update gunakan
```
_method: PUT
```
# Reset Password
```
POST /forgot-password → Kirim link reset password ke email
POST /reset-password → Reset password dengan token
```
# Note Tambahan
```
1.API ini dirancang untuk dijalankan secara local development (127.0.0.1:8000).
2.Gunakan Postman untuk testing dengan menambahkan header sesuai instruksi.
```
