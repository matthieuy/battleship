<?php

namespace UserBundle\Helper;

use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use UserBundle\Entity\User;

/**
 * Class AvatarHelper
 * @package UserBundle\Helper
 */
class AvatarHelper
{
    private $rootPath;
    private $nbAvatar = 21;

    /**
     * AvatarHelper constructor DI
     * @param string $rootPath
     */
    public function __construct($rootPath)
    {
        $this->rootPath = $rootPath;
    }

    /**
     * Get Binary response
     * @param User    $user
     * @param integer $size
     *
     * @return BinaryFileResponse
     */
    public function getBinaryResponse(User $user, $size)
    {
        $uploadDir = realpath($this->rootPath.'/var/avatars');
        $filename = $user->getId().'-'.$size.'.png';
        $outPath = $uploadDir.'/'.$filename;

        if (!file_exists($outPath)) {
            $sourcePath = $uploadDir.'/'.$user->getId();

            if (!file_exists($sourcePath)) {
                $randomAvatar = rand(1, $this->nbAvatar).'.png';
                copy(realpath($this->rootPath.'/src/UserBundle/Resources/avatars').'/'.$randomAvatar, $sourcePath);
            }

            $this->makeAvatar($sourcePath, $outPath, $size);
        }

        $response = new BinaryFileResponse($outPath);
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }

    /**
     * Make avatar from source img
     * @param string  $source
     * @param string  $dest
     * @param integer $size
     */
    private function makeAvatar($source, $dest, $size)
    {
        $manager = new ImageManager(['driver' => 'gd']);
        $image = $manager->make($source);
        $image
            ->fit($size, $size, function (Constraint $constraint) {
                $constraint->aspectRatio();
            })
            ->save($dest)
            ->destroy();

        chmod($dest, 0777);
    }
}
