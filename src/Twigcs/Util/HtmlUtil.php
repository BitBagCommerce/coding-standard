<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\CodingStandard\Twigcs\Util;

use BitBag\CodingStandard\Twigcs\Dto\HtmlTagDto;
use BitBag\CodingStandard\Twigcs\Dto\OffsetDto;
use FriendsOfTwig\Twigcs\TwigPort\Token;

final class HtmlUtil
{
    public const REGEX_FLAGS = PREG_OFFSET_CAPTURE | PREG_SET_ORDER;

    private const UNNECESSARY_TAGS = [
        '#<!--(.*?)-->#s',
        '#<!\[CDATA\[(.*?)\]\]>#s',
        '#<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>#s',
        '#<\s*script\s*>(.*?)<\s*/\s*script\s*>#s',
        '#<\s*style[^>]*[^/]>(.*?)<\s*/\s*style\s*>#s',
        '#<\s*style\s*>(.*?)<\s*/\s*style\s*>#s',
    ];

    private const HTML_TAG_PATTERN = '#</?\s*(?<tag>\w+)[^>]*>#s';

    /** @var array */
    private $unnecessaryTagsPositions = [];

    public function getHtmlContentOnly(Token $token): ?string
    {
        return Token::TEXT_TYPE === $token->getType()
            ? $this->stripUnnecessaryTagsAndSavePositions($token->getValue())
            : null;
    }

    /**
     * @return HtmlTagDto[]
     */
    public function getParsedHtmlTags(string $html): array
    {
        $ret = [];

        if (preg_match_all(self::HTML_TAG_PATTERN, $html, $matches, self::REGEX_FLAGS)) {
            foreach ($matches as $match) {
                $ret[] = (new HtmlTagDto())
                    ->setTag($match['tag'][0])
                    ->setHtmlLine($match[0][0])
                    ->setOffset($match[0][1]);
            }
        }

        return $ret;
    }

    public function getTwigcsOffset(string $html, int $length): OffsetDto
    {
        $substr = mb_substr($html, 0, $length);
        $lines = explode("\n", $substr);
        $linesCount = count($lines) - 1;

        return (new OffsetDto())
            ->setLine($linesCount)
            ->setColumn(mb_strlen($lines[$linesCount]));
    }

    public function isInsideUnnecessaryTag(int $position): bool
    {
        foreach ($this->unnecessaryTagsPositions as $tagsPosition) {
            if ($tagsPosition[0] <= $position && $position < $tagsPosition[1]) {
                return true;
            }
        }

        return false;
    }

    public function stripUnnecessaryTagsAndSavePositions(string $html): string
    {
        $tags = [];
        $this->unnecessaryTagsPositions = [];

        foreach (self::UNNECESSARY_TAGS as $tag) {
            $tags[$tag] = function ($m) { return $this->replaceToTwigcsStringPad($m[0]); };
        }

        return preg_replace_callback_array(
            $tags,
            str_replace("\r", '', $html),
            -1,
            $count,
            self::REGEX_FLAGS
        );
    }

    private function replaceToTwigcsStringPad(array $match): string
    {
        $html = $match[0];
        $offset = $match[1];

        $matchLength = mb_strlen($html);
        $matchLinesCount = mb_substr_count($html, "\n");

        $this->unnecessaryTagsPositions[] = [
            $offset, $offset + $matchLength,
        ];

        return str_pad('', $matchLength, 'A') . str_repeat("\n", $matchLinesCount);
    }
}
