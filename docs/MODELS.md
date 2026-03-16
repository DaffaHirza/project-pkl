# Models Documentation

Dokumentasi lengkap untuk semua model dalam sistem Kanban Asset Management.

**Lokasi:** `app/Models/`

---

## Daftar Model

| Model | Tabel | Deskripsi |
|-------|-------|-----------|
| User | users | Akun pengguna sistem |
| ClientKanban | clients_kanban | Data klien dengan hierarki |
| AssetKanban | assets_kanban | Asset yang dinilai |
| AssetDocumentKanban | asset_documents_kanban | Dokumen asset |
| AssetNoteKanban | asset_notes_kanban | Catatan/aktivitas asset |
| RecapitulationKanban | recapitulations_kanban | Rekapitulasi periodik |
| RecapitulationItemKanban | recapitulation_items_kanban | Detail item rekapitulasi |
| Notification | notifications | Notifikasi in-app |

---

## User.php

Model untuk akun pengguna sistem dengan role-based access.

### Traits
- `HasFactory` - Factory untuk testing
- `Notifiable` - Untuk Laravel Notifications (termasuk Telegram)

### Fillable
```php
'name', 'email', 'password', 'role', 'is_active', 'telegram_chat_id', 'last_login_at'
```

### Hidden
```php
'password', 'remember_token'
```

### Casts
```php
'email_verified_at' => 'datetime',
'password' => 'hashed',
'is_active' => 'boolean',
'last_login_at' => 'datetime',
```

### Constants

```php
// Role Types
ROLE_USER      = 'user'           // User biasa
ROLE_ADMIN     = 'admin'          // Admin
ROLE_SUPERUSER = 'superuser'      // Developer/Superuser

ROLES = [
    'user'      => 'User',
    'admin'     => 'Admin',
    'superuser' => 'Superuser (Developer)',
]
```

### Relationships
| Method | Return | Deskripsi |
|--------|--------|-----------|
| `uploadedDocuments()` | HasMany → AssetDocumentKanban | Dokumen yang diupload user |
| `assetNotes()` | HasMany → AssetNoteKanban | Catatan yang dibuat user |

### Methods
| Method | Return | Deskripsi |
|--------|--------|-----------|
| `isUser()` | bool | Cek apakah role = user |
| `isAdmin()` | bool | Cek apakah role = admin |
| `isSuperuser()` | bool | Cek apakah role = superuser |
| `hasAdminAccess()` | bool | Cek apakah admin ATAU superuser |
| `updateLastLogin()` | void | Update timestamp login terakhir |
| `routeNotificationForTelegram()` | string | Return telegram_chat_id untuk notif |
| `can($ability, $args)` | bool | Override: Superuser bypass semua permission |

### Accessors
| Attribute | Return | Deskripsi |
|-----------|--------|-----------|
| `role_name` | string | Label role dari ROLES constant |

### Scopes
| Scope | Deskripsi |
|-------|-----------|
| `active()` | Filter user dengan is_active = true |
| `admins()` | Filter admin dan superuser saja |

### Contoh Penggunaan
```php
// Cek akses admin
if ($user->hasAdminAccess()) {
    // Tampilkan menu admin
}

// Kirim notifikasi Telegram
$user->notify(new AssessmentUpdated($asset, 'stage_change', $actor));

// Get semua admin aktif
$admins = User::active()->admins()->get();
```

---

## ClientKanban.php

Model untuk data klien dengan struktur hierarki parent-child.

### Traits
- `HasFactory`

### Fillable
```php
'name', 'company_name', 'spk_number', 'type', 'parent_id'
```

### Constants

```php
TYPES = [
    'bank'    => 'Bank/Perbankan',    // Bank yang memiliki debitur
    'pt_cv'   => 'PT/CV',             // Perusahaan (bisa induk/anak)
    'debitur' => 'Debitur',           // Debitur dari bank
]
```

### Struktur Hierarki

```
Bank (parent_id: null)
└── Debitur 1 (parent_id: bank_id)
    └── Asset A
└── Debitur 2 (parent_id: bank_id)
    └── Asset B

PT Induk (parent_id: null)
└── PT Anak (parent_id: pt_induk_id)
    └── Asset C
```

### Relationships
| Method | Return | Deskripsi |
|--------|--------|-----------|
| `assets()` | HasMany → AssetKanban | Asset milik client ini |
| `parent()` | BelongsTo → ClientKanban | Parent client |
| `children()` | HasMany → ClientKanban | Semua child clients |
| `debiturs()` | HasMany (filtered) | Khusus debitur (jika ini bank) |
| `childCompanies()` | HasMany (filtered) | Khusus PT/CV anak |

### Accessors
| Attribute | Return | Deskripsi |
|-----------|--------|-----------|
| `display_name` | string | Format "Nama (Perusahaan)" atau "Nama" |
| `total_assets_count` | int | Jumlah asset + asset dari children |
| `type_name` | string | Label tipe dari TYPES constant |
| `children_count` | int | Jumlah child clients |

### Contoh Penggunaan
```php
// Mendapatkan semua debitur dari bank
$bank = ClientKanban::find($id);
$debiturs = $bank->debiturs()->with('assets')->get();

// Total asset termasuk dari debitur/anak
$totalAssets = $client->total_assets_count;

// Display nama lengkap
echo $client->display_name; // "PT ABC (Bank XYZ)"
```

---

## AssetKanban.php

Model utama untuk asset yang dinilai, dengan 13 tahap workflow Kanban.

### Traits
- `HasFactory`
- `SoftDeletes` - Soft delete untuk asset

### Fillable
```php
'client_id', 'name', 'asset_type', 'location', 'current_stage', 'priority', 'position'
```

### Casts
```php
'current_stage' => 'integer',
'position' => 'integer',
```

### Constants

```php
// 13 Stages Workflow
STAGES = [
    1  => 'Inisiasi',
    2  => 'Penawaran',
    3  => 'Kesepakatan',
    4  => 'Eksekusi Lapangan',
    5  => 'Analisis',
    6  => 'Review 1',
    7  => 'Draft Resume',
    8  => 'Approval Klien',
    9  => 'Draft Laporan',
    10 => 'Review 2',
    11 => 'Finalisasi',
    12 => 'Delivery & Payment',
    13 => 'Arsip',
]

// Tipe Asset
ASSET_TYPES = [
    'tanah'            => 'Tanah',
    'bangunan'         => 'Bangunan',
    'tanah_bangunan'   => 'Tanah & Bangunan',
    'mesin'            => 'Mesin & Peralatan',
    'kendaraan'        => 'Kendaraan',
    'inventaris'       => 'Inventaris',
    'aset_tak_berwujud'=> 'Aset Tak Berwujud',
    'lainnya'          => 'Lainnya',
]

// Prioritas
PRIORITIES = [
    'normal'   => 'Normal',
    'warning'  => 'Warning',
    'critical' => 'Critical',
]
```

### Relationships
| Method | Return | Deskripsi |
|--------|--------|-----------|
| `client()` | BelongsTo → ClientKanban | Client pemilik asset |
| `documents()` | HasMany → AssetDocumentKanban | Dokumen asset |
| `notes()` | HasMany → AssetNoteKanban | Catatan/log asset |

### Accessors
| Attribute | Return | Deskripsi |
|-----------|--------|-----------|
| `stage_label` | string | Nama stage dari STAGES |
| `asset_type_label` | string | Label tipe dari ASSET_TYPES |
| `priority_label` | string | Label prioritas |
| `progress` | int | Persentase progress (0-100) |
| `bank` | ClientKanban\|null | Bank jika asset milik debitur |

### Stage Methods
| Method | Return | Deskripsi |
|--------|--------|-----------|
| `moveToStage($stage, $userId, $note)` | bool | Pindah ke stage tertentu |
| `moveToNextStage($userId, $note)` | bool | Pindah ke stage berikutnya |
| `moveToPreviousStage($userId, $note)` | bool | Pindah ke stage sebelumnya |
| `isCompleted()` | bool | Cek apakah sudah di stage 13 |

### Scopes
| Scope | Deskripsi |
|-------|-----------|
| `atStage($stage)` | Filter by stage tertentu |
| `completed()` | Asset di stage 13 (Arsip) |
| `active()` | Asset belum selesai (stage < 13) |
| `priority($priority)` | Filter by prioritas |
| `needsAttention()` | Asset warning/critical yang aktif |
| `ordered()` | Order by position |

### Contoh Penggunaan
```php
// Pindahkan asset ke stage berikutnya
$asset->moveToNextStage(auth()->id(), 'Dokumen lengkap');

// Get asset yang perlu perhatian
$urgent = AssetKanban::needsAttention()->with('client')->get();

// Progress percentage
echo "Progress: {$asset->progress}%"; // "Progress: 38%"

// Get asset per stage untuk kanban board
foreach (AssetKanban::STAGES as $num => $label) {
    $assets = AssetKanban::atStage($num)->ordered()->get();
}
```

---

## AssetDocumentKanban.php

Model untuk dokumen yang diupload pada asset.

### Traits
- `HasFactory`

### Fillable
```php
'asset_id', 'uploaded_by', 'stage', 'file_name', 'file_path', 'file_type', 'file_size', 'description'
```

### Casts
```php
'stage' => 'integer',
'file_size' => 'integer',
```

### Constants

```php
MAX_FILE_SIZE = 104857600      // 100MB dalam bytes

ALLOWED_TYPES = [
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',  // Dokumen
    'jpg', 'jpeg', 'png', 'gif', 'webp',                  // Gambar
    'zip', 'rar', '7z',                                   // Arsip
    'txt', 'csv',                                         // Text
]
```

### Boot Events
```php
// Otomatis hapus file dari storage saat model dihapus
static::deleting(function ($document) {
    $document->deleteFile();
});
```

### Relationships
| Method | Return | Deskripsi |
|--------|--------|-----------|
| `asset()` | BelongsTo → AssetKanban | Asset pemilik dokumen |
| `uploader()` | BelongsTo → User | User yang upload |

### Accessors
| Attribute | Return | Deskripsi |
|-----------|--------|-----------|
| `stage_label` | string | Nama stage dari AssetKanban::STAGES |
| `file_size_human` | string | Ukuran format human (KB/MB) |
| `file_url` | string | URL publik untuk akses file |

### Methods
| Method | Return | Deskripsi |
|--------|--------|-----------|
| `isImage()` | bool | Cek apakah file adalah gambar |
| `isPdf()` | bool | Cek apakah file adalah PDF |
| `deleteFile()` | bool | Hapus file dari storage |
| `getDownloadResponse()` | Response | Download response untuk controller |

### Scopes
| Scope | Deskripsi |
|-------|-----------|
| `atStage($stage)` | Filter dokumen by stage |
| `images()` | Hanya file gambar |
| `documents()` | Hanya file non-gambar |

### Contoh Penggunaan
```php
// Upload dokumen
$document = AssetDocumentKanban::create([
    'asset_id' => $asset->id,
    'uploaded_by' => auth()->id(),
    'stage' => $asset->current_stage,
    'file_name' => $file->getClientOriginalName(),
    'file_path' => $path,
    'file_type' => $file->extension(),
    'file_size' => $file->getSize(),
]);

// Get dokumen gambar saja
$images = $asset->documents()->images()->get();

// Display ukuran file
echo $document->file_size_human; // "2.5 MB"
```

---

## AssetNoteKanban.php

Model untuk catatan dan log aktivitas pada asset.

### Traits
- `HasFactory`

### Fillable
```php
'asset_id', 'user_id', 'stage', 'type', 'content'
```

### Casts
```php
'stage' => 'integer',
```

### Constants

```php
TYPES = [
    'note'         => 'Catatan',          // Catatan manual dari user
    'stage_change' => 'Perubahan Stage',  // Otomatis saat pindah stage
    'approval'     => 'Approval',         // Persetujuan
    'rejection'    => 'Penolakan',        // Penolakan/hambatan (untuk status "Terhambat")
]
```

### Relationships
| Method | Return | Deskripsi |
|--------|--------|-----------|
| `asset()` | BelongsTo → AssetKanban | Asset terkait |
| `user()` | BelongsTo → User | User pembuat catatan |

### Accessors
| Attribute | Return | Deskripsi |
|-----------|--------|-----------|
| `stage_label` | string | Nama stage |
| `type_label` | string | Label tipe dari TYPES |

### Scopes
| Scope | Deskripsi |
|-------|-----------|
| `atStage($stage)` | Filter by stage |
| `ofType($type)` | Filter by tipe |
| `notesOnly()` | Hanya catatan manual (type = note) |
| `activityLog()` | Log aktivitas (stage_change, approval, rejection) |

### Contoh Penggunaan
```php
// Tambah catatan manual
AssetNoteKanban::create([
    'asset_id' => $asset->id,
    'user_id' => auth()->id(),
    'stage' => $asset->current_stage,
    'type' => 'note',
    'content' => 'Dokumen sudah diterima dari klien',
]);

// Get activity log
$activities = $asset->notes()->activityLog()->latest()->get();

// Cek ada hambatan (untuk status "Terhambat" di rekapitulasi)
$hasBlocker = $asset->notes()->ofType('rejection')->exists();
```

---

## RecapitulationKanban.php

Model untuk rekapitulasi periodik pekerjaan.

### Fillable
```php
'title', 'period_start', 'period_end', 'summary', 'status', 'created_by', 'published_at'
```

### Casts
```php
'period_start' => 'date',
'period_end' => 'date',
'published_at' => 'datetime',
```

### Constants

```php
STATUS_DRAFT     = 'draft'
STATUS_PUBLISHED = 'published'

STATUSES = [
    'draft'     => 'Draft',
    'published' => 'Dipublikasikan',
]
```

### Relationships
| Method | Return | Deskripsi |
|--------|--------|-----------|
| `creator()` | BelongsTo → User | User pembuat |
| `items()` | HasMany → RecapitulationItemKanban | Detail item rekapitulasi |

### Accessors
| Attribute | Return | Deskripsi |
|-----------|--------|-----------|
| `status_label` | string | Label status |
| `period_label` | string | Format "01 Jan 2026 - 07 Jan 2026" |
| `duration_days` | int | Jumlah hari periode |
| `progress_summary` | array | Ringkasan progress items |
| `completion_rate` | float | Persentase item completed |

### Methods
| Method | Return | Deskripsi |
|--------|--------|-----------|
| `publish()` | bool | Set status ke published |
| `unpublish()` | bool | Set status ke draft |
| `isPublished()` | bool | Cek apakah sudah published |
| `generateTitle($start, $end)` | string | Generate judul otomatis |

### Scopes
| Scope | Deskripsi |
|-------|-----------|
| `published()` | Hanya yang dipublikasikan |
| `draft()` | Hanya draft |
| `forPeriod($date)` | Filter yang mencakup tanggal tertentu |
| `recent($limit)` | Terbaru, limit default 10 |

### Contoh Penggunaan
```php
// Buat rekapitulasi mingguan
$recap = RecapitulationKanban::create([
    'title' => 'Rekapitulasi 1-7 Jan 2026',
    'period_start' => '2026-01-01',
    'period_end' => '2026-01-07',
    'status' => 'draft',
    'created_by' => auth()->id(),
]);

// Get progress summary
$summary = $recap->progress_summary;
// ['total' => 10, 'completed' => 5, 'in_progress' => 3, ...]

// Publikasikan
$recap->publish();
```

---

## RecapitulationItemKanban.php

Model untuk detail item dalam rekapitulasi.

### Fillable
```php
'recapitulation_id', 'asset_id', 'stage_start', 'stage_end', 'work_status', 'activities', 'notes', 'next_actions'
```

### Casts
```php
'stage_start' => 'integer',
'stage_end' => 'integer',
```

### Constants

```php
// Status Pekerjaan
STATUS_NOT_STARTED    = 'not_started'
STATUS_IN_PROGRESS    = 'in_progress'
STATUS_COMPLETED      = 'completed'
STATUS_BLOCKED        = 'blocked'
STATUS_PENDING_REVIEW = 'pending_review'

WORK_STATUSES = [
    'not_started'    => 'Belum Dikerjakan',
    'in_progress'    => 'Sedang Dikerjakan',
    'completed'      => 'Selesai',
    'blocked'        => 'Terhambat',
    'pending_review' => 'Menunggu Review',
]

WORK_STATUS_COLORS = [
    'not_started'    => 'gray',
    'in_progress'    => 'blue',
    'completed'      => 'green',
    'blocked'        => 'red',
    'pending_review' => 'yellow',
]
```

### Relationships
| Method | Return | Deskripsi |
|--------|--------|-----------|
| `recapitulation()` | BelongsTo → RecapitulationKanban | Rekapitulasi induk |
| `asset()` | BelongsTo → AssetKanban | Asset terkait |

### Accessors
| Attribute | Return | Deskripsi |
|-----------|--------|-----------|
| `work_status_label` | string | Label status |
| `work_status_color` | string | Warna untuk UI |
| `stage_start_label` | string | Nama stage awal |
| `stage_end_label` | string | Nama stage akhir |
| `stage_progress` | int | Jumlah stage yang dilalui |
| `has_progress` | bool | Ada progress (stage_end > stage_start) |
| `progress_percentage` | float | Persentase progress |

### Contoh Penggunaan
```php
// Tambah item ke rekapitulasi
RecapitulationItemKanban::create([
    'recapitulation_id' => $recap->id,
    'asset_id' => $asset->id,
    'stage_start' => 3,
    'stage_end' => 5,
    'work_status' => 'in_progress',
    'activities' => 'Survey lapangan, pengumpulan data',
    'notes' => 'Menunggu dokumen dari klien',
]);

// Cek apakah ada progress
if ($item->has_progress) {
    echo "Progress: {$item->stage_progress} stage";
}
```

---

## Notification.php

Model untuk notifikasi in-app dengan UUID.

### Traits
- `HasUuids` - Menggunakan UUID sebagai primary key

### Primary Key
```php
protected $keyType = 'string';
public $incrementing = false;
```

### Fillable
```php
'id', 'type', 'notifiable_type', 'notifiable_id', 'data', 'read_at'
```

### Casts
```php
'data' => 'array',      // JSON data
'read_at' => 'datetime',
```

### Constants

```php
// Tipe Notifikasi (hanya yang digunakan)
TYPES = [
    // Asset
    'asset_stage_changed'      => 'Stage Asset Berubah',
    'asset_created'            => 'Asset Baru Dibuat',
    'asset_document_uploaded'  => 'Dokumen Asset Diupload',
    'asset_note_added'         => 'Catatan Asset Ditambahkan',
    'asset_priority_critical'  => 'Asset Priority Critical',
    
    // Client
    'client_created'           => 'Client Baru Ditambahkan',
    
    // System
    'system'                   => 'Sistem',
]
```

### Icon & Color Mapping
| Type | Icon | Color |
|------|------|-------|
| asset_stage_changed | git-branch | purple |
| asset_created | plus-circle | green |
| asset_document_uploaded | upload | blue |
| asset_note_added | message-circle | gray |
| asset_priority_critical | alert-triangle | red |
| client_created | users | blue |
| system | info | gray |

### Relationships
| Method | Return | Deskripsi |
|--------|--------|-----------|
| `notifiable()` | MorphTo | User/entity penerima notifikasi |

### Accessors
| Attribute | Return | Deskripsi |
|-----------|--------|-----------|
| `title` | string | Judul dari data atau TYPES |
| `message` | string | Pesan dari data |
| `icon` | string | Nama icon berdasarkan type |
| `color` | string | Warna berdasarkan type |
| `action_url` | string\|null | URL aksi dari data |

### Methods
| Method | Return | Deskripsi |
|--------|--------|-----------|
| `isRead()` | bool | Sudah dibaca? |
| `isUnread()` | bool | Belum dibaca? |
| `markAsRead()` | void | Tandai sudah dibaca |
| `markAsUnread()` | void | Tandai belum dibaca |
| `notify($user, $type, $data)` | Notification | Buat notifikasi untuk 1 user |
| `notifyMany($users, $type, $data)` | void | Buat notifikasi untuk banyak user |

### Scopes
| Scope | Deskripsi |
|-------|-----------|
| `unread()` | Notifikasi belum dibaca |
| `read()` | Notifikasi sudah dibaca |
| `recent()` | 30 hari terakhir |
| `ofType($type)` | Filter by tipe |

### Contoh Penggunaan
```php
// Buat notifikasi
Notification::notify($user, 'asset_created', [
    'title' => 'Asset Baru',
    'message' => 'Asset "Gedung A" telah dibuat',
    'action_url' => route('kanban.assets.show', $asset),
]);

// Buat notifikasi ke semua admin
$admins = User::admins()->get();
Notification::notifyMany($admins, 'asset_priority_critical', $data);

// Get notifikasi unread
$unread = Notification::where('notifiable_id', $user->id)
    ->unread()
    ->recent()
    ->get();

// Tandai semua sudah dibaca
Notification::where('notifiable_id', $user->id)
    ->unread()
    ->update(['read_at' => now()]);
```

---

## Diagram Relasi

```
┌─────────────────┐
│      User       │
├─────────────────┤
│ id              │
│ name            │
│ email           │
│ role            │
│ telegram_chat_id│
└────────┬────────┘
         │
         │ 1:N
         ▼
┌─────────────────┐      ┌─────────────────┐
│  Notification   │      │ AssetNoteKanban │
└─────────────────┘      └────────┬────────┘
                                  │
         ┌─────────────────┐      │ N:1
         │  ClientKanban   │◄─────┼──────────────┐
         ├─────────────────┤      │              │
         │ id              │      │              │
         │ name            │      │              │
         │ type            │      │              │
         │ parent_id ──────┼──┐   │              │
         └────────┬────────┘  │   │              │
                  │           │   │              │
                  │ 1:N       │   │              │
                  ▼           │   │              │
         ┌─────────────────┐  │   │              │
         │   AssetKanban   │◄─┘   │              │
         ├─────────────────┤      │              │
         │ id              │◄─────┘              │
         │ client_id       │                     │
         │ current_stage   │                     │
         │ priority        │                     │
         └────────┬────────┘                     │
                  │                              │
         ┌────────┴────────┐                     │
         │ 1:N             │ 1:N                 │
         ▼                 ▼                     │
┌─────────────────┐ ┌─────────────────┐          │
│AssetDocKanban   │ │AssetNoteKanban  │──────────┘
└─────────────────┘ └─────────────────┘

┌─────────────────┐      ┌─────────────────────┐
│RecapKanban      │─────▶│RecapItemKanban      │
│                 │ 1:N  │                     │
│ period_start    │      │ asset_id ──────────▶│ AssetKanban
│ period_end      │      │ work_status         │
└─────────────────┘      └─────────────────────┘
```

---

## Tips Performa

### Eager Loading
```php
// ❌ N+1 Problem
$assets = AssetKanban::all();
foreach ($assets as $asset) {
    echo $asset->client->name; // Query setiap iterasi
}

// ✅ Eager Loading
$assets = AssetKanban::with('client')->get();
foreach ($assets as $asset) {
    echo $asset->client->name; // Tidak ada query tambahan
}
```

### Select Specific Columns
```php
// ✅ Hanya ambil kolom yang diperlukan
$assets = AssetKanban::select('id', 'name', 'current_stage', 'client_id')
    ->with('client:id,name')
    ->get();
```

### Count vs Load
```php
// ❌ Load semua lalu count
$count = $client->assets->count();

// ✅ Count langsung di database
$count = $client->assets()->count();
```

**Method:**

- isRead(), isUnread() - Cek status baca
- markAsRead() - Tandai sudah dibaca
- markAsUnread() - Tandai belum dibaca
- notify(user, type, data) - Buat notifikasi baru (static)

**Accessor:**

- title - Judul dari data atau TYPES
- message - Pesan dari data
- icon - Icon berdasarkan type
- color - Warna berdasarkan type
- action_url - URL aksi dari data

**Scope:**

- unread() - Filter belum dibaca
- read() - Filter sudah dibaca
- recent() - 30 hari terakhir
- ofType(type) - Filter by type

## RecapitulationKanban.php

Model untuk rekapitulasi progress mingguan.

**Fillable:** title, period_start, period_end, summary, status, created_by, published_at

**Status constants:**

- STATUS_DRAFT = 'draft'
- STATUS_PUBLISHED = 'published'

**Relasi:**

- creator() - User pembuat rekapitulasi
- items() - Item-item pekerjaan

**Method:**

- publish() - Publikasikan rekapitulasi
- unpublish() - Kembalikan ke draft
- generateTitle() - Generate judul otomatis berdasarkan periode
- getSuggestedPeriod() - Dapatkan saran periode (7-14 hari dari hari ini)

**Accessor:**

- status_label - Label status (Draft/Dipublikasikan)
- period_label - Format "DD MMM - DD MMM YYYY"
- duration_days - Jumlah hari dalam periode
- progress_summary - Ringkasan progress (X dari Y selesai)
- completion_rate - Persentase penyelesaian

**Scope:**

- published() - Filter dipublikasikan
- draft() - Filter draft
- inPeriod(start, end) - Filter by periode

## RecapitulationItemKanban.php

Model untuk item pekerjaan dalam rekapitulasi.

**Fillable:** recapitulation_id, asset_id, stage_start, stage_end, work_status, activities, notes, next_actions

**Work Status constants:**

- not_started - Belum Dimulai
- in_progress - Dalam Proses
- completed - Selesai
- blocked - Terhambat
- pending_review - Menunggu Review

**WORK_STATUS_COLORS:**

- not_started - gray
- in_progress - blue
- completed - green
- blocked - red
- pending_review - yellow

**Relasi:**

- recapitulation() - Rekapitulasi parent
- asset() - Asset terkait

**Method:**

- generateActivitiesFromNotes(periodStart, periodEnd) - Generate aktivitas dari catatan dalam periode
- determineWorkStatus() - Tentukan status otomatis berdasarkan:
  - `completed` jika stage_end >= 13 (Arsip)
  - `pending_review` jika stage_end adalah 6 (Review 1) atau 10 (Review 2)
  - `blocked` jika asset memiliki catatan tipe `rejection` dalam 14 hari terakhir
  - `in_progress` jika stage_end > stage_start
  - `not_started` jika tidak ada perubahan stage

**Accessor:**

- work_status_label - Label status in Indonesian
- work_status_color - Warna untuk badge
- stage_start_label - Label stage awal
- stage_end_label - Label stage akhir
- stage_progress - Jumlah stage yang dilalui

**Cast:**

- activities => array (JSON)
