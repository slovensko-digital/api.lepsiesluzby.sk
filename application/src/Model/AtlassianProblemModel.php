<?php

declare(strict_types=1);

namespace App\Model;

class AtlassianProblemModel extends AtlassianIdeaModel
{
    private string $url;
    private string $requestTypeId;

    public function __construct(
        $summary = '',
        $description = '',
        $name = '',
        $surname = '',
        $email = '',
        $phone = '',
        $url = ''
    )
    {
        parent::__construct(
            $summary,
            $description,
            $name,
            $surname,
            $email,
            $phone
        );
        $this->requestTypeId = '25';
        $this->url = $url;
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
        $data = parent::toArray();
        $data['requestFieldValues']['customfield_10048'] = $this->url;
        return $data;
    }
}
