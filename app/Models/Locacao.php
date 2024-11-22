<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use mysql_xdevapi\Table;

class Locacao extends Model
{
    use HasFactory;
    protected $table = 'locacoes';
}
