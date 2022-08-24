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

final class MultipleWhitespaceInAttributeRule extends AbstractRule implements RuleInterface
{
    /** @var string */
    private $pattern = '#(?<offset>\s{2,})#';

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

        foreach ($this->htmlUtil->getParsedHtmlTags($content) as $tag) {
            foreach ($this->getViolationSpaces($tag->getHtmlLine()) as $space) {
                $offset = $this->htmlUtil->getTwigcsOffset($content, $tag->getOffset() + $space['offset'][1]);

                $violations[] = $this->createViolation(
                    $tokens->getSourceContext()->getPath(),
                    $offset->getLine(),
                    $offset->getColumn(),
                    sprintf(Ruleset::ERROR_MULTIPLE_WHITESPACES, $tag->getTag())
                );
            }
        }

        return $violations;
    }

    private function getViolationSpaces(string $html): array
    {
        return preg_match_all($this->pattern, $html, $spaces, HtmlUtil::REGEX_FLAGS)
            ? $spaces
            : [];
    }
}
