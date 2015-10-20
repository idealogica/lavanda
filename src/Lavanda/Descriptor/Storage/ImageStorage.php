<?php
namespace Idealogica\Lavanda\Descriptor\Storage;

/**
 * Handles image file.
 */
class ImageStorage extends Storage
{
    /**
     * {@inheritDoc}
     */
    protected $parms = [
        'type' => 'jpg',
        'path' => 'public/image',
        'compression' => 80];

    /**
     * {@inheritDoc}
     */
    public function attach(&$item)
    {
        $id = $item['id'];
        $type = $this->getParm('type');
        $path = ($this->publicPath ? $this->publicPath.DIRECTORY_SEPARATOR : '').
            $this->getParm('path');
        $file = $path.DIRECTORY_SEPARATOR.$id.'.'.$type;
        $item[$this->name] = file_exists($file) ?
            preg_replace(
                '{^'.($this->publicPath ? $this->publicPath.DIRECTORY_SEPARATOR : '').'}',
                '',
                $file) :
            null;
    }

    /**
     * {@inheritDoc}
     */
    public function save($item, $key = '')
    {
        $id = $item['id'];
        $outputType = strtolower($this->getParm('type'));
        $path = ($this->publicPath ? $this->publicPath.DIRECTORY_SEPARATOR : '').
            $this->getParm('path');
        $compression = $this->getParm('compression');
        $key = ($key ? $key.'.' : '').$this->name;
        $file = $this->request->file($key);
        if($file)
        {
            if($file->isValid())
            {
                $tmpFile = $path.DIRECTORY_SEPARATOR.$id;
                $outputFile = $tmpFile.'.'.$outputType;
                $file->move($path, $id);
                list($width, $height, $inputType) = getimagesize($tmpFile);
                if($inputType === imageConvertType($outputType))
                {
                    rename($tmpFile, $outputFile);
                }
                else
                {
                    imageSave(
                        $outputType,
                        $outputFile,
                        imageOpen($inputType, $tmpFile),
                        $compression);
                    unlink($tmpFile);
                }
            }
        }
        elseif(!$this->request->input($key))
        {
            $this->delete($item);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete($item)
    {
        $id = $item['id'];
        $type = $this->getParm('type');
        $path = ($this->publicPath ? $this->publicPath.DIRECTORY_SEPARATOR : '').
            $this->getParm('path');
        $file = $path.DIRECTORY_SEPARATOR.$id.'.'.$type;
        if(file_exists($file))
        {
            unlink($file);
        }
    }
}
