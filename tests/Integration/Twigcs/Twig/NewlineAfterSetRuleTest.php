<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\CodingStandard\Tests\Integration\Twigcs\Twig;

use BitBag\CodingStandard\Tests\Integration\BaseIntegrationTest;
use BitBag\CodingStandard\Twigcs\Rule\Twig\NewlineAfterSetRule;
use BitBag\CodingStandard\Twigcs\Ruleset\Ruleset;
use BitBag\CodingStandard\Twigcs\Util\HtmlUtil;
use FriendsOfTwig\Twigcs\Validator\Violation;

class NewlineAfterSetRuleTest extends BaseIntegrationTest
{
    /** @var NewlineAfterSetRule */
    private $rule;

    public function setUp(): void
    {
        parent::setUp();

        $this->rule = new NewlineAfterSetRule(Violation::SEVERITY_ERROR, new HtmlUtil());
    }

    public function test_it_returns_violation_when_is_no_new_line_after_set()
    {
        $html = "<div></div> {% set var = 'value' %} {{ var }} ";
        $tokenStream = $this->getFinalTokenStream($html);

        $violations = $this->rule->check($tokenStream);

        self::assertIsArray($violations);
        self::assertCount(1, $violations);
        self::assertInstanceOf(Violation::class, $violations[0]);

        self::assertEquals(1, $violations[0]->getLine());
        self::assertEquals(37, $violations[0]->getColumn());
        self::assertEquals(Ruleset::ERROR_NO_NEW_LINE_AFTER_SET, $violations[0]->getReason());
    }

    public function test_its_ok_when_is_new_line_after_set()
    {
        $html = "<div></div> {% set var = 'value' %}
                {{ var }} ";
        $tokenStream = $this->getFinalTokenStream($html);

        $violations = $this->rule->check($tokenStream);

        self::assertIsArray($violations);
        self::assertCount(0, $violations);
    }
}
