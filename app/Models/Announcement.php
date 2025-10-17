<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'attachment',
        'attachment_original_name',
        'attachment_type',
    ];
    
    public function hasAttachment()
    {
        return !is_null($this->attachment);
    }
    
    public function getAttachmentTypeIcon()
    {
        $type = $this->attachment_type;
        
        if (strpos($type, 'image/') === 0) {
            return 'fas fa-image';
        } elseif (strpos($type, 'application/pdf') === 0) {
            return 'fas fa-file-pdf';
        } elseif (strpos($type, 'application/msword') === 0 || strpos($type, 'application/vnd.openxmlformats-officedocument.wordprocessingml') === 0) {
            return 'fas fa-file-word';
        } elseif (strpos($type, 'application/vnd.ms-excel') === 0 || strpos($type, 'application/vnd.openxmlformats-officedocument.spreadsheetml') === 0) {
            return 'fas fa-file-excel';
        } else {
            return 'fas fa-file';
        }
    }
}