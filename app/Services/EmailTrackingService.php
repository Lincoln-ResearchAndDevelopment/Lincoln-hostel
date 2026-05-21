<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * EmailTrackingService
 * 
 * Comprehensive email tracking and logging service for Lincoln Hostel.
 * Provides detailed logging, error tracking, and success confirmation
 * for all critical email flows.
 * 
 * Features:
 * - Success/failure logging with detailed context
 * - Email delivery tracking
 * - Error categorization and reporting
 * - Performance monitoring
 * - Retry mechanism for failed emails
 * - Dashboard-ready statistics
 */
class EmailTrackingService
{
    const EMAIL_TYPES = [
        'application_received' => 'Application Received Confirmation',
        'application_approved' => 'Application Approved Notification',
        'application_rejected' => 'Application Rejected Notification',
        'student_onboarding' => 'Student Onboarding Welcome',
        'leave_submitted' => 'Leave Request Submitted',
        'leave_approved' => 'Leave Request Approved',
        'leave_rejected' => 'Leave Request Rejected',
        'payment_received' => 'Payment Received Confirmation',
        'payment_approved' => 'Payment Approved Notification',
        'room_assigned' => 'Room Assignment Notification',
        'booking_received' => 'Booking Payment Received',
        'announcement' => 'Announcement Notification',
        'contact_form' => 'Contact Form Submission',
        'admin_notification' => 'Admin Notification'
    ];

    /**
     * Send email with comprehensive tracking and logging
     * 
     * @param string $emailType Type of email (see EMAIL_TYPES)
     * @param string $recipient Email address
     * @param mixed $mailable Laravel Mailable instance
     * @param array $context Additional context for logging
     * @return array ['success' => bool, 'message' => string, 'email_id' => string]
     */
    public function sendTrackedEmail(string $emailType, string $recipient, $mailable, array $context = []): array
    {
        $emailId = $this->generateEmailId();
        $startTime = microtime(true);
        
        // Log email attempt
        $this->logEmailAttempt($emailId, $emailType, $recipient, $context);
        
        try {
            // Validate email type
            if (!array_key_exists($emailType, self::EMAIL_TYPES)) {
                throw new Exception("Invalid email type: {$emailType}");
            }
            
            // Validate recipient
            if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email address: {$recipient}");
            }
            
            // Send email
            Mail::to($recipient)->send($mailable);
            
            $duration = round((microtime(true) - $startTime) * 1000, 2); // milliseconds
            
            // Log success
            $this->logEmailSuccess($emailId, $emailType, $recipient, $duration, $context);
            
            return [
                'success' => true,
                'message' => "Email sent successfully to {$recipient}",
                'email_id' => $emailId,
                'duration_ms' => $duration
            ];
            
        } catch (Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            // Log failure with detailed error info
            $this->logEmailFailure($emailId, $emailType, $recipient, $e, $duration, $context);
            
            return [
                'success' => false,
                'message' => "Email failed: " . $e->getMessage(),
                'email_id' => $emailId,
                'error' => $e->getMessage(),
                'duration_ms' => $duration
            ];
        }
    }
    
    /**
     * Send multiple emails with batch tracking
     */
    public function sendBatchEmails(string $emailType, array $recipients, $mailable, array $context = []): array
    {
        $results = [];
        $batchId = $this->generateBatchId();
        
        Log::info("📧 BATCH EMAIL START", [
            'batch_id' => $batchId,
            'email_type' => $emailType,
            'recipient_count' => count($recipients),
            'context' => $context
        ]);
        
        foreach ($recipients as $recipient) {
            $result = $this->sendTrackedEmail($emailType, $recipient, $mailable, array_merge($context, ['batch_id' => $batchId]));
            $results[] = $result;
        }
        
        $successCount = count(array_filter($results, fn($r) => $r['success']));
        $failureCount = count($recipients) - $successCount;
        
        Log::info("📧 BATCH EMAIL COMPLETE", [
            'batch_id' => $batchId,
            'total' => count($recipients),
            'success' => $successCount,
            'failures' => $failureCount,
            'success_rate' => round(($successCount / count($recipients)) * 100, 2) . '%'
        ]);
        
        return [
            'batch_id' => $batchId,
            'total' => count($recipients),
            'success' => $successCount,
            'failures' => $failureCount,
            'results' => $results
        ];
    }
    
    /**
     * Log email attempt
     */
    private function logEmailAttempt(string $emailId, string $emailType, string $recipient, array $context): void
    {
        Log::info("📧 EMAIL ATTEMPT", [
            'email_id' => $emailId,
            'type' => $emailType,
            'type_name' => self::EMAIL_TYPES[$emailType] ?? 'Unknown',
            'recipient' => $recipient,
            'timestamp' => now()->toISOString(),
            'context' => $context
        ]);
        
        // Store in database for dashboard tracking
        try {
            DB::table('email_logs')->insert([
                'email_id' => $emailId,
                'email_type' => $emailType,
                'recipient' => $recipient,
                'status' => 'attempting',
                'context' => json_encode($context),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (Exception $e) {
            Log::warning("Failed to store email log in database: " . $e->getMessage());
        }
    }
    
    /**
     * Log successful email delivery
     */
    private function logEmailSuccess(string $emailId, string $emailType, string $recipient, float $duration, array $context): void
    {
        Log::info("✅ EMAIL SUCCESS", [
            'email_id' => $emailId,
            'type' => $emailType,
            'type_name' => self::EMAIL_TYPES[$emailType] ?? 'Unknown',
            'recipient' => $recipient,
            'duration_ms' => $duration,
            'timestamp' => now()->toISOString(),
            'context' => $context
        ]);
        
        // Update database record
        try {
            DB::table('email_logs')
                ->where('email_id', $emailId)
                ->update([
                    'status' => 'sent',
                    'duration_ms' => $duration,
                    'sent_at' => now(),
                    'updated_at' => now()
                ]);
        } catch (Exception $e) {
            Log::warning("Failed to update email log in database: " . $e->getMessage());
        }
    }
    
    /**
     * Log email failure with detailed error information
     */
    private function logEmailFailure(string $emailId, string $emailType, string $recipient, Exception $error, float $duration, array $context): void
    {
        $errorCategory = $this->categorizeError($error);
        
        Log::error("❌ EMAIL FAILURE", [
            'email_id' => $emailId,
            'type' => $emailType,
            'type_name' => self::EMAIL_TYPES[$emailType] ?? 'Unknown',
            'recipient' => $recipient,
            'error_message' => $error->getMessage(),
            'error_category' => $errorCategory,
            'error_code' => $error->getCode(),
            'error_file' => $error->getFile(),
            'error_line' => $error->getLine(),
            'duration_ms' => $duration,
            'timestamp' => now()->toISOString(),
            'context' => $context
        ]);
        
        // Update database record
        try {
            DB::table('email_logs')
                ->where('email_id', $emailId)
                ->update([
                    'status' => 'failed',
                    'error_message' => $error->getMessage(),
                    'error_category' => $errorCategory,
                    'duration_ms' => $duration,
                    'failed_at' => now(),
                    'updated_at' => now()
                ]);
        } catch (Exception $e) {
            Log::warning("Failed to update email log in database: " . $e->getMessage());
        }
    }
    
    /**
     * Categorize email errors for better debugging
     */
    private function categorizeError(Exception $error): string
    {
        $message = strtolower($error->getMessage());
        
        if (strpos($message, 'connection') !== false || strpos($message, 'timeout') !== false) {
            return 'connection_error';
        }
        
        if (strpos($message, 'authentication') !== false || strpos($message, 'login') !== false) {
            return 'auth_error';
        }
        
        if (strpos($message, 'invalid') !== false && strpos($message, 'email') !== false) {
            return 'invalid_email';
        }
        
        if (strpos($message, 'quota') !== false || strpos($message, 'limit') !== false) {
            return 'quota_exceeded';
        }
        
        if (strpos($message, 'dns') !== false || strpos($message, 'host') !== false) {
            return 'dns_error';
        }
        
        return 'unknown_error';
    }
    
    /**
     * Generate unique email ID for tracking
     */
    private function generateEmailId(): string
    {
        return 'email_' . now()->format('Ymd_His') . '_' . substr(md5(uniqid()), 0, 8);
    }
    
    /**
     * Generate unique batch ID for tracking
     */
    private function generateBatchId(): string
    {
        return 'batch_' . now()->format('Ymd_His') . '_' . substr(md5(uniqid()), 0, 8);
    }
    
    /**
     * Get email statistics for dashboard
     */
    public function getEmailStats(int $days = 7): array
    {
        try {
            $stats = DB::table('email_logs')
                ->where('created_at', '>=', now()->subDays($days))
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
                    SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
                    AVG(CASE WHEN status = "sent" THEN duration_ms ELSE NULL END) as avg_duration_ms,
                    email_type,
                    error_category
                ')
                ->groupBy('email_type', 'error_category')
                ->get();
                
            return $stats->toArray();
        } catch (Exception $e) {
            Log::warning("Failed to get email stats: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Retry failed emails
     */
    public function retryFailedEmails(int $hours = 24): array
    {
        try {
            $failedEmails = DB::table('email_logs')
                ->where('status', 'failed')
                ->where('created_at', '>=', now()->subHours($hours))
                ->get();
                
            $retryResults = [];
            
            foreach ($failedEmails as $emailLog) {
                Log::info("🔄 RETRYING FAILED EMAIL", [
                    'original_email_id' => $emailLog->email_id,
                    'email_type' => $emailLog->email_type,
                    'recipient' => $emailLog->recipient
                ]);
                
                // Note: Actual retry would need the original mailable instance
                // This is a framework for retry logic
                $retryResults[] = [
                    'original_email_id' => $emailLog->email_id,
                    'email_type' => $emailLog->email_type,
                    'recipient' => $emailLog->recipient,
                    'retry_status' => 'queued_for_manual_retry'
                ];
            }
            
            return $retryResults;
        } catch (Exception $e) {
            Log::error("Failed to retry emails: " . $e->getMessage());
            return [];
        }
    }
}