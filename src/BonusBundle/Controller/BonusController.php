<?php

namespace BonusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BonusController
 * @package BonusBundle\Controller
 */
class BonusController extends Controller
{
    /**
     * Get all weapons in json format
     * @Route(
     *     name="ajax.weapon",
     *     path="/ajax/weapons",
     *     methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxWeaponAction(Request $request)
    {
        // AJAX only
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(['error' => 'Bad request']);
        }

        $weapons = $this->get('weapon.registry')->getAllWeapons();
        $list = [];
        foreach ($weapons as $w) {
            $list[] = $w->toArray();
        }

        return new JsonResponse($list);
    }
}
