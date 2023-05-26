<?php

declare(strict_types=1);

namespace App\Handler;

use App\Model\AtlassianIdeaModel;
use App\Service\AtlassianApiService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class SendIdeaHandler extends AbstractHandler
{
    private AtlassianApiService $atlassianApi;

    public function __construct()
    {
        $this->atlassianApi = new AtlassianApiService();
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        if ($this->validRecaptcha($data['token'] ?? null, $data['action'] ?? null)) {
            $issueData = new AtlassianIdeaModel();
            $issueData->fromArray($data);
            return $this->atlassianApi->sendIssue($issueData);
        }
        return new Response(401, [], 'DATA_INVALID');
    }
}
