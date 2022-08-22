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
    private const UNNECESSARY_TAGS = [
        '#<!--(.*?)-->#s',
        '#<!\[CDATA\[(.*?)\]\]>#s',
        '#<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>#s',
        '#<\s*script\s*>(.*?)<\s*/\s*script\s*>#s',
        '#<\s*style[^>]*[^/]>(.*?)<\s*/\s*style\s*>#s',
        '#<\s*style\s*>(.*?)<\s*/\s*style\s*>#s',
    ];

    private const HTML_TAG_PATTERN = '#</?\s*(?<tag>\w+)[^>]*>#s';

    public function getHtmlContentOnly(Token $token): ?string
    {
        return Token::TEXT_TYPE === $token->getType()
            ? $this->stripUnnecessaryTags($token->getValue())
            : null;
    }

    /**
     * @return HtmlTagDto[]
     */
    public function getParsedHtmlTags(string $html): array
    {
        $ret = [];

        if (preg_match_all(self::HTML_TAG_PATTERN, $html, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
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

    private function stripUnnecessaryTags(string $html): string
    {
        $tags = [];

        foreach (self::UNNECESSARY_TAGS as $tag) {
            $tags[$tag] = function ($m) { return $this->replaceToTwigcsStringPad($m[0]); };
        }

        return preg_replace_callback_array(
            $tags,
            str_replace("\r", '', $html)
        );
    }

    private function replaceToTwigcsStringPad(string $match): string
    {
        $newLinesCount = mb_substr_count($match, "\n");

        return str_pad('', mb_strlen($match), 'A') . str_repeat("\n", $newLinesCount);
    }
}
