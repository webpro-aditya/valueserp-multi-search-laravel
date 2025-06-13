<?php

// app/Services/SearchService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SearchService
{
    public static function searchAllPages($queries)
    {
        $results = [];

        $apiKey = config('services.apikeys.valuesrp_api_key');
        $apiUrl = config('services.apikeys.valuesrp_api_url');

        foreach ($queries as $query) {
            if (!empty($query)) {
                $response = Http::withOptions(['verify' => false])->get($apiUrl, [
                    'api_key' => $apiKey,
                    'q' => $query,
                ]);

                if ($response->failed() || !$response->json('organic_results')) {
                    continue; // skip if failed or no data
                }

                $organicResults = $response->json('organic_results');

                foreach ($organicResults as $item) {
                    $results[] = [
                        'query'   => $query,
                        'title'   => $item['title'] ?? 'N/A',
                        'link'    => $item['link'] ?? 'N/A',
                        'snippet' => $item['snippet'] ?? 'N/A',
                    ];
                }
            }
        }

        return $results;
    }
}
