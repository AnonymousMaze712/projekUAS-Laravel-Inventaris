System Requirement :

- PHP 7.4
- MySQL
- Docker

Fitur Aplikasi :

<details>
  <summary>Lihat Fitur-fitur</summary>
  
- Login Admin
<img width="1919" height="726" alt="image" src="https://github.com/user-attachments/assets/f03f0fb5-67ab-480b-90f6-2d09186ae289" />

- Tampilan Dashboard
<img width="1920" height="1039" alt="image" src="https://github.com/user-attachments/assets/516b83e6-3032-4a99-96df-1aa351ac5ee8" />

- Data Kategori
<img width="1920" height="961" alt="{4C504A96-0189-4D36-B1EC-0EBC1484D8C9}" src="https://github.com/user-attachments/assets/86235c1b-6599-484c-8904-7e92acc52e00" />
  
- Data Produk
<img width="1920" height="960" alt="{2EA2C137-5911-4956-A2BB-4DB88CCD1CC3}" src="https://github.com/user-attachments/assets/c5a41e26-9a31-4f5c-9d47-3bfa8ac34670" />

- Data Customer
<img width="1920" height="959" alt="{068AAA98-658D-4902-B83B-9B78E4696C8F}" src="https://github.com/user-attachments/assets/e4028435-a6b4-43dd-9505-a8f4125c7a37" />

- Data Supplier
<img width="1920" height="959" alt="{45F90D81-3191-4D55-B81F-C2683892C884}" src="https://github.com/user-attachments/assets/5cdbd76a-90b0-4d32-b843-1a191af63f37" />

- Stok In / Out
<img width="1920" height="960" alt="{D8666CC0-FA95-4B8F-8E41-E4B18EB36196}" src="https://github.com/user-attachments/assets/ad9f7aed-8b72-425c-a990-55fafa9b623f" />
<img width="1918" height="950" alt="{A2C4D87A-62FF-4021-8ADD-940851C98354}" src="https://github.com/user-attachments/assets/d43b1467-d779-4e3d-af05-155592d68cdc" />

- Export PDF/Excel
<img width="1920" height="1080" alt="{7AF15160-B026-4C9C-97E8-2794B66736F0}" src="https://github.com/user-attachments/assets/bd24292c-7116-4bc3-9eb4-cc4f135db781" />
  </details>


Tentu, berikut adalah contoh file `README.md` yang menggunakan **PHP versi 7.4**, **Apache**, **MySQL**, dan **phpMyAdmin** dengan penyesuaian port:

  * Akses Laravel: **Port 9009**
  * Akses phpMyAdmin: **Port 9002**

## 🐳 Panduan Instalasi Laravel dengan Docker (PHP 7.4, Port Kustom)

Panduan ini memandu Anda melalui instalasi dan menjalankan proyek Laravel baru menggunakan Docker Compose dengan spesifikasi: **PHP 7.4** (melalui Apache), **MySQL**, dan **phpMyAdmin**, dengan port kustom yang diminta.

-----

## Langkah 1: Membuat Proyek Laravel Baru

Kita akan menggunakan *image* Composer/PHP sementara dengan versi 7.4 untuk membuat proyek Laravel baru.

Ganti `nama-aplikasi` dengan nama folder proyek yang Anda inginkan.

```bash
# Membuat direktori proyek dan masuk ke dalamnya
mkdir nama-aplikasi
cd nama-aplikasi

# Membuat proyek Laravel menggunakan image PHP 7.4 dan Composer sementara
docker run --rm \
    -v $(pwd):/app \
    composer:2.7.2 --ignore-platform-reqs create-project --prefer-dist laravel/laravel . "7.*"
```

-----

## Langkah 2: Membuat Konfigurasi Docker Compose

Buat file bernama **`docker-compose.yml`** di root direktori proyek Anda (`nama-aplikasi`) dengan port yang telah disesuaikan:

```yaml
version: '3.8'

services:
  # Layanan Database MySQL
  mysql:
    image: mysql:5.7
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: root_password_anda 
      MYSQL_DATABASE: laravel_app_db         
      MYSQL_USER: laravel_user               
      MYSQL_PASSWORD: laravel_password       
    ports:
      - "33061:3306" # Opsional: Akses dari host di port 33061
    volumes:
      - dbdata:/var/lib/mysql
    networks:
      - app-network

  # Layanan Aplikasi PHP 7.4 & Web Server Apache
  app:
    build:
      context: .
      dockerfile: Dockerfile
    image: laravel-apache-php74-app
    restart: always
    volumes:
      - .:/var/www/html
    ports:
      - "9009:80" # Akses aplikasi di host melalui port 9009 (BARU)
    depends_on:
      - mysql
    networks:
      - app-network

  # Layanan phpMyAdmin
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    restart: always
    environment:
      PMA_HOST: mysql 
      PMA_PORT: 3306
      MYSQL_ROOT_PASSWORD: root_password_anda 
    ports:
      - "9002:80" # Akses phpMyAdmin di host melalui port 9002 (BARU)
    depends_on:
      - mysql
    networks:
      - app-network

# Volume untuk menyimpan data MySQL secara persisten
volumes:
  dbdata:

# Jaringan kustom untuk kontainer
networks:
  app-network:
    driver: bridge
```

-----

## Langkah 3: Membuat Dockerfile untuk PHP 7.4 & Apache

Buat file bernama **`Dockerfile`** di root proyek Anda. Kontennya sama seperti sebelumnya karena perubahan port hanya terjadi di `docker-compose.yml`.

```dockerfile
FROM php:7.4-apache

# Instal dependensi sistem yang diperlukan
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# Instal dan aktifkan ekstensi PHP yang diperlukan oleh Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Aktifkan rewrite module Apache
RUN a2enmod rewrite

# Konfigurasi Apache (Mengatur root direktori ke public)
COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Mengatur user/group www-data
RUN usermod -u 1000 www-data

WORKDIR /var/www/html
```

### Konfigurasi Apache (Virtual Host)

Buat direktori **`docker/apache`** dan di dalamnya buat file **`vhost.conf`**:

```bash
mkdir -p docker/apache
```

**`docker/apache/vhost.conf`**

```apache
<VirtualHost *:80>
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

-----

## Langkah 4: Konfigurasi File Lingkungan (.env)

Edit file **`.env`** di root proyek Anda. Perubahan pada `APP_URL` mencerminkan port akses Laravel yang baru (`9009`).

```env
# URL Aplikasi
APP_URL=http://localhost:9009 # DISESUAIKAN DENGAN PORT BARU

# Pengaturan Database
DB_CONNECTION=mysql
DB_HOST=mysql         # Nama layanan dari docker-compose.yml
DB_PORT=3306
DB_DATABASE=laravel_app_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_password
```

-----

## Langkah 5: Menjalankan Kontainer Docker

Bangun *image* dan nyalakan semua layanan:

```bash
docker-compose up -d --build
```

-----

## Langkah 6: Menjalankan Migrasi Database

Setelah kontainer berjalan, jalankan migrasi database di dalam kontainer `app`:

```bash
docker-compose exec app php artisan migrate
```

-----

## Akses Aplikasi dan Database

| Layanan | URL | Catatan |
| :--- | :--- | :--- |
| **Aplikasi Laravel** | **`http://localhost:9009`** | Tampilan Welcome Laravel. |
| **phpMyAdmin** | **`http://localhost:9002`** | Gunakan `root` dan `root_password_anda` untuk login. |


  * [Dokumentasi Laravel Sail Resmi](https://laravel.com/docs/sail)
  * [Dokumentasi Docker](https://docs.docker.com/)
