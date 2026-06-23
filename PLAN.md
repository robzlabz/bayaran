# PLAN — Aplikasi Manajemen Karyawan

## 📋 Ringkasan Fitur

Aplikasi manajemen karyawan dengan multi-tier (personal/company), multi-metode pembayaran (bulanan/harian/per-pengantaran), pencatatan hutang, absensi dengan foto, dan manajemen saldo.

---

## 🧱 Fase 1: Foundation ✅ Selesai

### 1.1 Role System

| Role | Dashboard | Prefix URL |
|------|-----------|------------|
| **Super Admin** | Panel admin — lihat semua pengguna | `/admin/*` |
| **Owner** (Company/Personal) | Kelola karyawan, hutang, absensi | `/company/*` |
| **Employee** | Clock-in/out manual | `/employee/*` |

**Database:**
```php
users table:
- account_type: enum('personal', 'company')
- company_name: string, nullable (khusus company)
- role: enum('super_admin', 'owner', 'employee') — default 'owner'
- owner_id: FK to users, nullable (untuk employee)
```

### 1.2 Arsitektur Controller

```
App/Http/Controllers/
├── Auth/               # Breeze auth (login redirect berdasarkan role)
├── SuperAdmin/
│   └── DashboardController.php   → /admin/dashboard
├── Company/
│   ├── DashboardController.php   → /company/dashboard
│   └── EmployeeController.php    → /company/employees/*
├── Employee/
│   └── DashboardController.php   → /employee/dashboard
└── ProfileController.php
```

### 1.3 Layout & Navigasi

| Device | Tampilan |
|--------|----------|
| **Desktop (lg+)** | Sidebar fixed di kiri (w-64) + konten di kanan |
| **Mobile (< lg)** | Topbar dengan hamburger → drawer slide dari kiri |

Sidebar menyesuaikan role:
- **Owner**: Dashboard, Karyawan, Hutang, Absensi, Pembayaran
- **Employee**: Clock In/Out, Riwayat
- **Super Admin**: Dashboard, Semua Pengguna

---

## 🧱 Fase 2: Manajemen Karyawan (Minggu 2)

### 2.1 CRUD Karyawan

| Field | Tipe | Keterangan |
|-------|------|------------|
| Nama | string | |
| No. HP | string | |
| Foto | image, nullable | |
| Tipe Pembayaran | enum: `monthly`, `daily`, `per_delivery` | |
| Gaji Pokok | decimal, nullable | Untuk monthly |
| Upah Harian | decimal, nullable | Untuk daily |
| Tarif Pengantaran | decimal, nullable | Untuk per_delivery |
| Saldo | decimal, default 0 | Untuk daily & per_delivery |
| Status Aktif | boolean | Default true |

### 2.2 Billing Company (Per Karyawan)

Jika user bertipe **company** dan menambah karyawan:

1. Setiap tambah karyawan → generate invoice 5000/karyawan
2. Status karyawan otomatis `pending` sampai dibayar
3. Admin upload bukti transfer bank manual
4. Admin konfirmasi pembayaran → karyawan aktif

**Database:**
```php
invoices table:
- user_id (company owner)
- amount: decimal
- status: enum('pending', 'paid', 'cancelled')
- proof_of_payment: string, nullable (path file upload)
- notes: text, nullable
- paid_at: timestamp, nullable
- expires_at: timestamp
```

---

## 🧱 Fase 3: Hutang Karyawan (Minggu 3)

### 3.1 Pencatatan Hutang

Fitur untuk mencatat hutang yang dilakukan karyawan.

| Fitur | Detail |
|-------|--------|
| Tambah Hutang | Pilih karyawan, masukkan jumlah, keterangan, tanggal |
| Daftar Hutang | Tabel semua hutang dengan filter (lunas/belum) |
| Detail per Karyawan | Total hutang per karyawan |
| Pelunasan | Tandai lunas, input tanggal bayar |
| Riwayat | Log semua transaksi hutang |

**Database:**
```php
debts table:
- employee_id: FK
- amount: decimal
- description: text
- debt_date: date
- is_paid: boolean, default false
- paid_at: timestamp, nullable
- notes: text, nullable
```

---

## 🧱 Fase 4: Saldo & Ongkos (Minggu 4)

### 4.1 Manajemen Saldo

Untuk karyawan **daily** dan **per_delivery** — mereka punya saldo.

**Skema:**
1. Admin isi saldo (top-up) → saldo bertambah
2. Gaji/pembayaran dipotong dari saldo
3. Riwayat mutasi saldo (debit/kredit)

### 4.2 Pencatatan Ongkos (Transport)

Karyawan harian kadang minta ongkos kirim (misal 10rb, bisa custom).

| Fitur | Detail |
|-------|--------|
| Tambah Ongkos | Pilih karyawan, nominal (default 10rb, bisa custom), tanggal, keterangan |
| Otomatis catat ke saldo | Ongkos dicatat sebagai pengurangan saldo |
| Riwayat Ongkos | Tabel semua ongkos per karyawan |

**Database:**
```php
transactions table:
- employee_id: FK
- type: enum('topup', 'salary', 'transport', 'debt_payment', 'adjustment')
- amount: decimal (positif = masuk, negatif = keluar)
- balance_before: decimal
- balance_after: decimal
- description: text, nullable
- transaction_date: date
```

---

## 🧱 Fase 5: Absensi dengan Foto (Minggu 5-6)

### 5.1 Sistem Absen

Fitur utama: **clock-in & clock-out dengan foto.**

**Alur:**
1. Karyawan pilih nama dari daftar → kamera terbuka
2. Foto clock-in → tersimpan dengan timestamp
3. Nanti kalau clock-out → foto clock-out → tersimpan
4. Admin bisa override (jika karyawan lupa)

**Mode Absen:**
- **Kiosk Mode** — satu device (tablet/handphone) ditempel di tempat kerja, karyawan absen sendiri
- **Admin Entry** — admin yang meng-input-kan absen dari dashboard

### 5.2 Handling Lupa Clock-in/Clock-out

| Skenario | Solusi |
|----------|--------|
| Lupa clock-in | Admin input manual jam masuk |
| Lupa clock-out | Admin input manual jam pulang |
| Lupa keduanya | Admin input manual full day |
| Setiap manual entry | Ditandai flag `is_manual_entry = true` |
| Catatan | Admin tambah notes/alasan |

### 5.3 Perhitungan Jam Kerja

- Dari clock-in ke clock-out → total jam kerja
- Untuk daily: total jam × (daily_rate / jam_kerja_normal)
- Untuk monthly: absensi sebagai acuan, gaji tetap

**Database:**
```php
attendances table:
- employee_id: FK
- clock_in: datetime
- clock_out: datetime, nullable
- clock_in_photo: string (path)
- clock_out_photo: string, nullable (path)
- is_manual_entry: boolean, default false
- is_clock_in_manual: boolean, default false
- is_clock_out_manual: boolean, default false
- notes: text, nullable
- work_hours: decimal, nullable (calculated)
- overtime_hours: decimal, nullable
```

---

## 🧱 Fase 6: Laporan & Export (Minggu 7)

### 6.1 Laporan

| Laporan | Isi |
|---------|-----|
| Rekap Absen Bulanan | Per karyawan: total hari kerja, total jam, keterlambatan |
| Rekap Hutang | Semua hutang per periode, status lunas/belum |
| Rekap Transaksi | Mutasi saldo, ongkos, pembayaran |
| Rekap Gaji | Gaji yang harus dibayar per periode |
| Laporan Perusahaan | Total biaya per karyawan (untuk company) |

### 6.2 Export

- Export ke PDF
- Export ke Excel (menggunakan PhpSpreadsheet atau Laravel Excel)

---

## 📊 Entity Relationship (Simplified)

```
users (1) ──── (N) employees
users (1) ──── (N) invoices (khusus company)

employees (1) ──── (N) debts
employees (1) ──── (N) transactions
employees (1) ──── (N) attendances
employees (1) ──── (N) transports
```

---

## 🗓️ Milestone & Prioritas

| Fase | Prioritas | Estimasi |
|------|-----------|----------|
| **Fase 1:** Foundation + Auth | 🔴 P0 | 1 hari |
| **Fase 2:** Manajemen Karyawan + Billing | 🔴 P0 | 2 hari |
| **Fase 3:** Hutang Karyawan | 🔴 P0 | 1 hari |
| **Fase 4:** Saldo & Ongkos | 🟡 P1 | 2 hari |
| **Fase 5:** Absensi Foto | 🟡 P1 | 3 hari |
| **Fase 6:** Laporan & Export | 🟢 P2 | 2 hari |

---

## ⚙️ Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 13 (PHP 8.3) |
| Frontend | Blade + Tailwind CSS |
| Database | PostgreSQL (via Herd/Laravel Valet) |
| Auth | Laravel Breeze (Blade stack) |
| File Upload | Laravel Filesystem (local) |
| Image Capture | WebRTC / HTML5 Camera API |
| Export | PhpSpreadsheet / Laravel Excel |
| Queue | Database queue (untuk background jobs) |

---

## 🚀 Cara Mulai Development (Urutan)

```
Fase 1: Foundation
├── Step 1: Custom register (personal/company)
├── Step 2: Modifikasi dashboard
└── Step 3: Setup layout & navigasi

Fase 2: Karyawan
├── Step 4: Migration + Model Employee
├── Step 5: CRUD Karyawan (Controller + Views)
└── Step 6: Billing system untuk company

Fase 3: Hutang
├── Step 7: Migration + Model Debt
└── Step 8: CRUD Hutang + Pelunasan

Fase 4: Saldo & Ongkos
├── Step 9: Migration + Model Transaction
├── Step 10: Manajemen saldo
└── Step 11: Pencatatan ongkos kirim

Fase 5: Absensi
├── Step 12: Migration + Model Attendance
├── Step 13: Integrasi kamera (WebRTC)
├── Step 14: Clock-in/out flow
└── Step 15: Manual entry & override

Fase 6: Laporan
├── Step 16: Report controllers & views
├── Step 17: Export PDF/Excel
└── Step 18: Dashboard widgets & summary
```

---

## ✅ Status Proyek

- [x] Laravel 13 installed
- [x] Breeze Blade installed (Tailwind + Dark mode)
- [x] PostgreSQL configured
- [x] Git initialized, first commit done
- [x] Fase 1: Foundation
- [ ] Fase 2: Manajemen Karyawan
- [ ] Fase 3: Hutang
- [ ] Fase 4: Saldo & Ongkos
- [ ] Fase 5: Absensi Foto
- [ ] Fase 6: Laporan & Export
