<?php

namespace UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as ControllerBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RegistrationController
 *
 * @package UserBundle\Controller
 */
class RegistrationController extends ControllerBase
{
    /**
     * Register page
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        // Already auth
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('homepage');
        }

        return parent::registerAction($request);
    }

    /**
     * Confirm register
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function confirmedAction()
    {
        // Already auth
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('homepage');
        }

        return parent::confirmedAction();
    }
}
