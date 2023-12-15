<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ingredient extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'measure', 'supplier'];

    /**
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public static function getRequiredIngredients($startDate, $endDate, $filters)
    {
        $response = DB::table('boxes AS b')
            ->join('box_recipe AS br', 'b.id', '=', 'br.box_id')
            ->join('recipes AS r', 'br.recipe_id', '=', 'r.id')
            ->join('ingredient_recipe AS ir', 'r.id', '=', 'ir.recipe_id')
            ->join('ingredients AS i', 'i.id', '=', 'ir.ingredient_id')
            ->where('b.delivery_date', '>=', $startDate)
            ->where('b.delivery_date', '<', $endDate);
        if (!empty($filters['supplier'])) {
            $response->where('i.supplier', '=', $filters['supplier']);
        }
        return $response->groupBy('i.id')
            ->select('i.id', 'i.name', 'i.measure', 'i.supplier', DB::raw('SUM(ir.amount) as required_amount'))
            ->get()
            ->toArray();
    }
}
