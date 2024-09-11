<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'subject_name',
        'mark',
    ];

    /**
     * Get the user associated with the mark.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
