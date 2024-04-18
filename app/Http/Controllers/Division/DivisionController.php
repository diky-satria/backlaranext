<?php

namespace App\Http\Controllers\Division;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DivisionController extends Controller
{
    public function index()
    {
        $data = DB::select("SELECT id as value, UPPER(name) as label FROM divisions ORDER BY name ASC");

        return response()->json([
            'message' => 'all division',
            'data' => $data
        ]);
    }
}
