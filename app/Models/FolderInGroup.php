<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolderInGroup extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'folder_id', 'group_mail_id', 'to_full', 'to_read', 'status',
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
    
    public function group_mail()
    {
        return $this->belongsTo(GroupMail::class);
    }
}
