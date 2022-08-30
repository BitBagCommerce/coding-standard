<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\CodingStandard\Tests\Integration\Twigcs\Html;

use BitBag\CodingStandard\Tests\Integration\BaseIntegrationTest;
use BitBag\CodingStandard\Twigcs\Rule\Html\UnclosedVoidTagsRule;
use BitBag\CodingStandard\Twigcs\Ruleset\Ruleset;
use BitBag\CodingStandard\Twigcs\Util\HtmlUtil;
use FriendsOfTwig\Twigcs\Validator\Violation;

class UnclosedVoidTagsRuleTest extends BaseIntegrationTest
{
    /** @var UnclosedVoidTagsRule */
    private $rule;

    public function setUp(): void
    {
        parent::setUp();

        $this->rule = new UnclosedVoidTagsRule(Violation::SEVERITY_ERROR, new HtmlUtil());
    }

    public function test_it_returns_violation_when_tag_is_unclosed()
    {
        $html = 'some content <img src="" alt="">';
        $tokenStream = $this->getFinalTokenStream($html);

        $violations = $this->rule->check($tokenStream);

        self::assertIsArray($violations);
        self::assertCount(1, $violations);
        self::assertInstanceOf(Violation::class, $violations[0]);

        self::assertEquals(1, $violations[0]->getLine());
        self::assertEquals(14, $violations[0]->getColumn());
        self::assertEquals(sprintf(Ruleset::ERROR_UNCLOSED_VOID_HTML_TAG, 'img'), $violations[0]->getReason());
    }

    public function test_its_ok_when_tags_are_closed()
    {
        $html = 'content <img src="" alt="" /> <area  alt=""/> <base /> <br /> <col /> <embed /> <hr /> <input /> '.
            '<link /> <meta /> <param name="" value="" /> <source /> <track src="" /> <wbr />';
        $tokenStream = $this->getFinalTokenStream($html);

        $violations = $this->rule->check($tokenStream);

        self::assertIsArray($violations);
        self::assertCount(0, $violations);
    }
}
