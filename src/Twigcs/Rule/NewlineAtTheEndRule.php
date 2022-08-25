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

final class NewlineAtTheEndRule extends AbstractRule implements RuleInterface
{
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

        if ($this->isNoNewline($content)) {
            $offset = $this->htmlUtil->getTwigcsOffset($content, mb_strlen($content));

            $violations[] = $this->createViolation(
                $tokens->getSourceContext()->getPath(),
                $offset->getLine() + 1,
                $offset->getColumn(),
                Ruleset::ERROR_NO_NEW_LINE_AT_THE_END
            );
        }

        return $violations;
    }

    private function isNoNewline(string $content): bool
    {
        return $content && "\n" !== mb_substr($content, -1);
    }
}
