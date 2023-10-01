<?php

declare(strict_types=1);

namespace App\Handler;

use App\Model\AtlassianIdeaModel;

final class SendIdeaHandler extends IssueHandler
{
    private string $issueClassName = AtlassianIdeaModel::class;
}
