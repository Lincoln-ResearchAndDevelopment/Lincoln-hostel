<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\SessionManagementService;

class MonitorSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:monitor {--cleanup : Clean up orphaned session contexts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor active sessions and session contexts for debugging';

    protected $sessionService;

    public function __construct(SessionManagementService $sessionService)
    {
        parent::__construct();
        $this->sessionService = $sessionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Session Monitoring Report');
        $this->newLine();

        // Get active sessions from database
        $activeSessions = DB::table('sessions')
            ->select('id', 'user_id', 'ip_address', 'user_agent', 'last_activity', 'payload')
            ->orderBy('last_activity', 'desc')
            ->get();

        $this->info("📊 Total Active Sessions: {$activeSessions->count()}");
        $this->newLine();

        if ($activeSessions->count() > 0) {
            $this->table(
                ['Session ID', 'User ID', 'IP Address', 'Last Activity', 'Contexts'],
                $activeSessions->map(function ($session) {
                    $payload = $this->unserializePayload($session->payload);
                    $contexts = [];
                    
                    // Check for auth contexts
                    foreach (['admin', 'student', 'superadmin'] as $context) {
                        if (isset($payload["auth_context_{$context}"])) {
                            $contextData = $payload["auth_context_{$context}"];
                            $contexts[] = "{$context}:{$contextData['user_id']}";
                        }
                    }
                    
                    return [
                        substr($session->id, 0, 8) . '...',
                        $session->user_id ?? 'N/A',
                        $session->ip_address,
                        date('Y-m-d H:i:s', $session->last_activity),
                        implode(', ', $contexts) ?: 'None'
                    ];
                })->toArray()
            );
        }

        // Check for session inconsistencies
        $this->newLine();
        $this->info('🔍 Checking for Session Inconsistencies...');
        
        $inconsistencies = 0;
        foreach ($activeSessions as $session) {
            $payload = $this->unserializePayload($session->payload);
            
            // Check for multiple auth contexts in same session
            $authContexts = [];
            foreach (['admin', 'student', 'superadmin'] as $context) {
                if (isset($payload["auth_context_{$context}"])) {
                    $authContexts[] = $context;
                }
            }
            
            if (count($authContexts) > 1) {
                $this->warn("⚠️  Session {$session->id} has multiple auth contexts: " . implode(', ', $authContexts));
                $inconsistencies++;
            }
        }

        if ($inconsistencies === 0) {
            $this->info('✅ No session inconsistencies found');
        } else {
            $this->warn("⚠️  Found {$inconsistencies} session inconsistencies");
        }

        // Cleanup option
        if ($this->option('cleanup')) {
            $this->newLine();
            $this->info('🧹 Cleaning up orphaned session contexts...');
            
            $cleaned = 0;
            foreach ($activeSessions as $session) {
                $payload = $this->unserializePayload($session->payload);
                $modified = false;
                
                // Remove orphaned contexts (contexts without valid Laravel auth)
                foreach (['admin', 'student', 'superadmin'] as $context) {
                    if (isset($payload["auth_context_{$context}"])) {
                        $contextData = $payload["auth_context_{$context}"];
                        $guard = $contextData['guard'];
                        
                        // This is a simplified check - in real cleanup, you'd verify against auth tables
                        if (!isset($payload["login_{$guard}_" . hash('sha256', $guard)])) {
                            unset($payload["auth_context_{$context}"]);
                            $modified = true;
                            $cleaned++;
                        }
                    }
                }
                
                if ($modified) {
                    DB::table('sessions')
                        ->where('id', $session->id)
                        ->update(['payload' => $this->serializePayload($payload)]);
                }
            }
            
            $this->info("✅ Cleaned up {$cleaned} orphaned session contexts");
        }

        $this->newLine();
        $this->info('📋 Session Monitoring Complete');
        
        return Command::SUCCESS;
    }

    /**
     * Unserialize the session payload, handles both encrypted and unencrypted sessions
     */
    private function unserializePayload(string $payloadRaw)
    {
        try {
            if (config('session.encrypt')) {
                return unserialize(decrypt($payloadRaw));
            }
            
            return unserialize(base64_decode($payloadRaw));
        } catch (\Exception $e) {
            // Fallback: try the opposite method if it fails
            try {
                if (config('session.encrypt')) {
                    return unserialize(base64_decode($payloadRaw));
                } else {
                    return unserialize(decrypt($payloadRaw));
                }
            } catch (\Exception $ex) {
                return [];
            }
        }
    }

    /**
     * Serialize the session payload, handles both encrypted and unencrypted sessions
     */
    private function serializePayload($payload): string
    {
        if (config('session.encrypt')) {
            return encrypt(serialize($payload));
        }
        
        return base64_encode(serialize($payload));
    }
}
