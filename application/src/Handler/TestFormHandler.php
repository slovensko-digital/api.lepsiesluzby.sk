<?php

declare(strict_types=1);

namespace App\Handler;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestFormHandler extends AbstractHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $headers = $this->getApiResponseHeaders();
        $headers['Accept'] = 'multipart/form-data';

        $data = $request->getParsedBody();
        return new Response( 200, $headers, json_encode($request->getHeaders()));
        $files = $this->getFilesFromRequest($request);
        $attachment = $this->addIssueAttachments($files, '10732');

        $body = $attachment->getbody()->getContents();
        $out = new \GuzzleHttp\Psr7\Response(
            $attachment->getStatusCode(),
            $headers,
            $body
        );

        return new Response( $attachment->getStatusCode(), $headers, $body);
    }

    public function addIssueAttachments(array $files, string $issueId): ?ResponseInterface
    {
        if (empty($files)) return null;
        /* @var \App\Service\AtlassianApiService $attlasianApi */
        $tempResponse = $this->atlassianApi->CreateAttachment($files);
        $tempResponseData = json_decode($tempResponse->getBody()->getContents(), true);
        $attachmentIds = [];
        foreach ($tempResponseData['temporaryAttachments'] ?? [] as $attachment) {
            $attachmentIds[] = $attachment['temporaryAttachmentId'];
        }
        $response = $this->atlassianApi->addIssueAttachments($issueId, $attachmentIds);
        return $response;
    }
}
