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
use FriendsOfTwig\Twigcs\Rule\AbstractRule;
use FriendsOfTwig\Twigcs\Rule\RuleInterface;
use FriendsOfTwig\Twigcs\TwigPort\TokenStream;

final class LineLengthRule extends AbstractRule implements RuleInterface
{
    /** @var int */
    private $maxLineLength = 120;

    public function check(TokenStream $tokens)
    {
        $violations = [];

        $content = str_replace("\r", '', $tokens->getSourceContext()->getCode());
        $lines = explode("\n", $content);

        foreach ($lines as $lineNumber => $line) {
            if ($this->lineIsTooLong($line)) {
                $violations[] = $this->createViolation(
                    $tokens->getSourceContext()->getPath(),
                    $lineNumber + 1,
                    $this->maxLineLength - 1,
                    Ruleset::ERROR_LINE_TOO_LONG
                );
            }
        }

        return $violations;
    }

    private function lineIsTooLong(string $line): bool
    {
        return mb_strlen($line) > $this->maxLineLength;
    }
}
