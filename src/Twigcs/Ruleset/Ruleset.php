<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\CodingStandard\Twigcs\Ruleset;

use BitBag\CodingStandard\Twigcs\Rule as BitBagRule;
use BitBag\CodingStandard\Twigcs\Util\HtmlUtil;
use FriendsOfTwig\Twigcs\RegEngine\RulesetBuilder;
use FriendsOfTwig\Twigcs\RegEngine\RulesetConfigurator;
use FriendsOfTwig\Twigcs\Rule as TwigcsRule;
use FriendsOfTwig\Twigcs\Ruleset\RulesetInterface;
use FriendsOfTwig\Twigcs\Validator\Violation;

final class Ruleset implements RulesetInterface
{
    public const ERROR_UNCLOSED_VOID_HTML_TAG = '<%s> HTML void tag should be closed.';
    public const ERROR_MULTIPLE_MACROS = 'There should only be one macro in the same file.';
    public const ERROR_MACRO_IN_TEMPLATE = 'There should not be a macro in the template file.';
    public const ERROR_MULTIPLE_WHITESPACES = 'There should not be so many whitespaces in <%s> HTML tag attributes.';
    public const ERROR_APOSTROPHE_IN_ATTRIBUTE = 'A quote should be used instead of apostrophe in <%s> HTML tag attributes.';
    public const ERROR_NO_SPACE_BETWEEN_ATTRIBUTES = 'There should be a whitespace between attributes in <%s> HTML tag.';
    public const ERROR_NO_NEW_LINE_AT_THE_END = 'There should be a new line at the end of the file.';
    public const ERROR_LINE_TOO_LONG = 'Line should be up to %d characters long.';
    public const ERROR_MULTIPLE_EMPTY_LINES = 'There should not be so many empty lines.';
    public const ERROR_NO_NEW_LINE_AFTER_SET = 'There should be a new line after twig {% set %} declaration.';
    public const ERROR_QUOTE_IN_TWIG = 'An apostrophe should be used instead of quote in Twig tag.';

    /** @var int */
    private $twigMajorVersion;

    /** @var string[] */
    private $forbiddenTwigFunctions = [
        'dump',
    ];

    public function __construct(int $twigMajorVersion)
    {
        $this->twigMajorVersion = $twigMajorVersion;
    }

    public function getRules()
    {
        $rulesetConfigurator = (new RulesetConfigurator())
            ->setTwigMajorVersion($this->twigMajorVersion);
        $twigcsRulesetBuilder = new RulesetBuilder($rulesetConfigurator);

        $htmlUtil = new HtmlUtil();

        return [
            new BitBagRule\EmptyLinesRule(Violation::SEVERITY_ERROR, $htmlUtil),
            new BitBagRule\LineLengthRule(Violation::SEVERITY_ERROR, $htmlUtil),
            new BitBagRule\NewlineAtTheEndRule(Violation::SEVERITY_ERROR, $htmlUtil),

            new BitBagRule\Html\ApostropheInAttributesRule(Violation::SEVERITY_ERROR, $htmlUtil),
            new BitBagRule\Html\MultiWhitespaceInAttributesRule(Violation::SEVERITY_ERROR, $htmlUtil),
            new BitBagRule\Html\UnclosedVoidTagsRule(Violation::SEVERITY_ERROR, $htmlUtil),
            new BitBagRule\Html\WhitespaceInAttributesRule(Violation::SEVERITY_ERROR, $htmlUtil),

            new BitBagRule\Twig\MacroRule(Violation::SEVERITY_ERROR),
            new BitBagRule\Twig\NewlineAfterSetRule(Violation::SEVERITY_ERROR, $htmlUtil),
            new BitBagRule\Twig\QuoteInTwigRule(Violation::SEVERITY_ERROR, $htmlUtil),

            new TwigcsRule\ForbiddenFunctions(Violation::SEVERITY_ERROR, $this->forbiddenTwigFunctions),
            new TwigcsRule\LowerCaseVariable(Violation::SEVERITY_ERROR),
            new TwigcsRule\RegEngineRule(Violation::SEVERITY_ERROR, $twigcsRulesetBuilder->build()),
            new TwigcsRule\TrailingSpace(Violation::SEVERITY_ERROR),
            new TwigcsRule\UnusedMacro(Violation::SEVERITY_WARNING),
            new TwigcsRule\UnusedVariable(Violation::SEVERITY_WARNING),
        ];
    }
}
