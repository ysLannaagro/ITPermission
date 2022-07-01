<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMail extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'status', 'set_column'
    ];

    public function group_mail_relations()
    {
        return $this->hasMany(GroupMailRelation::class);
    }

    public function mail_in_groups()
    {
        return $this->hasMany(MailInGroup::class);
    }
    
    public function folder_in_groups()
    {
        return $this->hasMany(FolderInGroup::class);
    }
}
