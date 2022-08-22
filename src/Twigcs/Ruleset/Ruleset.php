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
    public const ERROR_UNCLOSED_VOID_HTML_TAG = 'Unclosed <%s> HTML void tag';
    public const ERROR_MULTIPLE_MACROS = 'Multiple macros in the same file';
    public const ERROR_MACRO_IN_TEMPLATE = 'Macro in the template file';
    public const ERROR_MULTIPLE_WHITESPACES = 'Multiple whitespaces in <%s> HTML tag';
    public const ERROR_APOSTROPHE_IN_ATTRIBUTE = 'Apostrophe instead of quote in <%s> HTML tag attributes';

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
            new BitBagRule\VoidHtmlTagRule(Violation::SEVERITY_ERROR, $htmlUtil),
            new BitBagRule\MacroRule(Violation::SEVERITY_ERROR),
            new BitBagRule\WhitespaceRule(Violation::SEVERITY_ERROR, $htmlUtil),
            new BitBagRule\QuoteHtmlTagAttributeRule(Violation::SEVERITY_ERROR, $htmlUtil),

            new TwigcsRule\RegEngineRule(Violation::SEVERITY_ERROR, $twigcsRulesetBuilder->build()),
            new TwigcsRule\LowerCaseVariable(Violation::SEVERITY_ERROR),
            new TwigcsRule\ForbiddenFunctions(Violation::SEVERITY_ERROR, $this->forbiddenTwigFunctions),
            new TwigcsRule\TrailingSpace(Violation::SEVERITY_ERROR),
            new TwigcsRule\UnusedMacro(Violation::SEVERITY_ERROR),
            new TwigcsRule\UnusedVariable(Violation::SEVERITY_ERROR),
        ];
    }
}
