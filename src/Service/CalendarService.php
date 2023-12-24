<?php

declare(strict_types=1);

namespace App\Service;

class CalendarService
{
    public function generateICalendarLink(
        string $eventName,
        string $eventDescription,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        ?string $location = null,
        ?string $url = null
    ): string {
        $startDate = $startDate->setTimezone(new \DateTimeZone('Europe/Brussels'));
        $endDate = $endDate->setTimezone(new \DateTimeZone('Europe/Brussels'));

        $icsContent = "BEGIN:VCALENDAR
VERSION:2.0
CALSCALE:GREGORIAN
BEGIN:VEVENT
SUMMARY:{$eventName}
DESCRIPTION:{$eventDescription}
DTSTART;TZID=Europe/Brussels:" . $startDate->format('Ymd\THis') . '
DTEND;TZID=Europe/Brussels:' . $endDate->format('Ymd\THis');

        if ($location) {
            $icsContent .= "\nLOCATION:{$location}";
        }

        if ($url) {
            $icsContent .= "\nURL;VALUE=URI:{$url}";
        }

        $icsContent .= "\nEND:VEVENT\nEND:VCALENDAR";

        return $icsContent;
    }
}
