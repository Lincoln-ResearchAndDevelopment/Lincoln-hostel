<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BedAssignmentService;

class SyncBedOccupancy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'beds:sync-occupancy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync bed occupancy status with actual student assignments';

    protected $bedService;

    public function __construct(BedAssignmentService $bedService)
    {
        parent::__construct();
        $this->bedService = $bedService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting bed occupancy sync...');
        $this->newLine();

        $result = $this->bedService->syncBedOccupancy();

        $this->info("✓ Sync completed successfully!");
        $this->info("  Fixed records: {$result['fixed']}");
        
        if (count($result['errors']) > 0) {
            $this->warn("  Errors encountered: " . count($result['errors']));
            foreach ($result['errors'] as $error) {
                $this->error("  - {$error}");
            }
        }

        $this->newLine();
        return Command::SUCCESS;
    }
}
