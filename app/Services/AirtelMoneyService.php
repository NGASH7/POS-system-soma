<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AirtelMoneyService
{
    protected $client;
    protected $clientId;
    protected $clientSecret;
    protected $environment;
    protected $country;
    protected $currency;
    protected $callbackUrl;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false // For production, set to true
        ]);

        $this->clientId = env('AIRTL_MONEY_CLIENT_ID');
        $this->clientSecret = env('AIRTL_MONEY_CLIENT_SECRET');
        $this->environment = env('AIRTL_MONEY_ENV', 'sandbox');
        $this->country = env('AIRTL_MONEY_COUNTRY', 'KE');
        $this->currency = env('AIRTL_MONEY_CURRENCY', 'KES');
        $this->callbackUrl = env('AIRTL_MONEY_CALLBACK_URL');
    }

    /**
     * Get the base URL based on environment
     */
    protected function getBaseUrl()
    {
        if ($this->environment === 'production') {
            return 'https://openapi.airtel.africa';
        }
        return 'https://openapiuat.airtel.africa';
    }

    /**
     * Get access token from Airtel Money
     */
    public function getAccessToken()
    {
        try {
            $url = $this->getBaseUrl() . '/auth/oauth2/token';
            
            $response = $this->client->post($url, [
                'json' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credentials'
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            
            if (isset($data['access_token'])) {
                Log::info('Airtel Money access token retrieved successfully');
                return $data['access_token'];
            }

            Log::error('Failed to get Airtel Money access token', ['response' => $data]);
            return null;

        } catch (\Exception $e) {
            Log::error('Airtel Money token error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Initiate USSD Push for Airtel Money payment
     */
    public function initiatePayment($phoneNumber, $amount, $reference, $description = 'Payment')
    {
        try {
            $token = $this->getAccessToken();
            
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with Airtel Money API'
                ];
            }

            // Format phone number (remove leading 0 and country code)
            $phone = $this->formatPhoneNumber($phoneNumber);
            
            $url = $this->getBaseUrl() . '/payments/USSDPush/initiate';

            $payload = [
                'amount' => (string) number_format($amount, 2, '.', ''),
                'currency' => $this->currency,
                'country' => $this->country,
                'msisdn' => $phone,
                'transaction_id' => $reference,
                'payment_narration' => $description,
                'callback_url' => $this->callbackUrl
            ];

            Log::info('Airtel Money payment payload:', $payload);

            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => $payload
            ]);

            $data = json_decode($response->getBody(), true);
            
            Log::info('Airtel Money payment response:', $data);

            if (isset($data['transactionId']) || isset($data['status']) && $data['status'] === 'success') {
                return [
                    'success' => true,
                    'transaction_id' => $data['transactionId'] ?? $data['transaction_id'] ?? $reference,
                    'message' => 'Payment initiated successfully'
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Payment initiation failed'
            ];

        } catch (\Exception $e) {
            Log::error('Airtel Money payment error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment initiation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query transaction status from Airtel Money
     */
    public function queryTransaction($transactionId)
    {
        try {
            $token = $this->getAccessToken();
            
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with Airtel Money API'
                ];
            }

            $url = $this->getBaseUrl() . '/payments/query';

            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'transaction_id' => $transactionId
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            
            Log::info('Airtel Money query response:', $data);

            return [
                'success' => true,
                'status' => $data['status'] ?? 'pending',
                'data' => $data
            ];

        } catch (\Exception $e) {
            Log::error('Airtel Money query error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Query failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number for Airtel Money API
     */
    protected function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If number starts with 0, remove it
        if (substr($phone, 0, 1) == '0') {
            $phone = substr($phone, 1);
        }
        
        // If number doesn't start with 254, remove country code if present
        // For Airtel, they expect the number in format: 7XXXXXXXX (without country code)
        if (substr($phone, 0, 3) == '254') {
            $phone = substr($phone, 3);
        }
        
        return $phone;
    }
}