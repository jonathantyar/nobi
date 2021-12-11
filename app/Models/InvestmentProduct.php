<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Calculate;

use App\Models\Events\NabHistory;

class InvestmentProduct extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['code', 'name', 'nab'];

    public function histories()
    {
        return $this->hasMany(NabHistory::class, 'investment_product_id', 'id')->latest('id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_investment')->withPivot(['unit']);
    }

    public function totalUnitInvestment()
    {
        return $this->users->sum('pivot.unit');
    }

    public function totalBalanceInvestment()
    {
        return $this->users->sum('pivot.balance');
    }

    public function currentNab(Float $current_balance)
    {
        return $this->totalUnitInvestment() ? Calculate::roundDown($current_balance / $this->totalUnitInvestment(), 4) : 1;
    }
}
