<?php

namespace UpheldSolutions\GmsInventoryApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class GmsApiClient
{
    protected Client $client;
    protected string $apiBaseUrl;
    public string $accept = 'application/json';
    protected string $token;

    public function __construct(string $apiBaseUrl)
    {
        $this->apiBaseUrl = rtrim($apiBaseUrl, '/') . '/api/';
        $this->client = new Client([
            'base_uri' => $this->apiBaseUrl,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function setToken($token): GmsApiClient
    {
        $this->token = $token;
        $this->client = new Client([
            'base_uri' => $this->apiBaseUrl,
            'headers' => [
                'Accept' => $this->accept,
                'Authorization' => "Bearer $token",
            ],
        ]);

        return $this;
    }

    /**
     * @throws GuzzleException
     */
    public function login($email, $password)
    {
        $response = $this->client->post('/login', [
            'body' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);

        return $this->handleResponse($response);
    }
    /**
     * @throws GuzzleException
     */
    public function getProfile()
    {
        $response = $this->client->get('user');

        return $this->handleResponse($response);
    }

    /**
     * @throws GuzzleException
     */
    public function getSectors()
    {
        $response = $this->client->get('available/sectors');

        return $this->handleResponse($response);
    }

    /**
     * @throws GuzzleException
     */
    public function getAirlines()
    {
        $response = $this->client->get('available/airlines');

        return $this->handleResponse($response);
    }

    /**
     * @throws GuzzleException
     */
    public function getAvailableSeats($groupId)
    {
        $response = $this->client->get('available/seats/'.$groupId);

        return $this->handleResponse($response);
    }

    /**
     * @throws GuzzleException
     */
    public function getGroups()
    {
        $response = $this->client->get('available/groups');

        return $this->handleResponse($response);
    }

    /**
     * @throws GuzzleException
     */
    public function getGroup($groupId)
    {
        $response = $this->client->get('group/detail/'.$groupId);

        return $this->handleResponse($response);
    }

    /**
     * This method allows you to create a new booking by sending an HTTP POST request to the specified URL.
     * @param $groupId
     * @param $bookingInfo
     * @param $bookingDetails
     * @return mixed
     * @throws GuzzleException
     */
    public function createBooking($groupId, $bookingInfo, $bookingDetails): mixed
    {
        $response = $this->client->post('create/booking', [
            'body' => [
                'group_id' => $groupId,
                'agency_info' => $bookingInfo,
                'booking_details' => $bookingDetails,
            ],
        ]);

        return $this->handleResponse($response);
    }

    /**
     * @throws GuzzleException
     */
    public function getBooking($bookingId)
    {
        $response = $this->client->get('show/booking/'.$bookingId);

        return $this->handleResponse($response);
    }

    protected function handleResponse(ResponseInterface $response)
    {
        if($response->getStatusCode() == 200){
            return json_decode($response->getBody()->getContents());
        }elseif($response->getStatusCode() == 401){
            return (object)['Invalid or expired token.', json_decode($response->getBody()->getContents(), true)];
        }else{
            return (object)['ERR: '.$response->getStatusCode(), json_decode($response->getBody()->getContents(), true)];
        }
    }
}
