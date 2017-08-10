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
     * @param Request $request
     * @param Game    $game
     *
     * @Route(
     *     name="match.css",
     *     path="/game/{slug}.css",
     *     methods={"GET"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"})
     * @return Response
     */
    public function cssAction(Request $request, Game $game)
    {
        // Default value
        $isMobile = $this->isMobile($request);
        $boxSizeDefault = 20;
        $displayGridDefault = false;

        // Personal options
        if ($this->getUser()) {
            $boxSize = $this->getUser()->getOption('boxSize', $boxSizeDefault);
            $displayGrid = $this->getUser()->getOption('displayGrid', $displayGridDefault);
        }

        // Mobile
        if ($isMobile) {
            $boxSize = $boxSizeDefault;
            $displayGrid = true;
        }

        // View (CSS content)
        $boxSize = (isset($boxSize)) ? $boxSize : $boxSizeDefault;
        $response = $this->render('@Match/Match/game.css.twig', [
            'widthBox' => $boxSize,
            'size' => $boxSize * $game->getSize(),
            'borders' => range(0, $game->getSize(), 10),
            'displayGrid' => (isset($displayGrid)) ? $displayGrid : $displayGridDefault,
            'game' => $game,
            'isMobile' => $isMobile,
        ]);
        $response->headers->set('Content-Type', 'text/css');

        // Compress
        $content = $response->getContent();
        $content = str_replace(["    ", "\t", "\r", "\n"], '', $content);
        $response->setContent($content);

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
            $this->resizeImg($img, $destPath, 8 * $size, 7 * $size, false);
        }

        // Response
        return $this->getResponse($request, $destPath);
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
            $this->resizeImg($img, $destPath, $size / 2);
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
            $this->resizeImg($sourcePath, $destPath, 12 * $size, 2 * $size, false);
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
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }

    /**
     * Resize image
     * @param string|resource $source Source image
     * @param string          $destPath Output file
     * @param integer         $width Width (px)
     * @param integer|null    $height Height (px)
     * @param boolean         $keepRatio Keep the aspect ratio
     */
    private function resizeImg($source, $destPath, $width, $height = null, $keepRatio = true)
    {
        // Ration
        if ($keepRatio) {
            $ratioClosure = function (Constraint $constraint) {
                $constraint->aspectRatio();
            };
        } else {
            $ratioClosure = null;
        }

        $manager = new ImageManager(['driver' => 'gd']);
        $image = $manager->make($source);
        $image
            ->resize($width, $height, $ratioClosure)
            ->save($destPath)
            ->destroy();
    }

    /**
     * Check user-agent for mobile device
     * @param Request $request
     *
     * @return boolean
     */
    private function isMobile(Request $request)
    {
        $userAgent = $request->headers->get('user-agent');
        $regex = "/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i";

        return (preg_match($regex, $userAgent) > 0);
    }
}
