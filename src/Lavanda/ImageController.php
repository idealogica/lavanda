<?php
namespace Idealogica\Lavanda;

use Illuminate\Http\Request;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelController;

/**
 * Image presentation display controller.
 */
class ImageController extends LaravelController
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * Displays image with given file name, size and compression.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Repository $config)
    {
        $file = $request->get('src');
        $width = (int)$request->get('width');
        $height = (int)$request->get('height');
        $compression = $request->get('compression') ?
            (int)$request->get('compression') :
            80;
        if(!$file)
        {
            abort(404);
        }
        if(file_exists($file))
        {
            if(!$width && !$height)
            {
                $cacheFile = $file;
            }
            else
            {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $cacheFile = $config->get('lavanda.image_cache_path').DIRECTORY_SEPARATOR.
                    crc32($file).$width.$height.'.'.$ext;
                if(!file_exists($cacheFile) || filemtime($file) > filemtime($cacheFile))
                {
                    list($imgWidth, $imgHeight, $type) = getimagesize($file);
                    $image = imageOpen($type, $file);
                    if($width && !$height)
                    {
                        if($width != $imgWidth)
                        {
                            $ratio = $width / $imgWidth;
                            $height = $imgHeight * $ratio;
                        }
                        else
                        {
                            $height = $imgHeight;
                        }
                    }
                    if($height && !$width)
                    {
                        if($height != $imgHeight)
                        {
                            $ratio = $height / $imgHeight;
                            $width = $imgWidth * $ratio;
                        }
                        else
                        {
                            $width = $imgWidth;
                        }
                    }
                    $cacheImage = imagecreatetruecolor($width, $height);
                    imagecopyresampled(
                        $cacheImage,
                        $image,
                        0, 0, 0, 0,
                        $width,
                        $height,
                        $imgWidth,
                        $imgHeight);
                    imageSave($type, $cacheFile, $cacheImage, $compression);
                }
            }
            return response(file_get_contents($cacheFile))->
                header('Content-Type', mime_content_type($cacheFile));
        }
        else
        {
            abort(404);
        }
    }
}
