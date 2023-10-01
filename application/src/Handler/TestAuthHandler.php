<?php

declare(strict_types=1);

namespace App\Handler;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestAuthHandler extends AbstractHandler
{
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $headers = $this->getApiResponseHeaders();
        $headers['Accept'] = 'multipart/form-data';

        $token = $request->getHeader('recaptcha-token')[0] ?? null;
        $action = $request->getHeader('recaptcha-action')[0] ?? null;

        try {
            if ($this->validRecaptcha($token, $action)) {
                return new Response(200, $headers, json_encode([$token,$action,'OK']));
            }
        } catch (\Exception $e) {
            return new Response(200, $headers, json_encode([$token,$action,$e->getMessage()]));
        }

        return new Response(200, $headers, json_encode([$token,$action,'NOK']));
    }
}