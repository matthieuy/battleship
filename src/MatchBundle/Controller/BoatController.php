<?php

namespace MatchBundle\Controller;

use Intervention\Image\Constraint;
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
        // Personal options
        $boxSize = 20;
        $displayGrid = false;
        if ($this->getUser()) {
            $boxSize = $this->getUser()->getOption('boxSize', $boxSize);
            $displayGrid = $this->getUser()->getOption('displayGrid', $displayGrid);
        }

        // View (CSS content)
        $response = $this->render('@Match/Match/game.css.twig', [
            'widthBox' => $boxSize,
            'displayGrid' => $displayGrid,
            'game' => $game,
        ]);
        $response->headers->set('Content-Type', 'text/css');

        return $response;
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
            $img = $this->changeColor($sourcePath, [255, 255, 255], $color);

            // Resize
            $manager = new ImageManager(['driver' => 'gd']);
            $image = $manager->make($img);
            $image
                ->resize(8 * $size, 7 * $size) // 8x7 : number of sprite in source img
                ->save($destPath)
                ->destroy();
        }

        // Response
        $this->getResponse($request, $destPath);
    }

    /**
     * Get the rocket image
     * @param Request $request
     * @param string  $color
     * @param integer $size
     *
     * @Route(
     *     name="match.rocket.img",
     *     path="/img/rocket/{color}-{size}.png",
     *     methods={"GET"},
     *     requirements={"color": "([0-9A-F]{6})", "size": "([2-6]0)"},
     *     defaults={"size": "60"})
     * @return Response
     */
    public function rocketImageAction(Request $request, $color, $size = 60)
    {
        // Path
        $rootDir = $this->get('kernel')->getRootDir();
        $sourcePath = realpath($rootDir.'/../web/img/rocket.png');

        $destDir = realpath($rootDir.'/../var/boats');
        $destPath = "$destDir/rocket-$color-$size.png";

        // Create img
        if (!file_exists($destPath)) {
            $img = $this->changeColor($sourcePath, [255, 0, 0], $color);

            // Resize
            $manager = new ImageManager(['driver' => 'gd']);
            $image = $manager->make($img);
            $image
                ->resize($size / 2, null, function (Constraint $constraint) {
                    $constraint->aspectRatio();
                })
                ->save($destPath)
                ->destroy();
        }

        // Response
        return $this->getResponse($request, $destPath);
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
     *     requirements={"size": "([2-6]0)"},
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
        return $this->getResponse($request, $destPath);
    }

    /**
     * Change color and resize
     * @param string $sourcePath
     * @param array  $colorInit RGB values
     * @param string $colorDest Hexa color
     *
     * @return resource
     */
    private function changeColor($sourcePath, $colorInit, $colorDest)
    {
        // Dest color
        $red = hexdec(substr($colorDest, 0, 2));
        $green = hexdec(substr($colorDest, 2, 2));
        $blue = hexdec(substr($colorDest, 4, 2));

        // Change color
        $img = imagecreatefrompng($sourcePath);
        $white = imagecolorclosest($img, $colorInit[0], $colorInit[1], $colorInit[2]);
        imagecolorset($img, $white, $red, $green, $blue);

        return $img;
    }

    /**
     * Get binary response
     * @param Request $request
     * @param string  $destPath
     *
     * @return BinaryFileResponse
     */
    private function getResponse(Request $request, $destPath)
    {
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
}
