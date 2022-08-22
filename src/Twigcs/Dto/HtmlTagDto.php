<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\CodingStandard\Twigcs\Dto;

final class HtmlTagDto
{
    /** @var string */
    private $tag = '';

    /** @var string */
    private $htmlLine = '';

    /** @var int */
    private $offset = 0;

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getHtmlLine(): string
    {
        return $this->htmlLine;
    }

    public function setHtmlLine(string $htmlLine): self
    {
        $this->htmlLine = $htmlLine;

        return $this;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }
}
