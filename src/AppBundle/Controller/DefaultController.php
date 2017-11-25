<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DefaultController
 *
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * Homepage
     * @Route(
     *     path="/",
     *     name="homepage",
     *     methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('@App/default/homepage.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * Who online
     * @Route(
     *     path="/who-online",
     *     name="who-online",
     *     methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function whoAction()
    {
        // Rights
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        // Get socket list
        $list = $this->get('online.manager')->getSessionList();
        $list = array_reverse($list);

        // View
        return $this->render('@App/default/who.html.twig', [
            'list' => $list,
        ]);
    }

    /**
     * Manifest.json
     * @Route(
     *     path="/manifest.json",
     *     name="manifest",
     *     methods={"GET"})
     * @return JsonResponse
     */
    public function manifestAction()
    {
        $translator = $this->get('translator');

        $data = [
            'short_name' => $translator->trans('app_name', [], 'app'),
            'name' => $translator->trans('app_name', [], 'app'),
            'icons' => [
                [
                    'src' => './img/icons/logo192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                ],
                [
                    'src' => './img/icons/logo512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                ],
            ],
            'start_url' => '/',
            'display' => 'standalone',
            'theme_color' => '#000000',
            'background_color' => '#ffffff',
        ];

        return new JsonResponse($data);
    }
}
