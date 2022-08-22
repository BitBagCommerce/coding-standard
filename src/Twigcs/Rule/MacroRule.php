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
use FriendsOfTwig\Twigcs\Rule\AbstractRule;
use FriendsOfTwig\Twigcs\Rule\RuleInterface;
use FriendsOfTwig\Twigcs\TwigPort\Token;
use FriendsOfTwig\Twigcs\TwigPort\TokenStream;
use FriendsOfTwig\Twigcs\Validator\Violation;

final class MacroRule extends AbstractRule implements RuleInterface
{
    private const TWIG_TAG_MACRO = 'macro';
    private const TWIG_TAG_SELF = '_self';

    /** @var bool */
    private $isMacro;

    public function check(TokenStream $tokens)
    {
        $violations = [];
        $this->isMacro = false;
        $path = $tokens->getSourceContext()->getPath();

        while (!$tokens->isEOF()) {
            $token = $tokens->getCurrent();

            if ($violation = $this->checkMultipleMacros($token, $path)) {
                $violations[] = $violation;
            }
            if ($violation = $this->checkSelfMacro($tokens, $token, $path)) {
                $violations[] = $violation;
            }

            $tokens->next();
        }

        return $violations;
    }

    private function checkMultipleMacros(Token $token, string $filename): ?Violation
    {
        if (
            Token::NAME_TYPE === $token->getType()
            && self::TWIG_TAG_MACRO === $token->getValue()
        ) {
            if (!$this->isMacro) {
                $this->isMacro = true;
            } else {
                return $this->createViolation(
                    $filename,
                    $token->getLine(),
                    $token->getColumn(),
                    Ruleset::MULTIPLE_MACROS_IN_THE_SAME_FILE
                );
            }
        }

        return null;
    }

    private function checkSelfMacro(TokenStream $tokens, Token $token, string $filename): ?Violation
    {
        if ($this->isSelfTagUsedForMacro($tokens, $token)) {
            return $this->createViolation(
                $filename,
                $token->getLine(),
                $token->getColumn(),
                Ruleset::MACRO_IN_THE_TEMPLATE
            );
        }

        return null;
    }

    private function isSelfTagUsedForMacro(TokenStream $tokens, Token $token): bool
    {
        return Token::NAME_TYPE === $token->getType()
            && self::TWIG_TAG_SELF === $token->getValue()

            && Token::PUNCTUATION_TYPE === $tokens->look(1)->getType()
            && '.' === $tokens->look(1)->getValue()

            && Token::NAME_TYPE === $tokens->look(2)->getType()

            && Token::PUNCTUATION_TYPE === $tokens->look(3)->getType()
            && '(' === $tokens->look(3)->getValue();
    }
}
