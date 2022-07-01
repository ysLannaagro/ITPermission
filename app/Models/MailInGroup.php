<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailInGroup extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'mail_id', 'group_mail_id', 'status',
    ];

    public function mail()
    {
        return $this->belongsTo(Mail::class);
    }
    
    public function group_mail()
    {
        return $this->belongsTo(GroupMail::class);
    }
}
