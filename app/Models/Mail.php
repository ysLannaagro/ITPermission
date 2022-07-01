<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'pre_name', 'email', 'status',
    ];

    public function mail_in_groups()
    {
        return $this->hasMany(MailInGroup::class);
    }
}
