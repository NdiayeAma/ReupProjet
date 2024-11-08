<?php

namespace App\Twig\Runtime;
use cebe\markdown\Markdown;
use Twig\Extension\RuntimeExtensionInterface;

class MarkdownExtensionRuntime implements RuntimeExtensionInterface
{
    private Markdown $markdown;

    public function __construct(Markdown $markdown)
    {
        $this->markdown = $markdown;
    }

    public function markdownToHtml($value):string
    {
        return $this->markdown->parse($value);
    }
}
