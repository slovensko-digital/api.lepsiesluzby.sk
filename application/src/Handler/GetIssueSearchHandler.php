<?php

declare(strict_types=1);

namespace App\Handler;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetIssueSearchHandler extends AbstractHandler
{
    /**
     * @throws GuzzleException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->atlassianApi->getSearchData($request->getQueryParams());
    }
}
