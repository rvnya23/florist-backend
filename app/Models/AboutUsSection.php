<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['section_key', 'title', 'content', 'sort_order'])]
class AboutUsSection extends Model
{
    //
}
