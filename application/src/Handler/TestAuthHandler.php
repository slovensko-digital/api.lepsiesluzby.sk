<?php

declare(strict_types=1);

namespace App\Handler;

use App\Service\AtlassianApiService;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestAuthHandler extends AbstractHandler
{

    private AtlassianApiService $atlassianApi;

    public function __construct()
    {
        $this->atlassianApi = new AtlassianApiService();
    }
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        if ($this->validRecaptcha($data['token'] ?? null, $data['action'] ?? null)) {
            return new Response(200, [], 'DATA_VALID');
        }
        return new Response(401, [], 'DATA_INVALID');
    }
}