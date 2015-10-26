<?php
use Kris\LaravelFormBuilder\Form;

if(!function_exists('getUnencryptedCookie'))
{
    /**
     * Gets unencrypted cookie written by JavaScript.
     *
     * @param string $name
     * @return string
     */
    function getUnencryptedCookie($name)
    {
        return !empty($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }
}

if(!function_exists('activateLinks'))
{
    /**
     * Activates hypertext anchor links in given text.
     *
     * @param string $str
     * @return string
     */
    function activateLinks($str)
    {
        $str = trim($str);
        $str = htmlspecialchars($str);
        $str = preg_replace(
            '#(.*)\@(.*)\.(.*)#',
            '<a href="mailto:\\1@\\2.\\3">\\1@\\2.\\3</a>',
            $str);
        $str = preg_replace(
            '=([^\s]*)(www.)([^\s]*)=',
            '<a href="http://\\2\\3" target=\'_new\'>\\2\\3</a>',
            $str);
        return $str;
    }
}

if(!function_exists('arrayMergeWithStrings'))
{
    /**
     * Merges array2 into array1. If there are some string elements with same keys
     * it implodes them using glue argument.
     *
     * @param array $array1
     * @param array $array2
     * @param string $glue
     * @return array
     */
    function arrayMergeWithStrings($array1, $array2, $glue = '')
    {
        foreach($array2 as $key2 => $item2)
        {
            if(is_string($item2) && array_key_exists($key2, $array1))
            {
               $array1[$key2] .= $glue.$item2;
               unset($array2[$key2]);
            }
        }
        return array_merge($array1, $array2);
    }
}

if(!function_exists('imageOpen'))
{
    /**
     * Opens image and returns image resource.
     *
     * @param int $type
     * @param string $file
     * @return resource
     */
    function imageOpen($type, $file)
    {
        if($type === IMAGETYPE_JPEG)
        {
           $image = imagecreatefromjpeg($file);
        }
        elseif($type === IMAGETYPE_GIF)
        {
           $image = imagecreatefromgif($file);
        }
        elseif($type === IMAGETYPE_PNG)
        {
           $image = imagecreatefrompng($file);
        }
        return $image;
    }
}

if(!function_exists('imageSave'))
{
    /**
     * Saves image with given resource.
     *
     * @param int $type
     * @param string $file
     * @param resource $image
     * @param int $compression
     */
    function imageSave($type, $file, $image, $compression = 80)
    {
        $type = imageConvertType($type);
        if($type === IMAGETYPE_JPEG)
        {
           imagejpeg($image, $file, $compression);
        }
        elseif($type === IMAGETYPE_GIF)
        {
           imagegif($image, $file);
        }
        elseif($type === IMAGETYPE_PNG)
        {
           imagepng($image, $file);
        }
    }
}

if(!function_exists('imageConvertType'))
{
    /**
     * Converts image file extension to PHP image type constant.
     *
     * @param string $type
     * @return int
     */
    function imageConvertType($type)
    {
        $type = is_string($type) ? strtolower($type) : $type;
        if($type === IMAGETYPE_JPEG || $type === 'jpg' || $type === 'jpeg')
        {
           return IMAGETYPE_JPEG;
        }
        elseif($type === IMAGETYPE_GIF || $type === 'gif')
        {
           return IMAGETYPE_GIF;
        }
        elseif($type === IMAGETYPE_PNG || $type === 'png')
        {
           return IMAGETYPE_PNG;
        }
    }
}

if(!function_exists('renderImage'))
{
    /**
     * Renders HTML code for given image.
     *
     * @param string $path Image path.
     * @param array $attrs Img tag attributes.
     * @param int $width Image width.
     * @param int $height Image height.
     * @param int $compression JPEG image compression.
     * @return string HTML code.
     */
    function renderImage(
        $path,
        array $attrs = [],
        $width = null,
        $height = null,
        $compression = null)
    {
        $attrs['alt'] = isset($attrs['alt']) ? $attrs['alt'] : '';
        if($width || $height)
        {
            $arguments = http_build_query(array_where(
                [
                    'width' => $width,
                    'height' => $height,
                    'compression' => $compression
                ],
                function ($k, $v)
                {
                    return !empty($v);
                }));
            $path = route('admin.image.index').'?src='.$path.'&'.$arguments;
        }
        return '<img src="'.url($path).'" style="'.
            ($width ? 'max-width:'.$width.'px;' : '').' '.
            ($height ? 'max-height:'.$height.'px;' : '').'" '.
            join(' ', array_map(
                function($key) use ($attrs)
                {
                   if(is_bool($attrs[$key]))
                   {
                      return $attrs[$key] ? $key : '';
                   }
                   return $key.'="'.$attrs[$key].'"';
                },
                array_keys($attrs))).
            '/>';
    }
}

if(!function_exists('getSiteName'))
{
    /**
     * Gets site domain without protocol and www domain.
     *
     * @return string
     */
    function getSiteName()
    {
        return preg_replace('/^www\./u', '', mb_strtolower(Request::server('SERVER_NAME')));
    }
}

if(!function_exists('adminRoute'))
{
    /**
     * Returns URL of Lavanda's controllers actions corresponding to given model name.
     *
     * @param string $model
     * @param string $action
     * @param array $parms
     * @return string
     */
    function adminRoute($model, $action = '', array $parms = [])
    {
        $modelClass = getModelClass($model);
        if($modelClass::hasController())
        {
            $getRoute = function ($action, $parms = []) use ($model)
            {
                return route('admin.'.$model.'.'.$action, $parms);
            };
        }
        else
        {
            $getRoute = function ($action, $parms = []) use ($model)
            {
                $parms = is_array($parms) ? $parms : [$parms];
                $parms['model'] = $model;
                return route('admin.entity.'.$action, $parms);
            };
        }
        return $action ? $getRoute($action, $parms) : $getRoute;
    }
}

if(!function_exists('getModelClass'))
{
    /**
     * Gets full namespaced model class name.
     *
     * @param string $model
     * @return string
     */
    function getModelClass($model)
    {
        return '\\App\\'.ucfirst($model);
    }
}

if(!function_exists('fixFormBuilderForm'))
{
    /**
     * It's the Laravel-From-Builder hack to fix error borders on all fields with the same name.
     *
     * @param Form $form
     * @param string $errorClass
     */
    function fixFormBuilderForm(Form $form, $errorClass)
    {
        foreach($form->getFields() as $field)
        {
            $class = str_replace($errorClass, '', $field->getOption('wrapper.class'));
            $field->setOption('wrapper.class', $class);
        }
    }
}
