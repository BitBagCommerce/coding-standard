<?php

declare(strict_types=1);

namespace BitBag\CodingStandard\Fixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class FinalClassInEntitiesFixer implements FixerInterface
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\T_FINAL);
    }

    public function isRisky(): bool
    {
        return true;
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_FINAL)) {
                continue;
            }
            $tokens->clearAt($index);
            if ($tokens[$index - 1]->isGivenKind(\T_WHITESPACE)) {
                $tokens->clearAt($index + 1);
            }
            if ($tokens[$index - 2]->isGivenKind(\T_COMMENT)) {
                $tokens->clearAt($index - 1);
            }
        }
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Entities should not be defined as final.',
            [
                new CodeSample(
                    '<?php
                           declare(strict_types=1);
                           namespace App\Entity;
                           final class Product
                           {
                           }
                           ',
                ),
            ],
        );
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function supports(SplFileInfo $file): bool
    {
        return false !== strpos($file->getPath(), 'src/Entity/');
    }
}
