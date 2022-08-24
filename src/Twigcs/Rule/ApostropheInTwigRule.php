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

final class ApostropheInTwigRule extends AbstractRule implements RuleInterface
{
    /** @var string */
    private $patternTwigTags = '#\{(\{|%).*?(\}|%)\}#s';

    /** @var string */
    private $patternQuotes = '#(?<offset>")[^"]*"#s';

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

        $content = $this->htmlUtil->stripUnnecessaryTagsAndSavePositions($tokens->getSourceContext()->getCode());

        foreach ($this->getMatches($this->patternTwigTags, $content) as $tag) {
            if ($this->htmlUtil->isInsideUnnecessaryTag($tag[0][1])) {
                continue;
            }

            foreach ($this->getMatches($this->patternQuotes, $tag[0][0]) as $quote) {
                $offset = $this->htmlUtil->getTwigcsOffset($content, $tag[0][1] + $quote['offset'][1]);

                $violations[] = $this->createViolation(
                    $tokens->getSourceContext()->getPath(),
                    $offset->getLine(),
                    $offset->getColumn(),
                    Ruleset::ERROR_QUOTE_IN_TWIG
                );
            }
        }

        return $violations;
    }

    private function getMatches(string $pattern, string $content): array
    {
        return preg_match_all($pattern, $content, $matches, HtmlUtil::REGEX_FLAGS)
            ? $matches
            : [];
    }
}
