<?php declare(strict_types=1);

use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testCanBeCreatedFromValidData(): void
    {
        $this->assertEquals(
            true,
            User::ensureIsValidUser("Haha", "passws")
    );
    }

    public function testCannotBeCreatedFromInvalidLogin(): void
    {
        User::ensureIsValidUser("Ha", "passws");
    }

    public function testCannotBeCreatedFromInvalidPass(): void
    {

        User::ensureIsValidUser("Haha", "pa");

    }
}