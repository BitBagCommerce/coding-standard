<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\CodingStandard\Twigcs\Rule;

use BitBag\CodingStandard\Twigcs\Ruleset\Ruleset;
use BitBag\CodingStandard\Twigcs\Util\HtmlUtil;
use FriendsOfTwig\Twigcs\Rule\AbstractRule;
use FriendsOfTwig\Twigcs\Rule\RuleInterface;
use FriendsOfTwig\Twigcs\TwigPort\TokenStream;

final class MultipleEmptyLineRule extends AbstractRule implements RuleInterface
{
    /** @var string */
    private $pattern = '#(?<offset>\n{3,})#s';

    /** @var HtmlUtil */
    private $htmlUtil;

    private $lineNumberOffset = 2;

    public function __construct($severity, HtmlUtil $htmlUtil)
    {
        parent::__construct($severity);

        $this->htmlUtil = $htmlUtil;
    }

    public function check(TokenStream $tokens)
    {
        $violations = [];

        $content = str_replace("\r", '', $tokens->getSourceContext()->getCode());
        $this->htmlUtil->stripUnnecessaryTagsAndSavePositions($content);

        foreach ($this->getMultilines($content) as $multiline) {
            if ($this->htmlUtil->isInsideUnnecessaryTag($multiline['offset'][1])) {
                continue;
            }

            $offset = $this->htmlUtil->getTwigcsOffset($content, $multiline['offset'][1] + $this->lineNumberOffset);

            $violations[] = $this->createViolation(
                $tokens->getSourceContext()->getPath(),
                $offset->getLine(),
                0,
                Ruleset::ERROR_MULTIPLE_EMPTY_LINES
            );
        }

        return $violations;
    }

    private function getMultilines(string $content): array
    {
        return preg_match_all($this->pattern, $content, $multilines, HtmlUtil::REGEX_FLAGS)
            ? $multilines
            : [];
    }
}
