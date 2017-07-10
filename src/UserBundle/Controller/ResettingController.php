<?php

namespace UserBundle\Controller;

use FOS\UserBundle\Controller\ResettingController as BaseCtrl;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ResettingController
 * @package UserBundle\Controller
 */
class ResettingController extends BaseCtrl
{
    /**
     * Request a new password
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function requestAction()
    {
        // Already auth
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        return parent::requestAction();
    }

    /**
     * Send mail with reset link
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function sendEmailAction(Request $request)
    {
        // Already auth
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        return parent::sendEmailAction($request);
    }

    /**
     * Check email
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function checkEmailAction(Request $request)
    {
        // Already auth
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        return parent::checkEmailAction($request);
    }

    /**
     * Reset the password
     * @param Request $request
     * @param string  $token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resetAction(Request $request, $token)
    {
        try {
            return parent::resetAction($request, $token);
        } catch (NotFoundHttpException $e) {
            return $this->redirectToRoute('fos_user_security_login');
        }
    }
}
