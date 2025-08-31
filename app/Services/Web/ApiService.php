<?php
// app/Services/Web/ApiService.php
namespace App\Services\Web;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Helpers\ApiHelper;
use Illuminate\Support\Facades\Log;

class ApiService
{
    protected $client;
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('pawtel.api.base_url', config('app.url') . '/api');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => config('pawtel.api.timeout', 30),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]
        ]);
    }

    protected function makeRequest($method, $endpoint, $data = [], $headers = [])
    {
        try {
            $defaultHeaders = ApiHelper::getAuthHeaders();

            // Remove CSRF token for API requests (handled by Sanctum)
            if (str_starts_with($endpoint, 'auth/')) {
                unset($defaultHeaders['X-CSRF-TOKEN']);
            }

            $headers = array_merge($defaultHeaders, $headers);

            $options = [
                'headers' => $headers
            ];

            if (!empty($data)) {
                if ($method === 'GET') {
                    $options['query'] = $data;
                } else {
                    $options['json'] = $data;
                }
            }

            $response = $this->client->request($method, $endpoint, $options);
            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'data' => $body,
                'status' => $response->getStatusCode()
            ];
        } catch (RequestException $e) {
            Log::error('API Request Error: ' . $e->getMessage(), [
                'method' => $method,
                'endpoint' => $endpoint,
                'data' => $data
            ]);

            return ApiHelper::handleApiError($e);
        }
    }

    public function get($endpoint, $params = [])
    {
        return $this->makeRequest('GET', $endpoint, $params);
    }

    public function post($endpoint, $data = [])
    {
        return $this->makeRequest('POST', $endpoint, $data);
    }

    public function put($endpoint, $data = [])
    {
        return $this->makeRequest('PUT', $endpoint, $data);
    }

    public function delete($endpoint)
    {
        return $this->makeRequest('DELETE', $endpoint);
    }

    public function upload($endpoint, $files, $data = [])
    {
        try {
            $headers = ApiHelper::getAuthHeaders();
            unset($headers['Content-Type']); // Let Guzzle set it for multipart

            $multipart = [];

            // Add regular form data
            foreach ($data as $key => $value) {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value
                ];
            }

            // Add files
            foreach ($files as $key => $file) {
                $multipart[] = [
                    'name' => $key,
                    'contents' => fopen($file->getPathname(), 'r'),
                    'filename' => $file->getClientOriginalName()
                ];
            }

            $response = $this->client->request('POST', $endpoint, [
                'headers' => $headers,
                'multipart' => $multipart
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'data' => $body,
                'status' => $response->getStatusCode()
            ];
        } catch (RequestException $e) {
            return ApiHelper::handleApiError($e);
        }
    }
}
