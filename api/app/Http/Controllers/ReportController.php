<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Models\AnonymousReport;
use App\Services\AIService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(ReportRequest $request, AIService $ai)
    {
        try {
            $analysis = $ai->filterReport($request->content);

            if (($analysis['allowed'] ?? true) === false) {
                return response()->json([
                    'message' => 'Relato rejeitado pelo filtro.',
                ], 422);
            }

            // Não associamos o relato a usuário, token ou sessão. O anonimato
            // também é garantido pelo schema, que não possui user_id.
            return response()->json(
                AnonymousReport::create([
                    'original_content' => $request->content,
                    'ai_analysis' => $analysis,
                ]),
                201
            );
        } catch (\RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 503);
        }
    }

    public function index()
    {
        return AnonymousReport::latest()->paginate(20);
    }

    public function update(Request $request, AnonymousReport $report)
    {
        $request->validate([
            'status' => 'required|in:nova,lida,resolvida',
        ]);

        $report->update(['status' => $request->status]);

        return $report;
    }
}
