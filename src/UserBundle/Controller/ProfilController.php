<?php

namespace UserBundle\Controller;

use FOS\UserBundle\Form\Type\ChangePasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use UserBundle\Entity\User;
use UserBundle\Form\Type\ProfilType;
use UserBundle\Validator\Jetable;

/**
 * Class ProfilController
 * @package UserBundle\Controller
 */
class ProfilController extends Controller
{
    /**
     * Show a profil
     * @param User $user
     *
     * @Route(
     *     name="user.profil.show",
     *     path="/user/{slug}",
     *     methods={"GET"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(User $user)
    {
        return $this->render('@User/Profil/show.html.twig', [
            'user' => $user,
            'isMe' => $user->getId() == $this->getUser()->getId(),
        ]);
    }

    /**
     * Redirect to profil from route id
     * @param User $user
     *
     * @Route(
     *     name="user.profil.showId",
     *     path="/user/id/{id}",
     *     methods={"GET"},
     *     requirements={"id": "([0-9]+)"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function showByIdAction(User $user)
    {
        return $this->redirectToRoute('user.profil.show', ['slug' => $user->getSlug()]);
    }

    /**
     * Edit a profil
     * @param Request $request
     * @param User    $user
     *
     * @Route(
     *     name="user.profil.edit",
     *     path="/user/{slug}/edit",
     *     methods={"GET", "POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, User $user)
    {
        // Right
        if ($user->getId() !== $this->getUser()->getId() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        // Form
        $form = $this->createForm(ProfilType::class, $user, [
            'action' => $this->generateUrl('user.profil.edit', ['slug' => $user->getSlug()]),
        ])->handleRequest($request);

        // Validate
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->flush();
            $this->addFlash('success', "Profil saved with success !");

            // Redirect
            return $this->redirectToRoute('user.profil.edit', ['slug' => $user->getSlug()]);
        }

        // View
        return $this->render('@User/Profil/edit.html.twig', [
            'user' => $user,
            'isMe' => $user->getId() == $this->getUser()->getId(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit personal info
     * @param Request $request
     * @param User    $user
     *
     * @Route(
     *     name="user.personal.edit",
     *     path="/user/{slug}/edit-personal",
     *     methods={"GET", "POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editPersonalAction(Request $request, User $user)
    {
        // Right
        if ($user->getId() !== $this->getUser()->getId() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        // Form
        $form = $this->createForm(ChangePasswordFormType::class, $user, [
            'action' => $this->generateUrl('user.personal.edit', ['slug' => $user->getSlug()]),
            'validation_groups' => 'ChangePassword',
        ]);
        $form->add('email', EmailType::class, [
            'constraints' => new Jetable(),
        ]);
        $form->handleRequest($request);

        // Validate
        if ($form->isSubmitted() && $form->isValid()) {
            // Save
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            $this->addFlash('success', "Profil saved with success !");

            // Redirect
            return $this->redirectToRoute('user.profil.show', ['slug' => $user->getSlug()]);
        }

        // View
        return $this->render('@User/Profil/edit-personal.html.twig', [
            'user' => $user,
            'isMe' => $user->getId() == $this->getUser()->getId(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Get the avatar img
     * @param Request $request
     * @param User    $user
     * @param integer $size
     *
     * @Route(
     *     name="user.avatar",
     *     path="/user/{id}-{size}.png",
     *     methods={"GET"},
     *     requirements={"id": "([0-9]+)", "size": "(50|60|150)"},
     *     defaults={"size": "60"})
     * @return Response
     */
    public function avatarAction(Request $request, User $user, $size = 60)
    {
        $helper = $this->get('profil.avatar.helper');

        return $helper->getResponse($request, $user, $size);
    }
}
