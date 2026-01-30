# Database Restructure: Project-Asset Relationship

**Terakhir diupdate: 30 Januari 2026**

## 📋 Ringkasan

Sistem ini menggunakan arsitektur **Parent-Child** dimana:
- **1 Project (Proyek)** dapat memiliki **banyak Assets (Objek Penilaian)**
- Project berada di level Admin/Kontrak
- Asset berada di level Technical/Operasional

## 🏗️ Struktur Database

### Diagram Relasi

```
┌─────────────────────┐
│   kanban_clients    │
│  (Data Klien)       │
└──────────┬──────────┘
           │ 1
           │
           │ N
┌──────────▼──────────┐
│   projects_kanban   │◄────────────────────┐
│  (Level Admin)      │                     │
│  - Kontrak          │                     │
│  - Invoice          │                     │
│  - Proposal         │                     │
└──────────┬──────────┘                     │
           │ 1                              │
           │                                │
           │ N                              │
┌──────────▼──────────┐                     │
│ project_assets_kanban│                    │
│  (Level Technical)  │                     │
│  - Inspeksi         │                     │
│  - Analisis         │                     │
│  - Laporan          │                     │
└──────────┬──────────┘                     │
           │ 1                              │
           ├──────────────┬─────────────────┤
           │ N            │ N               │ N
┌──────────▼────┐ ┌───────▼──────┐ ┌────────▼─────┐
│ inspections   │ │working_papers│ │   reports    │
│   _kanban     │ │   _kanban    │ │   _kanban    │
└───────────────┘ └──────────────┘ └──────────────┘
```

---

## 📦 Tabel Utama

### 1. `kanban_clients` - Data Klien
```sql
- id (PK)
- name
- contact_person
- email
- phone
- address
- company_type (perusahaan/perorangan/instansi)
- notes
- timestamps
```

### 2. `projects_kanban` - Level Project/Admin
```sql
- id (PK)
- client_id (FK → kanban_clients)
- project_code (unique, contoh: PRJ-2026-001)
- name
- description
- location
- current_stage (enum STAGES)
- priority_status (normal/warning/critical)
- total_assets (counter)
- due_date
- timestamps
- soft_deletes
```

**STAGES untuk Project:**
```php
const STAGES = [
    'lead' => 'Lead / Permintaan',
    'proposal' => 'Proposal',
    'contract' => 'Kontrak',
    'inspection' => 'Inspeksi',
    'analysis' => 'Analisis / Kertas Kerja',
    'review' => 'Review Internal',
    'client_approval' => 'Approval Klien',
    'final_report' => 'Laporan Final',
    'invoicing' => 'Penagihan',
    'done' => 'Selesai',
];
```

> **Note:** Field `admin_stage`, `global_status`, `contract_number`, `contract_value` sudah **dihapus** dari schema. Hanya menggunakan `current_stage` saja.

### 3. `project_assets_kanban` - Level Asset/Technical
```sql
- id (PK)
- project_id (FK → projects_kanban)
- asset_code (unique, auto-generated: PRJ001-A01)
- name
- description
- asset_type (enum)
- location_address
- location_coordinates (nullable)
- current_stage (enum STAGES)
- priority_status (normal/warning/critical)
- position (untuk ordering di kanban)
- target_completion_date
- notes
- timestamps
- soft_deletes
```

**ASSET_TYPES:**
```php
const ASSET_TYPES = [
    'tanah' => 'Tanah',
    'bangunan' => 'Bangunan',
    'tanah_bangunan' => 'Tanah & Bangunan',
    'mesin' => 'Mesin & Peralatan',
    'kendaraan' => 'Kendaraan',
    'inventaris' => 'Inventaris',
    'aset_tak_berwujud' => 'Aset Tak Berwujud',
    'lainnya' => 'Lainnya',
];
```

**STAGES untuk Asset:**
```php
const STAGES = [
    'pending' => 'Menunggu',
    'inspection' => 'Inspeksi',
    'analysis' => 'Analisis / Kertas Kerja',
    'review' => 'Review Internal',
    'client_approval' => 'Approval Klien',
    'final_report' => 'Laporan Final',
    'done' => 'Selesai',
];
```

---

## 🔗 Tabel Child/Pendukung

### Tabel dengan FK ke Project DAN Asset

| Tabel | project_id | project_asset_id | Keterangan |
|-------|------------|------------------|------------|
| `inspections_kanban` | ✅ Required | ✅ Nullable | Inspeksi lapangan |
| `working_papers_kanban` | ✅ Required | ✅ Nullable | Kertas kerja analisis |
| `reports_kanban` | ✅ Required | ✅ Nullable | Laporan penilaian |
| `approvals_kanban` | ✅ Required | ✅ Nullable | Approval internal/klien |
| `documents_kanban` | ✅ Required | ✅ Nullable | Dokumen pendukung |
| `activities_kanban` | ✅ Required | ✅ Nullable | Log aktivitas |

### Tabel dengan FK ke Project saja

| Tabel | Keterangan |
|-------|------------|
| `proposals_kanban` | Proposal penawaran |
| `contracts_kanban` | Kontrak/SPK |
| `invoices_kanban` | Tagihan/Invoice |

---

## 🔄 Workflow

### Project Level (Admin)
```
Lead → Proposal → Contract → [Asset Processing...] → Invoicing → Done
                                    ↓
                          Assets dikerjakan paralel
```

### Asset Level (Technical)
```
Pending → Inspection → Analysis → Review → Client Approval → Final Report → Done
```

Setiap asset bisa bergerak independen di workflow technical-nya sendiri.

---

## 📁 Model Relationships

### ProjectKanban.php
```php
// One to Many
public function assets(): HasMany      // ProjectAsset
public function proposals(): HasMany   // ProposalKanban
public function contracts(): HasMany   // ContractKanban
public function invoices(): HasMany    // InvoiceKanban
public function documents(): HasMany   // DocumentKanban (project level)
public function activities(): HasMany  // ActivityKanban

// Through Assets
public function inspections(): HasManyThrough
public function workingPapers(): HasManyThrough
public function reports(): HasManyThrough

// Belongs To
public function client(): BelongsTo    // KanbanClient
```

### ProjectAsset.php
```php
// Belongs To
public function project(): BelongsTo   // ProjectKanban

// One to Many
public function inspections(): HasMany    // InspectionKanban
public function workingPapers(): HasMany  // WorkingPaperKanban
public function reports(): HasMany        // ReportKanban
public function documents(): HasMany      // DocumentKanban (asset level)
public function activities(): HasMany     // ActivityKanban (asset level)

// Accessors
public function getProgressPercentageAttribute(): int
// Menghitung progress berdasarkan current_stage
```

---

## 🗂️ Migration Order

Urutan migration berdasarkan dependencies:

```
1.  0001_01_01_000000_create_users_table
2.  0001_01_01_000001_create_cache_table
3.  0001_01_01_000002_create_jobs_table
4.  2026_01_19_080544_create_boards_table
5.  2026_01_19_080547_create_columns_table
6.  2026_01_19_080549_create_cards_table
7.  2026_01_19_080550_create_card_assignments_table
8.  2026_01_19_110923_create_card_attachments_table
9.  2026_01_26_090301_create_kanban_clients
10. 2026_01_26_090655_create_projects_kanban
11. 2026_01_26_090957_create_proposals_kanban
12. 2026_01_26_091010_create_contracts_kanban
13. 2026_01_26_091020_create_inspections_kanban
14. 2026_01_26_091040_create_working_papers_kanban
15. 2026_01_26_091104_create_reports_kanban
16. 2026_01_26_091119_create_approvals_kanban
17. 2026_01_26_091130_create_invoices_kanban
18. 2026_01_26_091147_create_documents_kanban
19. 2026_01_26_091157_create_activities_kanban
20. 2026_01_26_092746_create_notifications_table
21. 2026_01_29_081410_create_project_assets_kanban ← FK constraints ditambahkan di sini
```

### Kenapa FK di Migration Terakhir?

Migration `project_assets_kanban` dibuat paling akhir agar bisa:
1. Membuat tabel `project_assets_kanban`
2. Menambahkan FK constraints ke tabel lain yang sudah ada

Ini karena tabel seperti `inspections_kanban` dibuat sebelum `project_assets_kanban`, sehingga kolom `project_asset_id` dibuat dulu tanpa FK, lalu FK ditambahkan kemudian.

---

## 🌱 Seeders

### DatabaseSeeder.php
```php
$this->call([
    AppraisalKanbanSeeder::class,  // Clients + Projects + child data
    ProjectAssetSeeder::class,      // Assets per project
]);
```

### ProjectAssetSeeder.php
- Membuat 1-5 asset random per project
- Asset type dipilih acak
- Stage disesuaikan dengan stage project
- Alamat acak di area Jabodetabek
- Priority status: 10% critical, 15% warning, 75% normal

### Data Saat Ini (30 Jan 2026)
```
Projects: 11
Assets: 34
Clients: 6
Inspections: 7
Users: 7
```

---

## 🚀 Menjalankan Migration

### Development (Fresh)
```bash
php artisan migrate:fresh --seed
```

### Production (Incremental)
```bash
php artisan migrate
php artisan db:seed --class=ProjectAssetSeeder
```

---

## 📝 Perubahan dari Versi Sebelumnya

### Dihapus
- ❌ `admin_stage` - diganti dengan `current_stage` saja
- ❌ `global_status` - tidak diperlukan
- ❌ `contract_number` - dipindah ke tabel contracts
- ❌ `contract_value` - dipindah ke tabel contracts
- ❌ `ADMIN_STAGES` constant - diganti dengan `STAGES`

### Ditambahkan
- ✅ Tabel `project_assets_kanban`
- ✅ Kolom `project_asset_id` di tabel child
- ✅ Kolom `total_assets` di projects_kanban
- ✅ `ProjectAssetSeeder.php`
- ✅ `ProjectAssetController.php`
- ✅ Views untuk assets (kanban, list, create, edit, show)

---

## 🎯 Arsitektur Final

```
Dashboard
├── Kanban Proyek (Project Level)
│   ├── Drag & drop antar stage
│   ├── Filter: client, priority
│   └── Link ke detail project
│
├── Objek Penilaian (Asset Level) ← BARU!
│   ├── Kanban view dengan drag & drop
│   ├── List view dengan tabel
│   ├── Filter: project, type, priority
│   └── CRUD lengkap
│
├── Klien
├── Inspeksi
├── Invoice
└── Log Aktivitas
```

Dengan struktur ini:
1. **Admin** mengelola project di level kontrak/bisnis
2. **Technical team** mengelola asset di level operasional
3. Setiap asset progress-nya bisa di-track independen
4. Dashboard menampilkan overview keduanya
