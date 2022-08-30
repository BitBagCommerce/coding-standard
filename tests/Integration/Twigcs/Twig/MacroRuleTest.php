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
use BitBag\CodingStandard\Twigcs\Rule\Twig\MacroRule;
use BitBag\CodingStandard\Twigcs\Ruleset\Ruleset;
use FriendsOfTwig\Twigcs\TwigPort\Token;
use FriendsOfTwig\Twigcs\Validator\Violation;

class MacroRuleTest extends BaseIntegrationTest
{
    /** @var MacroRule */
    private $rule;

    public function setUp(): void
    {
        parent::setUp();

        $this->rule = new MacroRule(Violation::SEVERITY_ERROR);
    }

    public function test_it_returns_violation_when_are_multiple_macros_in_the_same_file()
    {
        $html = "{% macro button(name, value, type='text', size=20) %}
<div> </div>
{% macro textarea(name, value, type='text', size=20) %}";

        $tokenStream = $this->getFinalTokenStream($html, [
            new Token(Token::BLOCK_START_TYPE, '', 1, 1),
            new Token(Token::NAME_TYPE, 'macro', 1, 4),
            new Token(Token::BLOCK_END_TYPE, '', 1, 52),

            new Token(Token::BLOCK_START_TYPE, '', 3, 1),
            new Token(Token::NAME_TYPE, 'macro', 3, 4),
            new Token(Token::BLOCK_END_TYPE, '', 3, 54),

            new Token(Token::EOF_TYPE, '', 3, 56),
        ]);

        $violations = $this->rule->check($tokenStream);

        self::assertIsArray($violations);
        self::assertCount(1, $violations);
        self::assertInstanceOf(Violation::class, $violations[0]);

        self::assertEquals(3, $violations[0]->getLine());
        self::assertEquals(4, $violations[0]->getColumn());
        self::assertEquals(Ruleset::ERROR_MULTIPLE_MACROS, $violations[0]->getReason());
    }

    public function test_it_returns_violation_when_is_macro_in_the_template_file()
    {
        $html = "{% macro button(name, value, type='text', size=20) %}
<div> </div>
{% _self.button('someName', 'someValue') %}";

        $tokenStream = $this->getFinalTokenStream($html, [
            new Token(Token::BLOCK_START_TYPE, '', 3, 1),
            new Token(Token::NAME_TYPE, '_self', 3, 4),
            new Token(Token::PUNCTUATION_TYPE, '.', 3, 9),
            new Token(Token::NAME_TYPE, 'button', 3, 10),
            new Token(Token::PUNCTUATION_TYPE, '(', 3, 16),
            new Token(Token::PUNCTUATION_TYPE, ')', 3, 40),
            new Token(Token::BLOCK_END_TYPE, '', 3, 42),

            new Token(Token::EOF_TYPE, '', 3, 44),
        ]);

        $violations = $this->rule->check($tokenStream);

        self::assertIsArray($violations);
        self::assertCount(1, $violations);
        self::assertInstanceOf(Violation::class, $violations[0]);

        self::assertEquals(3, $violations[0]->getLine());
        self::assertEquals(4, $violations[0]->getColumn());
        self::assertEquals(Ruleset::ERROR_MACRO_IN_TEMPLATE, $violations[0]->getReason());
    }

    public function test_its_ok_when_macro_is_in_the_separated_file()
    {
        $html = "{% macro button(name, value, type='text', size=20) %}";

        $tokenStream = $this->getFinalTokenStream($html, [
            new Token(Token::BLOCK_START_TYPE, '', 1, 1),
            new Token(Token::NAME_TYPE, 'macro', 1, 4),
            new Token(Token::BLOCK_END_TYPE, '', 1, 52),

            new Token(Token::EOF_TYPE, '', 1, 54),
        ]);

        $violations = $this->rule->check($tokenStream);

        self::assertIsArray($violations);
        self::assertCount(0, $violations);
    }
}
