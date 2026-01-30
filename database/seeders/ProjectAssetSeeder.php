<?php

namespace Database\Seeders;

use App\Models\ProjectKanban;
use App\Models\ProjectAsset;
use Illuminate\Database\Seeder;

class ProjectAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing projects or create sample ones
        $projects = ProjectKanban::all();

        if ($projects->isEmpty()) {
            $this->command->info('No projects found. Please run AppraisalKanbanSeeder first.');
            return;
        }

        $assetTypes = array_keys(ProjectAsset::ASSET_TYPES);
        $stages = array_keys(ProjectAsset::STAGES);

        foreach ($projects as $project) {
            // Skip if project already has assets
            if ($project->assets()->count() > 0) {
                continue;
            }

            // Create 1-5 random assets per project
            $assetCount = rand(1, 5);

            for ($i = 1; $i <= $assetCount; $i++) {
                $assetType = $assetTypes[array_rand($assetTypes)];
                $stage = $stages[array_rand($stages)];
                
                // Make stage consistent with project stage
                if ($project->current_stage === 'lead' || $project->current_stage === 'proposal') {
                    $stage = 'pending'; // Not yet started
                } elseif ($project->current_stage === 'done') {
                    $stage = 'done'; // All complete
                }

                $asset = ProjectAsset::create([
                    'project_id' => $project->id,
                    'name' => $this->generateAssetName($assetType, $i),
                    'description' => $this->generateDescription($assetType),
                    'asset_type' => $assetType,
                    'location_address' => $this->generateAddress(),
                    'current_stage' => $stage,
                    'priority_status' => $this->randomPriority(),
                    'position' => $i,
                    'target_completion_date' => now()->addDays(rand(7, 60)),
                    'notes' => $stage !== 'pending' ? 'Objek sedang dalam proses penilaian.' : null,
                ]);

                $this->command->info("Created asset: {$asset->asset_code} - {$asset->name}");
            }

            // Update project's total_assets count
            $project->update(['total_assets' => $assetCount]);
        }

        $this->command->info('Project assets seeding completed.');
    }

    private function generateAssetName(string $type, int $index): string
    {
        $names = [
            'tanah' => ['Tanah Kavling', 'Tanah Produktif', 'Tanah Industri', 'Tanah Perkebunan'],
            'bangunan' => ['Gedung Kantor', 'Gudang', 'Ruko', 'Pabrik', 'Workshop'],
            'tanah_bangunan' => ['Kantor Pusat', 'Pabrik & Lahan', 'Ruko 3 Lantai', 'Komplek Pergudangan'],
            'mesin' => ['Mesin Produksi', 'Mesin CNC', 'Genset', 'Kompressor', 'Forklift'],
            'kendaraan' => ['Truk Hino', 'Mobil Operasional', 'Dump Truck', 'Excavator', 'Crane'],
            'inventaris' => ['Furniture Kantor', 'Peralatan IT', 'AC Central', 'Sistem Keamanan'],
            'aset_tak_berwujud' => ['Hak Paten', 'Trademark', 'Software License', 'Goodwill'],
            'lainnya' => ['Aset Lainnya', 'Peralatan Khusus', 'Instalasi', 'Jaringan Utilitas'],
        ];

        $options = $names[$type] ?? $names['lainnya'];
        return $options[array_rand($options)] . ' #' . $index;
    }

    private function generateDescription(string $type): string
    {
        $descriptions = [
            'tanah' => 'Tanah dengan sertifikat SHM/SHGB, akses jalan baik.',
            'bangunan' => 'Bangunan permanen dengan konstruksi beton bertulang.',
            'tanah_bangunan' => 'Properti lengkap dengan tanah dan bangunan di atasnya.',
            'mesin' => 'Mesin produksi dengan kondisi operasional baik.',
            'kendaraan' => 'Kendaraan operasional dengan dokumen lengkap.',
            'inventaris' => 'Inventaris kantor dalam kondisi baik.',
            'aset_tak_berwujud' => 'Aset tak berwujud dengan dokumen legal lengkap.',
            'lainnya' => 'Aset dengan karakteristik khusus.',
        ];

        return $descriptions[$type] ?? $descriptions['lainnya'];
    }

    private function generateAddress(): string
    {
        $streets = ['Jl. Sudirman', 'Jl. Gatot Subroto', 'Jl. HR Rasuna Said', 'Jl. Kuningan', 'Jl. TB Simatupang'];
        $cities = ['Jakarta Selatan', 'Jakarta Pusat', 'Tangerang', 'Bekasi', 'Depok', 'Bogor'];
        
        return $streets[array_rand($streets)] . ' No. ' . rand(1, 200) . ', ' . $cities[array_rand($cities)];
    }

    private function randomPriority(): string
    {
        $rand = rand(1, 100);
        if ($rand <= 10) return 'critical';
        if ($rand <= 25) return 'warning';
        return 'normal';
    }
}
