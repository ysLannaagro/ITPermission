<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'status',
    ];

    public function folder_in_groups()
    {
        return $this->hasMany(FolderInGroup::class);
    }
}
