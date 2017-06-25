<?php

namespace UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as ControllerBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SecurityController
 *
 * @package UserBundle\Controller
 */
class SecurityController extends ControllerBase
{
    /**
     * Login page
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        // Already connected
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        return parent::loginAction($request);
    }
}
