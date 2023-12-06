<?php

declare(strict_types=1);

namespace App\Service;

class MailerService
{
    /*
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Security $security
    ) {
    }

    /**
     * @param array<string, string|null> $order
     */
    /* public function sendPurchaseConfirmationEmail(Bilan $bilan, array $order): void
    {
        $user = $this->security->getUser();

        if ($user && $user->getUserIdentifier()) {
            $email = (new TemplatedEmail())
                ->from(new Address('no-reply@cathyassenheim.com', 'Cathy Assenheim'))
                ->to(new Address($user->getUserIdentifier()))
                ->subject('[Diagnostic] Confirmation de votre achat')
                ->htmlTemplate('emails/purchase.html.twig')
                ->context([
                    'bilan' => $bilan,
                    'order' => $order,
                ]);

            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                echo 'An error has occurred while sending email: ' . $e->getMessage();
            }
        } else {
            throw new UserNotFoundException('This user do not exist.');
        }
    }*/
}
