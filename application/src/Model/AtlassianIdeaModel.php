<?php

declare(strict_types=1);

namespace App\Model;

class AtlassianIdeaModel implements AttlassianIssueModel
{
    private string $serviceDeskId;
    private string $requestTypeId;
    private string $summary;
    private string $description;
    private string $name;
    private string $surname;
    private string $email;
    private string $phone;

    public function __construct(
        $summary = '',
        $description = '',
        $name = '',
        $surname = '',
        $email = '',
        $phone = ''
    )
    {
        $this->serviceDeskId = '4';
        $this->requestTypeId = '19';
        $this->summary = $summary;
        $this->description = $description;
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
        $this->phone = $phone;
    }

    public function fromArray(array $data = []): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function toArray(): array
    {
        return [
            'serviceDeskId' => $this->serviceDeskId,
            'requestTypeId' => $this->requestTypeId,
            'requestFieldValues' => [
                'summary' => $this->summary,
                'description' => $this->description,
                'customfield_10040' => $this->name,
                'customfield_10036' => $this->surname,
                'customfield_10042' => $this->email,
                'customfield_10047' => $this->phone,
            ],
        ];
    }
}
