<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecommendationsController extends Controller
{
    public function recommendations(Request $request)
    {
        $client = $request->get('client', 'WAJO');

        $latest = DB::table('recommendation')
            ->selectRaw('
                to_json(recommendation) as recommendation,
                to_json(action) as action,
                to_json(content) as content,
                client
            ')
            ->where('client', $client)
            ->orderByDesc('created_at')
            ->first();

        if (!$latest) {
            return view('overview', ['rows' => [], 'client' => $client]);
        }

        $recs  = json_decode($latest->recommendation, true) ?? [];
        $acts  = json_decode($latest->action, true) ?? [];
        $conts = json_decode($latest->content, true) ?? [];

        $max = max(count($recs), count($acts), count($conts));
        $rows = [];
        for ($i = 0; $i < $max; $i++) {
            $rows[] = [
                'recommendation' => $recs[$i] ?? '-',
                'action'         => $acts[$i] ?? '-',
                'content'        => $conts[$i] ?? '-',
            ];
        }

        return view('overview', compact('rows', 'client'));
    }
}
