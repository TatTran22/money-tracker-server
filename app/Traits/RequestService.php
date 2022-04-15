<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait RequestService
{
    /**
     * @param       $method
     * @param       $requestUrl
     * @param array $formParams
     * @param array $headers
     * @return mixed
     */
    public function request($method, $requestUrl, array $formParams = [], array $headers = [])
    {
        $url = $this->baseUri . $requestUrl;

        if (isset($this->apiKey)) {
            $headers['x-api-key'] = $this->apiKey;
        }

        if (isset($this->userUid)) {
            $headers['user-uid'] = $this->userUid;
        }

        $headers = array_merge($headers, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

        return Http::withHeaders($headers)
            ->withOptions([
                'verify' => false,
            ])->{$method}($url, $formParams);
    }
}
