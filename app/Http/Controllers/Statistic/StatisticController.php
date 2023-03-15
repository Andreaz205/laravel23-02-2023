<?php

namespace App\Http\Controllers\Statistic;

use App\Http\Controllers\Controller;
use Inertia\Response;

class StatisticController extends Controller
{
    public function index(): Response
    {
        return inertia('Statistic/Index');
    }
}
