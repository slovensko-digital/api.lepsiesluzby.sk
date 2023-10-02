<?php

declare(strict_types=1);

namespace App\Handler;

use App\Model\AtlassianIdeaModel;
use App\Service\AtlassianApiService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\UploadedFile;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IssueHandler extends AbstractHandler
{
    protected string $issueClassName = AtlassianIdeaModel::class;

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

    public function addIssueAttachments(array $files, string $issueId): ?ResponseInterface
    {
        if (empty($files)) return null;
        /* @var AtlassianApiService $attlasianApi */
        $tempResponse = $this->atlassianApi->CreateAttachment($files);
        $tempResponseData = json_decode($tempResponse->getBody()->getContents(), true);
        $attachmentIds = [];
        foreach ($tempResponseData['temporaryAttachments'] ?? [] as $attachment) {
            $attachmentIds[] = $attachment['temporaryAttachmentId'];
        }
        return $this->atlassianApi->addIssueAttachments($issueId, $attachmentIds);
    }


    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        $token = $request->getHeader('Recaptcha-Token')[0] ?? null;
        $action = $request->getHeader('Recaptcha-Action')[0] ?? null;

        try {
            if ($this->validRecaptcha($token, $action)) {
                $issueData = new $this->issueClassName();
                $issueData->fromArray($data);
                $issueResponse =  $this->atlassianApi->sendIssue($issueData);

                $files = $this->getFilesFromRequest($request);
                if ($issueResponse->getStatusCode() === 201 && !empty($files)) {
                    $issue = json_decode($issueResponse->getBody()->getContents(), true);
                    $this->addIssueAttachments($files, $issue['issueId']);
                }

                return $issueResponse;
            }
        } catch (Exception | GuzzleException $e) {
            return new Response(401, [], json_encode([$e->getMessage()]));
        }

        return new Response(401, [], json_encode(['error' => 'DATA_INVALID']));
    }
}
