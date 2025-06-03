<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name']; // The name of the skill, e.g., "Plumbing", "Gardening"

    /**
     * The users that possess this skill.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
