<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailTrackingService;
use Illuminate\Support\Facades\DB;

class MonitorEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:monitor {--days=7 : Number of days to analyze} {--cleanup : Clean up old email logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor email delivery statistics and performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cleanup = $this->option('cleanup');
        
        $this->info("📧 EMAIL MONITORING REPORT - Last {$days} days");
        $this->line(str_repeat('=', 60));
        
        try {
            // Get overall statistics
            $stats = DB::table('email_logs')
                ->where('created_at', '>=', now()->subDays($days))
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
                    SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
                    SUM(CASE WHEN status = "attempting" THEN 1 ELSE 0 END) as attempting,
                    AVG(CASE WHEN status = "sent" THEN duration_ms ELSE NULL END) as avg_duration_ms
                ')
                ->first();
                
            if ($stats->total > 0) {
                $successRate = round(($stats->sent / $stats->total) * 100, 2);
                $failureRate = round(($stats->failed / $stats->total) * 100, 2);
                
                $this->info("📊 OVERALL STATISTICS:");
                $this->line("   Total Emails: {$stats->total}");
                $this->line("   ✅ Sent: {$stats->sent} ({$successRate}%)");
                $this->line("   ❌ Failed: {$stats->failed} ({$failureRate}%)");
                $this->line("   ⏳ Attempting: {$stats->attempting}");
                $this->line("   ⚡ Avg Duration: " . round($stats->avg_duration_ms, 2) . "ms");
                $this->line("");
                
                // Email type breakdown
                $this->info("📋 BY EMAIL TYPE:");
                $typeStats = DB::table('email_logs')
                    ->where('created_at', '>=', now()->subDays($days))
                    ->selectRaw('
                        email_type,
                        COUNT(*) as total,
                        SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
                        SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed
                    ')
                    ->groupBy('email_type')
                    ->orderBy('total', 'desc')
                    ->get();
                    
                foreach ($typeStats as $type) {
                    $typeSuccessRate = $type->total > 0 ? round(($type->sent / $type->total) * 100, 2) : 0;
                    $this->line("   {$type->email_type}: {$type->sent}/{$type->total} ({$typeSuccessRate}%)");
                }
                $this->line("");
                
                // Error analysis
                if ($stats->failed > 0) {
                    $this->warn("🚨 ERROR ANALYSIS:");
                    $errorStats = DB::table('email_logs')
                        ->where('created_at', '>=', now()->subDays($days))
                        ->where('status', 'failed')
                        ->selectRaw('
                            error_category,
                            COUNT(*) as count,
                            GROUP_CONCAT(DISTINCT SUBSTRING(error_message, 1, 50)) as sample_errors
                        ')
                        ->groupBy('error_category')
                        ->orderBy('count', 'desc')
                        ->get();
                        
                    foreach ($errorStats as $error) {
                        $this->line("   {$error->error_category}: {$error->count} failures");
                        if ($error->sample_errors) {
                            $this->line("     Sample: " . substr($error->sample_errors, 0, 80) . "...");
                        }
                    }
                    $this->line("");
                }
                
                // Recent failures
                $recentFailures = DB::table('email_logs')
                    ->where('status', 'failed')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['email_id', 'email_type', 'recipient', 'error_message', 'created_at']);
                    
                if ($recentFailures->count() > 0) {
                    $this->warn("🕐 RECENT FAILURES (Last 24h):");
                    foreach ($recentFailures as $failure) {
                        $this->line("   {$failure->created_at}: {$failure->email_type} to {$failure->recipient}");
                        $this->line("     Error: " . substr($failure->error_message, 0, 60) . "...");
                    }
                    $this->line("");
                }
                
            } else {
                $this->warn("No email logs found for the last {$days} days.");
            }
            
            // Cleanup old logs if requested
            if ($cleanup) {
                $this->info("🧹 CLEANING UP OLD EMAIL LOGS...");
                $deleted = DB::table('email_logs')
                    ->where('created_at', '<', now()->subDays(30))
                    ->delete();
                $this->info("   Deleted {$deleted} old email log entries.");
            }
            
        } catch (\Exception $e) {
            $this->error("Failed to generate email monitoring report: " . $e->getMessage());
            return 1;
        }
        
        $this->info("✅ Email monitoring report completed!");
        return 0;
    }
}
