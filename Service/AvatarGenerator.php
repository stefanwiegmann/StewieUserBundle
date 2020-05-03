<?php

namespace Stewie\UserBundle\Service;

use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AvatarGenerator extends AbstractController
{

    public function create($object){

      $path = $this->getParameter('kernel.project_dir').'/var/stewie/user-bundle/avatar/';

       // $string = $object->getSlug();

      switch (ClassUtils::getClass($object)) {
          case "Stewie\UserBundle\Entity\User":
              // Get string from object
              $string = $object->getUsername();
              break;
          case "Stewie\UserBundle\Entity\Group":
              // Get string from object
              $string = $object->getName();
              break;
          case "Stewie\UserBundle\Entity\Role":
              // Get string from object
              $string = str_replace('ROLE_', '', $object->getName());
              break;
          default:
             return false;
      }

      // Convert string to MD5
      $hash = md5(strtolower($string));
      // Get color from first 6 characters
      $color = substr($hash, 0, 6);
      // Create an array to store our boolean "pixel" values
      $pixels = array();

      switch (ClassUtils::getClass($object)) {
          case "Stewie\UserBundle\Entity\User":
              // Make it a symmetrical 5x5 multidimensional array
              for ($i = 0; $i < 5; $i++) {
                  $pixels[0][$i] = hexdec(substr($hash, ($i * 5) + 0 + 6, 1))%2 === 0;
                  $pixels[1][$i] = hexdec(substr($hash, ($i * 5) + 1 + 6, 1))%2 === 0;
                  $pixels[2][$i] = hexdec(substr($hash, ($i * 5) + 2 + 6, 1))%2 === 0;
                  $pixels[3][$i] = hexdec(substr($hash, ($i * 5) + 1 + 6, 1))%2 === 0;
                  $pixels[4][$i] = hexdec(substr($hash, ($i * 5) + 0 + 6, 1))%2 === 0;
              }
              break;
          case "Stewie\UserBundle\Entity\Group":
              // Make it a symmetrical 5x5 multidimensional array
              for ($i = 0; $i < 5; $i++) {
                  $pixels[$i][0] = hexdec(substr($hash, ($i * 5) + 0 + 6, 1))%2 === 0;
                  $pixels[$i][1] = hexdec(substr($hash, ($i * 5) + 1 + 6, 1))%2 === 0;
                  $pixels[$i][2] = hexdec(substr($hash, ($i * 5) + 2 + 6, 1))%2 === 0;
                  $pixels[$i][3] = hexdec(substr($hash, ($i * 5) + 1 + 6, 1))%2 === 0;
                  $pixels[$i][4] = hexdec(substr($hash, ($i * 5) + 0 + 6, 1))%2 === 0;
              }
              break;
          case "Stewie\UserBundle\Entity\Role":
              // Make it a random 5x5 multidimensional array
              for ($i = 0; $i < 5; $i++) {
                  for ($j = 0; $j < 5; $j++) {
                      $pixels[$i][$j] = hexdec(substr($hash, ($i * 5) + $j + 6, 1))%2 === 0;
                  }
              }
              break;
          default:
             return false;
      }

      // Create image
      $image = imagecreatetruecolor(400, 400);
      // Allocate the primary color. The hex code we assigned earlier needs to be decoded to RGB
      $color = imagecolorallocate($image, hexdec(substr($color,0,2)), hexdec(substr($color,2,2)), hexdec(substr($color,4,2)));
      // And a background color
      $bg = imagecolorallocate($image, 238, 238, 238);

      // Color the pixels
      for ($k = 0; $k < count($pixels); $k++) {
          for ($l = 0; $l < count($pixels[$k]); $l++) {
              // Default pixel color is the background color
              $pixelColor = $bg;

              // If the value in the $pixels array is true, make the pixel color the primary color
              if ($pixels[$k][$l]) {
                  $pixelColor = $color;
              }

              // Color the pixel. In a 400x400px image with a 5x5 grid of "pixels", each "pixel" is 80x80px
              imagefilledrectangle($image, $k * 80, $l * 80, ($k + 1) * 80, ($l + 1) * 80, $pixelColor);
          }
      }
      $filename = $hash.'.png';
      $file = imagepng($image,$path.$filename);
      return $filename;
    }
}
