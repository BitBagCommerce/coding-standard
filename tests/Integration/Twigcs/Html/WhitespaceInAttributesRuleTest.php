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
use BitBag\CodingStandard\Twigcs\Rule\Html\WhitespaceInAttributesRule;
use BitBag\CodingStandard\Twigcs\Ruleset\Ruleset;
use BitBag\CodingStandard\Twigcs\Util\HtmlUtil;
use FriendsOfTwig\Twigcs\Validator\Violation;

class WhitespaceInAttributesRuleTest extends BaseIntegrationTest
{
    /** @var WhitespaceInAttributesRule */
    private $rule;

    public function setUp(): void
    {
        parent::setUp();

        $this->rule = new WhitespaceInAttributesRule(Violation::SEVERITY_ERROR, new HtmlUtil());
    }

    public function test_it_returns_violation_when_is_no_space_between_attributes()
    {
        $html = 'content <span class="someClass"id="someId"> content </span>';
        $tokenStream = $this->getFinalTokenStream($html);

        $violations = $this->rule->check($tokenStream);

        self::assertIsArray($violations);
        self::assertCount(1, $violations);
        self::assertInstanceOf(Violation::class, $violations[0]);

        self::assertEquals(1, $violations[0]->getLine());
        self::assertEquals(32, $violations[0]->getColumn());
        self::assertEquals(sprintf(Ruleset::ERROR_NO_SPACE_BETWEEN_ATTRIBUTES, 'span'), $violations[0]->getReason());
    }

    public function test_its_ok_when_is_exactly_one_space_between_attributes()
    {
        $html = 'content <span class="someClass" id="someId"> content </span>';
        $tokenStream = $this->getFinalTokenStream($html);

        $violations = $this->rule->check($tokenStream);

        self::assertIsArray($violations);
        self::assertCount(0, $violations);
    }
}
