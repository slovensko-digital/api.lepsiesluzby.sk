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
    private ?string $token;
    private int $serviceDeskApiID = 5;

    public function __construct()
    {
        $this->token = getenv('jira_auth_token');
        $this->client = new Client([
            'base_uri' => getenv('jira_api_host'),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Access-Control-Allow-Origin' =>  '*',
            ],
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function sendIssue(AttlassianIssueModel $data): ResponseInterface
    {
        return $this->client->post('servicedeskapi/request', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Basic '.$this->token,
            ],
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

    public function CreateAttachment(array $files): ResponseInterface
    {
        return $this->client->post('servicedeskapi/servicedesk/'.$this->serviceDeskApiID.'/attachTemporaryFile', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Basic '.$this->token,
                'X-Atlassian-Token' => 'nocheck',
            ],
            RequestOptions::MULTIPART => $files,
        ]);
    }

    public function addIssueAttachments(string $issueId, array $files): ResponseInterface
    {
        $data = [
            "public" => true,
            "temporaryAttachmentIds" => $files
        ];
        return $this->client->post('servicedeskapi/request/'.$issueId.'/attachment', [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic '.$this->token,
            ],
            RequestOptions::BODY => json_encode($data, JSON_UNESCAPED_SLASHES),
        ]);
    }
}
