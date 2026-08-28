<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    protected $table = 'information';

    protected $fillable = [
        'main_topic',
        'description'
    ];

    public function knowledgeEntries()
    {
        return $this->hasMany(KnowledgeBase::class);
    }
}