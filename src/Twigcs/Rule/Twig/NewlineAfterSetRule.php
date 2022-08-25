<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\CodingStandard\Twigcs\Rule\Twig;

use BitBag\CodingStandard\Twigcs\Ruleset\Ruleset;
use BitBag\CodingStandard\Twigcs\Util\HtmlUtil;
use FriendsOfTwig\Twigcs\Rule\AbstractRule;
use FriendsOfTwig\Twigcs\Rule\RuleInterface;
use FriendsOfTwig\Twigcs\TwigPort\TokenStream;

final class NewlineAfterSetRule extends AbstractRule implements RuleInterface
{
    /** @var string */
    private $pattern = '#set[^%}]+%\s*}[^\S\n]*(?<offset>[^\n])#s';

    /** @var HtmlUtil */
    private $htmlUtil;

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

        foreach ($this->getNoNewlinesAfterSet($content) as $noNewLine) {
            if ($this->htmlUtil->isInsideUnnecessaryTag($noNewLine['offset'][1])) {
                continue;
            }

            $offset = $this->htmlUtil->getTwigcsOffset($content, $noNewLine['offset'][1]);

            $violations[] = $this->createViolation(
                $tokens->getSourceContext()->getPath(),
                $offset->getLine(),
                $offset->getColumn(),
                Ruleset::ERROR_NO_NEW_LINE_AFTER_SET
            );
        }

        return $violations;
    }

    private function getNoNewlinesAfterSet(string $content): array
    {
        return preg_match_all($this->pattern, $content, $noNewLines, HtmlUtil::REGEX_FLAGS)
            ? $noNewLines
            : [];
    }
}
