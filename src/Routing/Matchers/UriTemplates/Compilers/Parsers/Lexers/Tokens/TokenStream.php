<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/route-matcher/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Matchers\UriTemplates\Compilers\Parsers\Lexers\Tokens;

use InvalidArgumentException;

/**
 * Defines a token stream
 */
class TokenStream
{
    /** @var Token[] The list of tokens */
    private $tokens = [];
    /** @var int The current cursor */
    private $cursor = 0;

    /**
     * @param Token[] $tokens The list of tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * Tests the current token to see if it matches the input type, and optionally the input value, and throws an
     * exception if the token did not match
     *
     * @param string $type The type to check for
     * @param mixed $value The optional value to match against
     * @param string|null $message The exception message to use, otherwise a default one is generated
     *      Any '%s' in the message is first populated with the expected type, and then with the expected value
     * @throws InvalidArgumentException Thrown if the current token didn't match the expected type and value
     */
    public function expect(string $type, $value = null, string $message = null) : void
    {
        if ($this->test($type, $value)) {
            return;
        }

        $currentToken = $this->getCurrent();
        $formattedMessage = '';

        if ($message === null) {
            // Let's create a default message
            $formattedMessage = sprintf(
                'Expected token type %s%s',
                $type,
                $value === null ? '' : " with value \"$value\""
            );

            if ($currentToken === null) {
                $formattedMessage .= ', got end of stream';
            } else {
                $formattedMessage .= sprintf(
                    ', got %s with value \"%s\"',
                    $currentToken->getType(),
                    $currentToken->getValue()
                );
            }
        } else {
            $formattedMessage = sprintf(
                $message,
                $currentToken === null ? 'T_EOF' : $currentToken->getType(),
                $currentToken === null ? 'end of stream' : $currentToken->getValue()
            );
        }

        throw new InvalidArgumentException($formattedMessage);
    }

    /**
     * Gets the current token
     *
     * @return Token|null The current token
     */
    public function getCurrent() : ?Token
    {
        return count($this->tokens) > $this->cursor ? $this->tokens[$this->cursor] : null;
    }

    /**
     * Gets the next token, if there is one
     *
     * @return Token|null The next token, if there is one, otherwise false
     */
    public function next() : ?Token
    {
        return count($this->tokens) > ++$this->cursor ? $this->tokens[$this->cursor] : null;
    }

    /**
     * Gets the next token if the current one matches the input type, and optionally performs a value check
     *
     * @param string $type The type to check for
     * @param mixed $value The optional value to match against
     * @return bool True if the current token is of the input type, otherwise false
     */
    public function nextIfType(string $type, $value = null) : bool
    {
        $currentToken = $this->getCurrent();
        $typeMatches = $currentToken !== null && $currentToken->getType() === $type;

        if ($typeMatches && ($value === null || $currentToken->getValue() === $value)) {
            $this->next();

            return true;
        }

        return false;
    }

    /**
     * Peeks ahead at the next token
     *
     * @param int $lookahead The number of tokens to look ahead
     * @return Token|null The token
     */
    public function peek(int $lookahead = 1) : ?Token
    {
        if ($this->cursor + $lookahead >= count($this->tokens)) {
            return null;
        }

        return $this->tokens[$this->cursor + $lookahead];
    }

    /**
     * Tests the current token to see if it matches the input type, and optionally the input value
     *
     * @param string $type The type to check for
     * @param mixed $value The optional value to match against
     * @return bool True if the current token is of the input type, otherwise false
     */
    public function test(string $type, $value = null) : bool
    {
        $currentToken = $this->getCurrent();
        $typeMatches = $currentToken !== null && $currentToken->getType() === $type;

        if ($typeMatches && ($value === null || $currentToken->getValue() === $value)) {
            return true;
        }

        return false;
    }
}