<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'company_name',
    'email',
    'phone',
    'details',
    'status',
    'converted_type',
    'converted_id',
])]
class SponsorQuery extends Model
{
    use HasFactory;

    public function convertedEntity()
    {
        return $this->morphTo('converted');
    }
}
