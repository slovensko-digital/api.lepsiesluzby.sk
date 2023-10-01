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


// Add Error Handling Middleware
//$app->addErrorMiddleware(true, true, false);

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

function callAtlassian(
    HandlerInterface $handler,
    Request $request,
    Response $response,
    bool $isOption = false,
    string $accept = 'application/json',
    string $contentType = 'application/json'
): Response {
    $headers = [];
    $headers["Access-Control-Allow-Origin"] = "*";
    $headers['Access-Control-Allow-Methods'] = "*";
    $headers['Access-Control-Allow-Headers'] = 'Content-Type,X-Amz-Date,Authorization,X-Api-Key,X-Amz-Security-Token,Recaptcha-Token,Recaptcha-Action';
    $headers['Access-Control-Allow-Credentials'] = "true";
    $headers['Access-Control-Max-Age'] = 86400;
    $headers['Content-Type'] = $contentType;
    $headers['Accept'] = $accept;

    if ($isOption) {
        return new \GuzzleHttp\Psr7\Response(200, $headers, json_encode([]));
    }

    try {
        $response = $handler->handle($request);
        return new \GuzzleHttp\Psr7\Response(
            $response->getStatusCode(),
            $headers,
            $response->getBody()
        );
    } catch (\Exception $e) {
        return new \GuzzleHttp\Psr7\Response(
            500,
            $headers,
            json_encode([
                'error' => $e->getMessage()
            ])
        );
    }
}

$app->get('/test/ping', function (Request $request, Response $response, $args) {
    $request = procesJsonRequest($request);
    $response->getBody()->write(json_encode(['ack' => time()]));
    return $response;
});

$app->post('/test/auth', function (Request $request, Response $response, $args) {
    $handler = new TestAuthHandler();
    return $handler->handle($request);
});

$app->options('/test/auth', function (Request $request, Response $response, $args) {
    $handler = new TestAuthHandler();
    return $handler->handle($request);
});

$app->get('/issue/search', function (Request $request, Response $response, $args) {
    $handler = new GetIssueSearchHandler();
    $request = procesJsonRequest($request);
    return callAtlassian($handler, $request, $response);
});

$app->post('/issue/idea', function (Request $request, Response $response, $args) {
    $handler = new SendIdeaHandler();
    return callAtlassian($handler, $request, $response, false, 'multipart/form-data');
});

$app->options('/issue/idea', function (Request $request, Response $response, $args) {
    $handler = new SendIdeaHandler();
    return callAtlassian($handler, $request, $response, true, 'multipart/form-data');
});

$app->post('/issue/problem', function (Request $request, Response $response, $args) {
    $handler = new SendProblemHandler();
    return callAtlassian($handler, $request, $response, false,  'multipart/form-data');
});

$app->options('/issue/problem', function (Request $request, Response $response, $args) {
    $handler = new SendProblemHandler();
    return callAtlassian($handler, $request, $response, true,  'multipart/form-data');
});

$app->run();