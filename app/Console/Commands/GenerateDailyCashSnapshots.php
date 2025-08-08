<?php

namespace App\Console\Commands;

use App\Models\Outlet;
use App\Services\CashBalanceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateDailyCashSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cash:generate-snapshots 
                            {--date= : Generate snapshots for specific date (YYYY-MM-DD)}
                            {--outlet= : Generate snapshot for specific outlet ID}
                            {--days=1 : Generate snapshots for last N days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily cash balance snapshots for all outlets';

    protected $cashBalanceService;

    public function __construct(CashBalanceService $cashBalanceService)
    {
        parent::__construct();
        $this->cashBalanceService = $cashBalanceService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting daily cash snapshots generation...');
        
        // Get parameters
        $specificDate = $this->option('date');
        $specificOutlet = $this->option('outlet');
        $days = (int) $this->option('days');
        
        // Get outlets to process
        $outlets = $specificOutlet 
            ? Outlet::where('id', $specificOutlet)->get()
            : Outlet::all();
            
        if ($outlets->isEmpty()) {
            $this->error('No outlets found to process');
            return;
        }

        // Get dates to process
        $dates = $this->getDatesToProcess($specificDate, $days);
        
        $this->info("Processing {$outlets->count()} outlets for " . count($dates) . " date(s)");
        
        $progressBar = $this->output->createProgressBar($outlets->count() * count($dates));
        $progressBar->start();
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($outlets as $outlet) {
            foreach ($dates as $date) {
                try {
                    $snapshot = $this->cashBalanceService->calculateDailySnapshot(
                        $outlet->id,
                        $date,
                        1 // System user ID
                    );
                    
                    $successCount++;
                    $progressBar->advance();
                    
                    Log::info("Daily snapshot generated", [
                        'outlet_id' => $outlet->id,
                        'outlet_name' => $outlet->name,
                        'date' => $date,
                        'closing_balance' => $snapshot->closing_balance
                    ]);
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Outlet {$outlet->id} - Date {$date}: " . $e->getMessage();
                    $progressBar->advance();
                    
                    Log::error("Failed to generate daily snapshot", [
                        'outlet_id' => $outlet->id,
                        'date' => $date,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        $progressBar->finish();
        $this->newLine();
        
        // Show results
        $this->info("Snapshot generation completed:");
        $this->info("✅ Success: {$successCount}");
        
        if ($errorCount > 0) {
            $this->error("❌ Errors: {$errorCount}");
            $this->newLine();
            $this->error("Error details:");
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
        }
        
        // Check for variances
        $this->checkForVariances($outlets, $dates);
        
        $this->info('Daily cash snapshots generation completed.');
    }
    
    /**
     * Get dates to process based on options
     */
    protected function getDatesToProcess($specificDate, $days): array
    {
        if ($specificDate) {
            return [Carbon::parse($specificDate)->format('Y-m-d')];
        }
        
        $dates = [];
        for ($i = 1; $i <= $days; $i++) {
            $dates[] = Carbon::now()->subDays($i)->format('Y-m-d');
        }
        
        return $dates;
    }
    
    /**
     * Check for variances yang perlu attention
     */
    protected function checkForVariances($outlets, $dates)
    {
        $this->info('Checking for unusual variances...');
        
        foreach ($outlets as $outlet) {
            foreach ($dates as $date) {
                try {
                    $snapshot = \App\Models\CashBalanceSnapshot::where('outlet_id', $outlet->id)
                        ->where('date', $date)
                        ->first();
                        
                    if (!$snapshot) continue;
                    
                    $netChange = $snapshot->net_change;
                    $transactionCount = $snapshot->transactions_count;
                    
                    // Flag unusual activities
                    $warnings = [];
                    
                    if ($netChange < -1000000) { // Large decrease > 1M
                        $warnings[] = "Large cash decrease: Rp " . number_format(abs($netChange));
                    }
                    
                    if ($netChange > 5000000) { // Large increase > 5M  
                        $warnings[] = "Large cash increase: Rp " . number_format($netChange);
                    }
                    
                    if ($transactionCount > 500) { // High transaction volume
                        $warnings[] = "High transaction volume: {$transactionCount} transactions";
                    }
                    
                    if ($transactionCount == 0 && abs($netChange) > 0) {
                        $warnings[] = "Balance change without transactions";
                    }
                    
                    if (!empty($warnings)) {
                        $this->warn("🚨 {$outlet->name} - {$date}:");
                        foreach ($warnings as $warning) {
                            $this->warn("  - {$warning}");
                        }
                        
                        Log::warning("Unusual cash activity detected", [
                            'outlet_id' => $outlet->id,
                            'outlet_name' => $outlet->name,
                            'date' => $date,
                            'warnings' => $warnings,
                            'net_change' => $netChange,
                            'transactions_count' => $transactionCount
                        ]);
                    }
                    
                } catch (\Exception $e) {
                    Log::error("Error checking variances", [
                        'outlet_id' => $outlet->id,
                        'date' => $date,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }
}
