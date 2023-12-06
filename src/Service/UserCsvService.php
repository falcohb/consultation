<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserCsvService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly CsvService $csvService
    ) {
    }

    public function exportCsv(): StreamedResponse
    {
        $users = $this->userRepository->findAll();

        $filename = 'diagnostic_user_export.csv';
        $header = ['first_name', 'last_name', 'email', 'is_verified', 'last_login_at', 'created_at'];

        $data = [];
        foreach ($users as $user) {
            $lastLoginAt = $user->getLastLoginAt() ? $user->getLastLoginAt()->format('d/m/Y H:i') : null;
            $createdAt = $user->getCreatedAt() ? $user->getCreatedAt()->format('d/m/Y H:i') : null;

            $data[] = [
                $user->getFirstName(),
                $user->getLastName(),
                $user->getEmail(),
                $user->getIsVerified(),
                $lastLoginAt,
                $createdAt,
            ];
        }

        /** @var array<array<int, string>> $data */
        return $this->csvService->export($data, $filename, $header);
    }
}
