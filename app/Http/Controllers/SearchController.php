<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SearchController extends Controller
{
    private $valuesrpApiKey, $valueApiUrl;

    public function __construct()
    {
        // Load ValueSERP API keys from config/services.php
        $this->valuesrpApiKey = config('services.apikeys.valuesrp_api_key');
        $this->valueApiUrl = config('services.apikeys.valuesrp_api_url');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('searchview');
    }

    /**
     * Handle search queries, fetch results from ValueSERP API,
     * process JSON response, and store data for CSV export.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(Request $request)
    {
        $request->validate([
            'queries' => 'required|array|min:1|max:5',
            'queries.*' => 'required|string|min:1'
        ]);

        $results = collect();

        collect($request->queries)->each(function ($query) use (&$results) {
            $response = Http::withOptions(['verify' => false])->get($this->valueApiUrl, [
                'api_key' => $this->valuesrpApiKey,
                'q' => $query,
            ]);

            if ($response->failed() || !$response->json('organic_results')) {
                return;
            }

            $organicResults = collect($response->json('organic_results'));

            $organicResults->each(function ($item) use (&$results, $query) {
                $results->push([
                    'query' => $query,
                    'title' => $item['title'] ?? 'N/A',
                    'link' => $item['link'] ?? 'N/A',
                    'snippet' => $item['snippet'] ?? 'N/A',
                ]);
            });
        });

        if ($results->isEmpty()) {
            return response()->json(['message' => 'No results found.'], 404);
        }

        Session::put('csv_data', $results); // store for export

        return response()->json([
            'data' => $results
        ]);
    }


    /**
     * Export the search results stored in session as a CSV file.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function export(Request $request)
    {
        $queries = $request->input('queries');
        $allResults = SearchService::searchAllPages($queries); // service layer gets all data

        $headers = ['Query', 'Title', 'Link', 'Snippet'];
        $callback = function () use ($allResults, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($allResults as $row) {
                fputcsv($file, [$row['query'], $row['title'], $row['link'], $row['snippet']]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=search_results.csv",
        ]);
    }
}
