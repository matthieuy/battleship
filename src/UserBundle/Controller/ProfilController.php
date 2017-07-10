<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use UserBundle\Entity\User;
use UserBundle\Form\Type\ProfilType;

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

        // View
        return $this->render('@User/Profil/edit.html.twig', [
            'user' => $user,
            'isMe' => $user->getId() == $this->getUser()->getId(),
            'form' => $form->createView(),
        ]);
    }
}
