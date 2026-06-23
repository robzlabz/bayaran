# Bayaran

Aplikasi manajemen karyawan & absensi — untuk UMKM dan personal.

Dibangun dengan **Laravel 13** + **Blade** + **Tailwind CSS** + **PostgreSQL**.

---

## Fitur

### 👥 Manajemen Karyawan
- 3 tipe pembayaran: **Bulanan**, **Harian**, **Per Pengantaran**
- Auto-create user account saat tambah karyawan
- Password default: `bayaran` + 4 digit terakhir no HP

### 💰 Saldo & Ongkos
- Top-up saldo karyawan
- Catat ongkos kirim (default Rp10.000, bisa custom)
- Riwayat mutasi saldo (debit/kredit)

### 📝 Hutang
- Catat hutang per karyawan
- Status lunas / belum
- Filter berdasarkan status, bulan, karyawan

### 📸 Absensi
- Clock-in / clock-out dengan **kamera (WebRTC)**
- Hitung jam kerja otomatis
- **Manual entry** jika lupa clock-in/out (dengan flag)

### 📊 Laporan
- Rekap absensi per bulan (group per karyawan, total jam)
- Rekap hutang per bulan (filter status)
- **Export PDF** (dompdf)

### 🔐 Multi-Role
| Role | Akses | Login |
|------|-------|-------|
| **Super Admin** | Panel semua pengguna | Email |
| **Owner** (Company/Personal) | Kelola karyawan, absensi, hutang | Email |
| **Employee** | Clock-in/out, riwayat | No HP |

### 📱 Mobile Friendly
- **Desktop**: Sidebar navigasi
- **Mobile**: Topbar + drawer (Alpine.js)

---

## Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 13 (PHP 8.3) |
| Frontend | Blade + Tailwind CSS 4 |
| Database | PostgreSQL 14 |
| Auth | Laravel Breeze (Blade stack) |
| PDF | barryvdh/laravel-dompdf |
| Build | Vite 8 |

---

## Instalasi

```bash
git clone git@github.com:robzlabz/bayaran.git
cd bayaran

composer install
npm install

cp .env.example .env
# Sesuaikan DB_DATABASE, DB_USERNAME, dll di .env

php artisan key:generate
php artisan migrate --seed

npm run build

php artisan serve
```

### Default Credentials

| Role | Email / Phone | Password |
|------|---------------|----------|
| Super Admin | `admin@karyawanku.app` | `admin123` |
| Owner | (register baru) | (user punya) |
| Employee | (phone dari owner) | `bayaran` + 4 digit HP |

---

## Struktur Route

```
/                           → Landing page (2 pilihan login)
/login                      → Admin/Company login (email)
/employee/login             → Employee login (phone)

/admin/dashboard            → Super Admin panel
/company/dashboard          → Owner dashboard
/company/employees/*        → CRUD karyawan
/company/debts/*            → CRUD hutang
/company/transactions/*     → Mutasi saldo
/company/transports/*       → Catat ongkos
/company/attendances/*      → Absensi (owner view)
/company/reports/*          → Laporan + export PDF

/employee/dashboard         → Clock-in/out
/employee/attendance/*      → API absensi (status, clock-in, clock-out)
```

---

## Screenshots

Lihat folder [docs/screenshots](docs/screenshots) atau [[Screenshots]] (Obsidian vault `Rafamily/bayaran.web.id/`).

---

## Development

```bash
# Run dev server
php artisan serve --port=8081

# Run Vite dev
npx vite

# Build assets
npm run build
```

---

## License

MIT
