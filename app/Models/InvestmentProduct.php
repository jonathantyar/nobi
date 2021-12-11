<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentProduct extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['code', 'name', 'nab'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_investment')->withPivot(['balance', 'unit']);
    }

    public function totalUnitInvestment()
    {
        return $this->users->sum('pivot.unit');
    }

    public function totalBalanceInvestment()
    {
        return $this->users->sum('pivot.balance');
    }
}
