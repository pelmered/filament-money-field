<?php

namespace Pelmered\FilamentMoneyField\Tests\Support\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mirrors a model where the user forgot the money casts, as in
 * https://github.com/pelmered/filament-money-field/issues/102
 */
class PostWithoutCasts extends Model
{
    protected $table = 'posts';

    protected $guarded = [];
}
