<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject\Auth;

use Assert\Assertion;

final class HashedPassword
{
    public const COST = 12;
    private string $hashedPassword;

    private function __construct()
    {
    }

    public function __toString(): string
    {
        return $this->hashedPassword;
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public static function encode(string $plainPassword): self
    {
        $pass = new self();

        $pass->hash($plainPassword);

        return $pass;
    }

    public static function fromHash(string $hashedPassword): self
    {
        $pass = new self();

        $pass->hashedPassword = $hashedPassword;

        return $pass;
    }

    public function match(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->hashedPassword);
    }

    public function toString(): string
    {
        return $this->hashedPassword;
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    private function hash(string $plainPassword): void
    {
        $this->validate($plainPassword);

        $hashedPassword = password_hash($plainPassword, \PASSWORD_BCRYPT, ['cost' => self::COST]);

        if (\is_bool($hashedPassword)) {
            throw new \RuntimeException('Server error hashing password');
        }

        $this->hashedPassword = $hashedPassword;
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    private function validate(string $raw): void
    {
        Assertion::minLength($raw, 6, 'Min 6 characters password');
    }
}
