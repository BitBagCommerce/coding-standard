<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\CodingStandard\Twigcs\Rule;

use BitBag\CodingStandard\Twigcs\Dto\HtmlTagDto;
use BitBag\CodingStandard\Twigcs\Ruleset\Ruleset;
use BitBag\CodingStandard\Twigcs\Util\HtmlUtil;
use FriendsOfTwig\Twigcs\Rule\AbstractRule;
use FriendsOfTwig\Twigcs\Rule\RuleInterface;
use FriendsOfTwig\Twigcs\TwigPort\TokenStream;

final class VoidHtmlTagRule extends AbstractRule implements RuleInterface
{
    private const VOID_HTML_TAGS = [
        'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'
    ];

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
                    if ($this->isViolation($tag)) {
                        $offset = $this->htmlUtil->getTwigcsOffset($content, $tag->getOffset());

                        $violations[] = $this->createViolation(
                            $tokens->getSourceContext()->getPath(),
                            $token->getLine() + $offset->getLine(),
                            $offset->getColumn(),
                            sprintf(Ruleset::UNCLOSED_VOID_HTML_TAG, $tag->getTag())
                        );
                    }
                }
            }

            $tokens->next();
        }

        return $violations;
    }

    private function isViolation(HtmlTagDto $tag): bool
    {
        return in_array($tag->getTag(), self::VOID_HTML_TAGS)
            && !preg_match('#/\s*>$#', $tag->getHtmlLine());
    }
}
