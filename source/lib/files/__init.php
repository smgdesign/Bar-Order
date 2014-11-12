<?php


/**
 * Description of files
 *
 * @author richard
 */
class files {
    public function moveFile($src='', $dest='') {
        if (!empty($src) && !empty($dest)) {
            if (!@move_uploaded_file($src, $dest)) {
                return false;
            }
            return true;
        }
        return false;
    }
    
    public function createImageResource($name='', $tmpName='') {
        $ext = pathinfo($name);
        // get the dimensions \\
        list($this->width, $this->height) = getimagesize($tmpName);
        switch(strtolower($ext['extension'])) {
            case 'jpg': 
            case 'jpeg':
                if (imagetypes() & IMG_JPG) {
                    $this->img = imagecreatefromjpeg($tmpName);
                    return true;
                }
                break;

            case 'gif':
                if (imagetypes() & IMG_GIF) {
                    $this->img = imagecreatefromgif($tmpName);
                    return true;
                }
                break;

            case 'png':
                if (imagetypes() & IMG_PNG) {
                    $this->img = imagecreatefrompng($tmpName);
                    return true;
                }
                break;

            default:
                // *** No extension - No save.
                break;
        }
        return false;
    }
    
    public function resizeImage($newWidth, $newHeight, $option="auto") {  
  
        // *** Get optimal width and height - based on $option  
        $optionArray = $this->getDimensions($newWidth, $newHeight, strtolower($option));  
        $optimalWidth  = round($optionArray['optimalWidth']);
        $optimalHeight = round($optionArray['optimalHeight']);  
        // *** Resample - create image canvas of x, y size  
        if ($option == 'square' || $option == 'rectangle') {
            $this->imageResized = imagecreatetruecolor($newWidth, $newHeight);
            $white = imagecolorallocate($this->imageResized, 255, 255, 255);
            imagefill($this->imageResized, 0, 0, $white);
            if ($optimalWidth < $newWidth) {
                imagecopyresampled($this->imageResized, $this->img, round(($newWidth-$optimalWidth)/2), 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);  
            } else if ($optimalHeight < $newHeight) {
                imagecopyresampled($this->imageResized, $this->img, 0, round(($newHeight-$optimalHeight)/2), 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);  
            } else {
                imagecopyresampled($this->imageResized, $this->img, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);  
            }
        } else {
            $this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
            imagecopyresampled($this->imageResized, $this->img, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);  
        }
        

        // *** if option is 'crop', then crop too  
        if ($option == 'crop') {  
            $this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);  
        }  
    }
    
    private function getDimensions($newWidth, $newHeight, $option) {  

       switch ($option) {  
            case 'exact':  
                $optimalWidth = $newWidth;  
                $optimalHeight= $newHeight;  
                break;  
            case 'portrait':  
                $optimalWidth = $this->getSizeByFixedHeight($newHeight);  
                $optimalHeight= $newHeight;  
                break;  
            case 'landscape':  
                $optimalWidth = $newWidth;  
                $optimalHeight= $this->getSizeByFixedWidth($newWidth);  
                break;  
            case 'auto':  
            case 'square':
            case 'rectangle':
                $optionArray = $this->getSizeByAuto($newWidth, $newHeight);  
                $optimalWidth = $optionArray['optimalWidth'];  
                $optimalHeight = $optionArray['optimalHeight'];  
                break;  
            case 'crop':  
                $optionArray = $this->getOptimalCrop($newWidth, $newHeight);  
                $optimalWidth = $optionArray['optimalWidth'];  
                $optimalHeight = $optionArray['optimalHeight'];  
                break;  
        }  
        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);  
    }
    private function getSizeByFixedHeight($newHeight) {  
        $ratio = $this->width / $this->height;  
        $newWidth = $newHeight * $ratio;  
        return $newWidth;  
    }  

    private function getSizeByFixedWidth($newWidth) {  
        $ratio = $this->height / $this->width;  
        $newHeight = $newWidth * $ratio;  
        return $newHeight;  
    }  

    private function getSizeByAuto($newWidth, $newHeight) {
        if ($this->height < $this->width) {
            $optimalWidth = $newWidth;
            $optimalHeight = $this->getSizeByFixedWidth($newWidth);
            if ($optimalHeight > $newHeight) {
                $optimalHeight = $newHeight;
                $optimalWidth = $this->getSizeByFixedHeight($newHeight);
            }
        } elseif ($this->height > $this->width) {
            $optimalWidth = $this->getSizeByFixedHeight($newHeight);
            $optimalHeight = $newHeight;
            if ($optimalWidth > $newWidth) {
                $optimalWidth = $newWidth;
                $optimalHeight = $this->getSizeByFixedWidth($newWidth);
            }
        } else {
            if ($newHeight < $newWidth) {
                $optimalWidth = $newWidth;
                $optimalHeight = $this->getSizeByFixedWidth($newWidth);
                if ($optimalHeight > $newHeight) {
                    $optimalHeight = $newHeight;
                    $optimalWidth = $this->getSizeByFixedHeight($newHeight);
                }
            } else if ($newHeight > $newWidth) {
                $optimalWidth = $this->getSizeByFixedHeight($newHeight);
                $optimalHeight = $newHeight;
                if ($optimalWidth > $newWidth) {
                    $optimalWidth = $newWidth;
                    $optimalHeight = $this->getSizeByFixedWidth($newWidth);
                }
            } else {
                // *** Square being resized to a square
                $optimalWidth = $newWidth;
                $optimalHeight = $newHeight;
            }
        }

        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);  
    }  

    private function getOptimalCrop($newWidth, $newHeight) {  

        $heightRatio = $this->height / $newHeight;  
        $widthRatio  = $this->width /  $newWidth;  

        if ($heightRatio < $widthRatio) {  
            $optimalRatio = $heightRatio;  
        } else {  
            $optimalRatio = $widthRatio;  
        }  

        $optimalHeight = $this->height / $optimalRatio;  
        $optimalWidth  = $this->width  / $optimalRatio;  

        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);  
    }
    
    private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight) {  
        // *** Find center - this will be used for the crop  
        $cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );  
        $cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );  

        $crop = $this->imageResized;  
        //imagedestroy($this->imageResized);  

        // *** Now crop from center to exact requested size  
        $this->imageResized = imagecreatetruecolor($newWidth , $newHeight);  
        imagecopyresampled($this->imageResized, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);  
    }
    public function saveImage($savePath, $imageQuality="100") {  
        // *** Get extension  
        $ext = pathinfo($savePath);  
        switch(strtolower($ext['extension'])) {  
            case 'jpg':  
            case 'jpeg':  
                if (imagetypes() & IMG_JPG) {  
                    imagejpeg($this->imageResized, $savePath, $imageQuality);  
                }  
                break;  

            case 'gif':  
                if (imagetypes() & IMG_GIF) {  
                    imagegif($this->imageResized, $savePath);  
                }  
                break;  

            case 'png':  
                // *** Scale quality from 0-100 to 0-9  
                $scaleQuality = round(($imageQuality/100) * 9);  

                // *** Invert quality setting as 0 is best, not 9  
                $invertScaleQuality = 9 - $scaleQuality;  

                if (imagetypes() & IMG_PNG) {  
                    imagepng($this->imageResized, $savePath, $invertScaleQuality);  
                }
                break;  

            default:  
                // *** No extension - No save.  
                break;  
        }  

        imagedestroy($this->imageResized);  
    }
}

?>
