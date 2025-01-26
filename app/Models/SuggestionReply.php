<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuggestionReply extends Model
{
    protected $fillable = ['suggestion_id', 'reply'];

    public function suggestion()
    {
        return $this->belongsTo(Suggestion::class);
    }
}
