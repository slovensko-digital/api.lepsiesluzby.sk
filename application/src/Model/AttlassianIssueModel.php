<?php

namespace App\Model;

interface AttlassianIssueModel
{
    public function fromArray(array $data = []): void;
    public function toArray(): array;
}