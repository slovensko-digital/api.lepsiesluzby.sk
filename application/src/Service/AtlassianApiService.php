<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\AttlassianIssueModel;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class AtlassianApiService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => getenv('jira_api_host'),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Access-Control-Allow-Origin' =>  '*',
                'Authorization' => 'Basic '.getenv('jira_auth_token'),
            ],
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function sendIssue(AttlassianIssueModel $data): ResponseInterface
    {
        return $this->client->post('servicedeskapi/request', [
            RequestOptions::JSON => $data->toArray(),
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function getSearchData(array $searchData): ResponseInterface
    {
        return $this->client->get('api/2/search', [
            RequestOptions::QUERY => $searchData,
        ]);
    }
}
