<?php
namespace Pelmered\FilamentMoneyField\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Pelmered\FilamentMoneyField\Tests\Database\Factories\PostFactory;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'is_published' => 'boolean',
        'tags'         => 'array',
    ];

    protected $guarded = [];

    /*
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    */

    protected static function newFactory()
    {
        return PostFactory::new();
    }
}
