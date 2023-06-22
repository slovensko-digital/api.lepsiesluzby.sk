<?php

use App\Handler\GetIssueSearchHandler;
use App\Handler\HandlerInterface;
use App\Handler\SendIdeaHandler;
use App\Handler\SendProblemHandler;
use App\Handler\TestAuthHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

function procesJsonRequest($request): Request
{
    $contentType = $request->getHeaderLine('Content-Type');

    if (str_contains($contentType, 'application/json')) {
        $contents = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $request->withParsedBody($contents);
        }
    }
    return $request;
}

function callAtlassian(HandlerInterface $handler, Request $request, Response $response): Response
{
    $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Access-Control-Allow-Origin' =>  '*',
    ];
    try {
        $request = procesJsonRequest($request);
        return $handler->handle($request);
    } catch (\Exception $e) {
        $response = $response->withStatus(500);
        $response->getBody()->write($e->getMessage());
    }
    return $response;
}

$app->get('/test/ping', function (Request $request, Response $response, $args) {
    $request = procesJsonRequest($request);
    $response->getBody()->write(json_encode(['ack' => time()]));
    return $response;
});

$app->get('/test/auth', function (Request $request, Response $response, $args) {
    $handler = new TestAuthHandler();
    return callAtlassian($handler, $request, $response);
});

$app->get('/issue/search', function (Request $request, Response $response, $args) {
    $handler = new GetIssueSearchHandler();
    return callAtlassian($handler, $request, $response);
});

$app->post('/issue/idea', function (Request $request, Response $response, $args) {
    $handler = new SendIdeaHandler();
    return callAtlassian($handler, $request, $response);
});

$app->post('/issue/problem', function (Request $request, Response $response, $args) {
    $handler = new SendProblemHandler();
    return callAtlassian($handler, $request, $response);
});

$app->get('/issue', function (Request $request, Response $response, $args) {
    $request = procesJsonRequest($request);
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->run();