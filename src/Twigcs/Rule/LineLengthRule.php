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
use FriendsOfTwig\Twigcs\Validator\Violation;

final class LineLengthRule extends AbstractRule implements RuleInterface
{
    /** @var int */
    private $maxLineLength = 120;

    /** @var HtmlUtil */
    private $htmlUtil;

    public function __construct(int $severity, HtmlUtil $htmlUtil)
    {
        parent::__construct($severity);

        $this->htmlUtil = $htmlUtil;
    }

    /**
     * @return Violation[]
     */
    public function check(TokenStream $tokens): array
    {
        $violations = [];

        $content = str_replace("\r", '', $tokens->getSourceContext()->getCode());
        $lines = explode("\n", $content);

        $this->htmlUtil->stripUnnecessaryTagsAndSavePositions($content);
        $currentPosition = 0;

        foreach ($lines as $lineNumber => $line) {
            $lineLength = mb_strlen($line);
            $currentPosition += $lineLength;

            if ($this->isLineTooLong($lineLength) && !$this->htmlUtil->isInsideUnnecessaryTag($currentPosition)) {
                $violations[] = $this->createViolation(
                    $tokens->getSourceContext()->getPath(),
                    $lineNumber + 1,
                    $this->maxLineLength,
                    sprintf(Ruleset::ERROR_LINE_TOO_LONG, $this->maxLineLength)
                );
            }

            ++$currentPosition;
        }

        return $violations;
    }

    private function isLineTooLong(int $lineLength): bool
    {
        return $lineLength > $this->maxLineLength;
    }
}
