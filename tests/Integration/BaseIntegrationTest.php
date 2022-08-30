<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\CodingStandard\Tests\Integration;

use FriendsOfTwig\Twigcs\TwigPort\Source;
use FriendsOfTwig\Twigcs\TwigPort\TokenStream;
use PHPUnit\Framework\TestCase;

abstract class BaseIntegrationTest extends TestCase
{
    protected function getFinalTokenStream(string $html, array $tokens = []): TokenStream
    {
        return new TokenStream(
            $tokens,
            new Source($html, 'test', 'inlineHtml')
        );
    }
}
