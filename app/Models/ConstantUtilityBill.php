<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstantUtilityBill extends Model
{
    use HasFactory;

    protected $fillable = ['number_of_appliances', 'cost'];
}
