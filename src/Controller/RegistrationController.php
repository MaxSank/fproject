<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/{_locale<%app.supported_locales%>}/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRoles(User::ROLE_USER);

            if (self::currentLanguage($request) == 'en') {
                $user->setLanguage(User::ENG);
            }
            elseif (self::currentLanguage($request) == 'ru') {
                $user->setLanguage(User::RUS);
            }

            $user->setTheme(User::LIGHT);

            $currentTime = new DateTime('now', new DateTimeZone('Europe/Minsk'));
            $user->setRegistrationDate($currentTime);
            $user->setLastLoginDate($currentTime);

            $user->setStatus(User::NOT_BLOCKED);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'title' => 'Registration Form',
        ]);
    }

    #[Pure]
    static function currentLanguage(Request $request): string
    {
        return $request->getLocale();
    }
}
