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

final class QuoteInAttributeRule extends AbstractRule implements RuleInterface
{
    /** @var string */
    private $pattern = "#=\s*(?<offset>')#";

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

        while (!$tokens->isEOF()) {
            $token = $tokens->getCurrent();

            if ($content = $this->htmlUtil->getHtmlContentOnly($token)) {
                foreach ($this->htmlUtil->getParsedHtmlTags($content) as $tag) {
                    foreach ($this->getViolationQuotes($tag->getHtmlLine()) as $quote) {
                        $offset = $this->htmlUtil->getTwigcsOffset($content, $tag->getOffset() + $quote['offset'][1]);

                        $violations[] = $this->createViolation(
                            $tokens->getSourceContext()->getPath(),
                            $token->getLine() + $offset->getLine(),
                            $offset->getColumn(),
                            sprintf(Ruleset::ERROR_APOSTROPHE_IN_ATTRIBUTE, $tag->getTag())
                        );
                    }
                }
            }

            $tokens->next();
        }

        return $violations;
    }

    private function getViolationQuotes(string $html): array
    {
        return preg_match_all($this->pattern, $html, $quotes, HtmlUtil::REGEX_FLAGS)
            ? $quotes
            : [];
    }
}
