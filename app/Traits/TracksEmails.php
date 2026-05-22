<?php

namespace App\Traits;

use App\Services\EmailTrackingService;
use Illuminate\Support\Facades\Log;

/**
 * TracksEmails Trait
 * 
 * Provides easy email tracking integration for controllers.
 * Use this trait in any controller that sends emails to get
 * automatic logging and error tracking.
 */
trait TracksEmails
{
    /**
     * Send a tracked email with comprehensive logging
     * 
     * @param string $emailType Type of email (see EmailTrackingService::EMAIL_TYPES)
     * @param string $recipient Email address
     * @param mixed $mailable Laravel Mailable instance
     * @param array $context Additional context for logging
     * @return array Result with success status and details
     */
    protected function sendTrackedEmail(string $emailType, string $recipient, $mailable, array $context = []): array
    {
        $emailService = app(EmailTrackingService::class);
        $result = $emailService->sendTrackedEmail($emailType, $recipient, $mailable, $context);
        
        if (isset($result['error_category']) && $result['error_category'] === 'quota_exceeded') {
            session()->flash('error', 'The daily email limit of 500 has been reached. This message has been queued inside your portal dashboard. Please check back or try again in 24 hours.');
        }
        
        return $result;
    }
    
    /**
     * Send multiple tracked emails
     */
    protected function sendBatchEmails(string $emailType, array $recipients, $mailable, array $context = []): array
    {
        $emailService = app(EmailTrackingService::class);
        $result = $emailService->sendBatchEmails($emailType, $recipients, $mailable, $context);
        
        $quotaExceeded = false;
        if (isset($result['results'])) {
            foreach ($result['results'] as $r) {
                if (isset($r['error_category']) && $r['error_category'] === 'quota_exceeded') {
                    $quotaExceeded = true;
                    break;
                }
            }
        }
        
        if ($quotaExceeded) {
            session()->flash('error', 'The daily email limit of 500 has been reached. This message has been queued inside your portal dashboard. Please check back or try again in 24 hours.');
        }
        
        return $result;
    }
    
    /**
     * Log email result to Laravel log with consistent formatting
     */
    protected function logEmailResult(array $result, string $operation = 'Email Operation'): void
    {
        if ($result['success']) {
            Log::info("✅ {$operation} SUCCESS", [
                'email_id' => $result['email_id'],
                'message' => $result['message'],
                'duration_ms' => $result['duration_ms'] ?? null
            ]);
        } else {
            Log::error("❌ {$operation} FAILED", [
                'email_id' => $result['email_id'],
                'error' => $result['error'],
                'message' => $result['message'],
                'duration_ms' => $result['duration_ms'] ?? null
            ]);
        }
    }
    
    /**
     * Handle email result with user feedback
     * Returns appropriate response for controllers
     */
    protected function handleEmailResult(array $result, string $successMessage = null, string $errorMessage = null): array
    {
        if ($result['success']) {
            return [
                'success' => true,
                'message' => $successMessage ?: 'Email sent successfully',
                'email_id' => $result['email_id']
            ];
        } else {
            return [
                'success' => false,
                'message' => $errorMessage ?: 'Failed to send email. Please try again or contact support.',
                'error' => $result['error'],
                'email_id' => $result['email_id']
            ];
        }
    }
}