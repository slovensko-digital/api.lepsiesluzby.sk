<?php

declare(strict_types=1);

namespace App\Handler;

use App\Service\AtlassianApiService;
use App\Traits\RecaptchaTrait;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\UploadedFile;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AbstractHandler implements HandlerInterface
{
    use RecaptchaTrait;
    protected AtlassianApiService $atlassianApi;

    public function __construct()
    {
        $this->atlassianApi = new AtlassianApiService();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }

    protected function getApiResponseHeaders(): array
    {
        $headers = [];

        $headers["Access-Control-Allow-Origin"] = "*";
        $headers['Access-Control-Allow-Methods'] = "*";
        $headers['Access-Control-Allow-Headers'] = 'Content-Type,X-Amz-Date,Authorization,X-Api-Key,X-Amz-Security-Token,Recaptcha-Token,Recaptcha-Action';
        $headers['Access-Control-Allow-Credentials'] = "true";
        $headers['Access-Control-Max-Age'] = 86400;
        $headers['Content-Type'] = 'application/json';
        $headers['Accept'] = 'application/json';
        return $headers;
    }

    public function getFilesFromRequest(ServerRequestInterface $request): array
    {
        $uploadedFiles = $request->getUploadedFiles();
        $files = [];

        /**
         * @var UploadedFile $image
         */
        foreach ($uploadedFiles as $image) {

            if ($image->getError() === UPLOAD_ERR_OK) {
                $file = [
                    "name" => 'file',
                    "contents" => (string)$image->getStream(),
                    'filename' => $image->getClientFilename(),
                ];
                $files[] = $file;
            }
        }
        return $files;
    }
}
