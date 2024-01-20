<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render('account/delete.html.twig');
    }

    #[Route('/account/delete', name: 'app_account_delete')]
    #[IsGranted('ROLE_USER')]
    public function deleteUser(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User $user */
        $user = $this->getUser();

        $user = $userRepository->find($user->getId());

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $tokenStorage->setToken(null);

        $this->addFlash('success', 'Votre compte a été supprimé!');

        return $this->redirectToRoute('app_home');
    }
}
