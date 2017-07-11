<?php

namespace MatchBundle\Controller;

use Intervention\Image\ImageManager;
use MatchBundle\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BoatController
 * @package MatchBundle\Controller
 */
class BoatController extends Controller
{
    /**
     * Dynamique CSS
     * @param Game $game
     *
     * @Route(
     *     name="match.css",
     *     path="/game/{slug}.css",
     *     methods={"GET"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"})
     * @return Response
     */
    public function cssAction(Game $game)
    {
        // Set text/css response
        $response = new Response();
        $response->headers->set('Content-Type', 'text/css');

        // View with CSS response
        return $this->render('@Match/Match/game.css.twig', [
            'widthBox' => 20,
            'game' => $game,
        ], $response);
    }

    /**
     * Get the boat image
     * @param Request $request
     * @param string  $color
     * @param integer $size
     *
     * @Route(
     *     name="match.boat.img",
     *     path="/img/boats/{color}-{size}.png",
     *     methods={"GET"},
     *     requirements={"color": "([0-9A-F]{6})", "size": "([2-6]0)"},
     *     defaults={"size": "60"})
     * @return Response
     */
    public function boatImageAction(Request $request, $color, $size = 60)
    {
        // Path
        $rootDir = $this->get('kernel')->getRootDir();
        $sourcePath = realpath($rootDir.'/../web/img/boat.png');

        $destDir = realpath($rootDir.'/../var/boats');
        $destPath = "$destDir/$color-$size.png";

        // Create img
        if (!file_exists($destPath)) {
            $this->changeColor($sourcePath, $destPath, $color, $size);
        }

        // Response
        $response = new BinaryFileResponse($destPath);
        $filemtime = new \DateTime();
        $filemtime->setTimestamp(filemtime($destPath));
        $response
            ->setLastModified($filemtime)
            ->setEtag(md5_file($destPath))
            ->isNotModified($request);

        return $response;
    }

    /**
     * Get the explose image
     * @param Request $request
     * @param integer $size
     *
     * @Route(
     *     name="match.explose.img",
     *     path="/img/explose-{size}.png",
     *     methods={"GET"},
     *     requirements={"size": "(^[2-6]0$)"},
     *     defaults={"size": "60"})
     * @return Response
     */
    public function exploseImageAction(Request $request, $size = 60)
    {
        // Path
        $rootDir = $this->get('kernel')->getRootDir();
        $sourcePath = realpath($rootDir.'/../web/img/explose.png');

        $destDir = realpath($rootDir.'/../var/boats');
        $destPath = "$destDir/explose-$size.png";

        // Create img
        if (!file_exists($destPath)) {
            $manager = new ImageManager(['driver' => 'gd']);
            $image = $manager->make($sourcePath);
            $image
                ->resize(12 * $size, 2 * $size) // 12x2 : number of sprite in source img
                ->save($destPath)
                ->destroy();
        }

        // Response
        $response = new BinaryFileResponse($destPath);
        $filemtime = new \DateTime();
        $filemtime->setTimestamp(filemtime($destPath));
        $response
            ->setLastModified($filemtime)
            ->setEtag(md5_file($destPath))
            ->isNotModified($request);

        return $response;
    }

    /**
     * Change color and resize
     * @param string $source Source path img
     * @param string $destPath Dest path img
     * @param string $color Color
     * @param integer $size Size of box
     */
    private function changeColor($source, $destPath, $color, $size)
    {
        // Dest color
        $red = hexdec(substr($color, 0, 2));
        $green = hexdec(substr($color, 2, 2));
        $blue = hexdec(substr($color, 4, 2));

        // Change color
        $img = imagecreatefrompng($source);
        $white = imagecolorclosest($img, 255, 255, 255);
        imagecolorset($img, $white, $red, $green, $blue);

        // Resize
        $manager = new ImageManager(['driver' => 'gd']);
        $image = $manager->make($img);
        $image
            ->resize(8 * $size, 7 * $size) // 8x7 : number of sprite in source img
            ->save($destPath)
            ->destroy();
    }
}
