<?php

declare(strict_types=1);

namespace App\Handler;

use App\Service\AtlassianApiService;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetIssueSearchHandler extends AbstractHandler
{
    private AtlassianApiService $atlassianApi;

    public function __construct()
    {
        $this->atlassianApi = new AtlassianApiService();
    }

    /**
     * @throws GuzzleException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->atlassianApi->getSearchData($request->getQueryParams());
    }
}
