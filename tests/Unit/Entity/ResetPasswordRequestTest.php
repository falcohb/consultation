<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\ResetPasswordRequest;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

class ResetPasswordRequestTest extends TestCase
{
    use ResetPasswordRequestTrait;

    public function testResetPasswordRequest(): void
    {

        $user = new User();

        $expiresAt = new DateTime('+1 hour');

        $resetPasswordRequest = new ResetPasswordRequest($user, $expiresAt, 'selector123', 'hashedToken123');

        $this->assertInstanceOf(ResetPasswordRequest::class, $resetPasswordRequest);

        $this->assertNull($resetPasswordRequest->getId());
        $this->assertSame($user, $resetPasswordRequest->getUser());
        $this->assertSame($expiresAt, $resetPasswordRequest->getExpiresAt());
        $this->assertSame('hashedToken123', $resetPasswordRequest->getHashedToken());
    }
}
