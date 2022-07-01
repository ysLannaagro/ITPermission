<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMailRelation extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'group_mail_main', 'group_mail_detail', 'status',
    ];

    public function group_mail()
    {
        return $this->belongsTo(GroupMail::class);
    }
}
