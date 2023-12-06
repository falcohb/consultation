<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param array<int, array<int, string>> $data
     * @param array<string> $header
     */
    public function export(array $data, string $filename, array $header = []): StreamedResponse
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        return new StreamedResponse(function () use ($data, $header): void {
            /** @var resource $outputStream */
            $outputStream = fopen('php://output', 'wb');
            $delimiter = ',';

            if (!empty($header)) {
                fputcsv($outputStream, $header, $delimiter);
            }

            foreach ($data as $item) {
                fputcsv($outputStream, $item, $delimiter);
            }
        });
    }

    /**
     * @return array<array<string>>
     */
    public function import(UploadedFile $file, bool $hasHeader = true): array
    {
        /** @var array<int, string> $content */
        $content = file($file->getPath() . '/' . $file->getFilename());

        if ($hasHeader) {
            // Removes the header line from the content
            /** @var array<int|string> $header */
            /** @phpstan-ignore-next-line */
            $header = str_getcsv(array_shift($content));
        }

        $data = [];
        foreach ($content as $line) {
            $item = str_getcsv($line);
            $data[] = isset($header) ? array_combine($header, $item) : $item;
        }

        /** @var array<array<string>> */
        return $data;
    }
}
