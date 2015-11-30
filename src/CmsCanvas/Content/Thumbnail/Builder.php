<?php

namespace CmsCanvas\Content\Thumbnail;

use Config;
use Intervention\Image\ImageManagerStatic as Image;

class Builder {

    /**
     * @var string
     */
    protected $source;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var bool
     */
    protected $crop;

    /**
     * @var string
     */
    protected $sourcePath;

    /**
     * @var string
     */
    protected $destinationPath;

    /**
     * @var string
     */
    protected $noImagePath;

    /**
     * @var int
     */
    protected $sourceModificationTime;

    /**
     * @var int
     */
    protected $destinationModificationTime;

    /**
     * @var string
     */
    protected $computedSourcePath;

    /**
     * Constructor
     *
     * @param string $source
     * @param array $config
     * @return void
     */
    public function __construct($source, array $config = [])
    {
        $this->setSource($source);
        $this->buildFromArray($config);
    }

    /**
     * Returns URL to generated image
     *
     * @return string
     */
    public function get()
    {
        if ($this->getWidth() == null && $this->getHeight() == null) {
            return asset($this->getComputedSourcePath());
        } else {
            $this->compile();
            return asset($this->getDestinationPath());
        }
    }

    /**
     * Construct the object from an array
     *
     * @param array $config
     * @return void
     */
    protected function buildFromArray(array $config)
    {
        foreach ($config as $key => $value) {
            switch ($key) {
                case 'width':
                    $this->setWidth($value);
                    break;

                case 'height':
                    $this->setHeight($value);
                    break;

                case 'crop':
                    $this->setCrop($value);
                    break;

                case 'no_image':
                    $this->setNoImage($value);
                    break;
            }
        }
    }

    /**
     * Sets the max width for the image
     *
     * @param  int $width
     * @return void
     */
    protected function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Sets the max height for the image
     *
     * @param  int  $height
     * @return void
     */
    protected function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Sets whether or not to crop the image
     *
     * @param  bool  $crop
     * @return void
     */
    protected function setCrop($crop)
    {
        $this->crop = $crop;
    }

    /**
     * Returns whether or not to crop the image
     *
     * @return bool
     */
    protected function getCrop()
    {
        return $this->crop;
    }

    /**
     * Returns the max height for the image
     *
     * @return bool
     */
    protected function getHeight()
    {
        return $this->height;
    }

    /**
     * Returns the max width for the image
     *
     * @return bool
     */
    protected function getWidth()
    {
        return $this->width;
    }

    /**
     * Generates image using the provided build config
     *
     * @return void
     */
    protected function compile()
    {
        if ($this->isFresh()) {
            return;
        }

        $image = Image::make(public_path($this->getComputedSourcePath()));

        if ($this->getCrop() == true) {
            $image->fit($this->getWidth(), $this->getHeight(), function ($constraint) {
                $constraint->upsize();
            }, 'center');
        } else {
            $image->resize($this->getWidth(), $this->getHeight(), function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $image->save(public_path($this->getDestinationPath()));
    }

    /**
     * Returns true if the destination image is up to date
     *
     * @return bool
     */
    public function isFresh()
    {
        return ($this->getSourceModificationTime() !== false
            && $this->getDestinationModificationTime() !== false
            && $this->getDestinationModificationTime() >= $this->getSourceModificationTime()
        );
    }

    /**
     * The new generated filename
     *
     * @return string
     */
    protected function getNewFileName()
    {
        $info = pathinfo($this->getComputedSourcePath());

        $filename = (isset($info['filename'])) ? $info['filename'] : '';
        $extension = (isset($info['extension'])) ? $info['extension'] : '';
        $dirname = (isset($info['dirname'])) ? $info['dirname'] : '';

        $thumbnailFilename = md5($dirname.'/'.$filename.'.'.$extension)
            .'-'.$filename.'-'.$this->getWidth().'x'.$this->getHeight();

        if ($this->crop == true) {
            $thumbnailFilename .= '-cropped';
        }

        $thumbnailFilename .= '.'.$extension;

        return $thumbnailFilename;
    }

    /**
     * Sets the source path as a class property
     *
     * @param  string  $source
     * @return void
     */
    protected function setSource($source)
    {
        $this->sourcePath = str_replace(asset(null), '', $source);
    }

    /**
     * Sets the no image path as a class property
     *
     * @param  string  $noImage
     * @return void
     */
    protected function setNoImage($noImage)
    {
        $this->noImagePath = str_replace(asset(null), '', $noImage);
    }

    /**
     * Returns the source image path
     *
     * @return string
     */
    protected function getSourcePath()
    {
        return $this->sourcePath;
    }

    /**
     * Returns the no image path
     *
     * @return string
     */
    protected function getNoImagePath()
    {
        return $this->noImagePath;
    }

    /**
     * Returns the source path to the computed source image
     *
     * @return string
     */
    protected function getComputedSourcePath()
    {
        if ($this->computedSourcePath !== null) {
            return $this->computedSourcePath;
        }

        $this->computedSourcePath = $this->getSourcePath();
        $this->sourceModificationTime = @filemtime(public_path($this->getSourcePath()));

        if (empty($this->computedSourcePath) || $this->sourceModificationTime === false) {
            if (! empty($this->getNoImagePath())) {
                $this->computedSourcePath = $this->getNoImagePath();
                $this->sourceModificationTime = @filemtime(public_path($this->getNoImagePath()));
            }
        }

        if (empty($this->computedSourcePath) || empty($this->sourceModificationTime)) {
            throw new Exception('Unable to find source image.');
        }

        return $this->computedSourcePath;
    }

    /**
     * Return the source image modification time
     *
     * @return int
     */
    protected function getSourceModificationTime()
    {
        if ($this->sourceModificationTime !== null) {
            return $this->sourceModificationTime;
        }

        $this->sourceModificationTime = @filemtime(public_path($this->getComputedSourcePath()));

        return $this->sourceModificationTime;
    }

    /**
     * Returns the destination path
     *
     * @return string
     */
    public function getDestinationPath()
    {
        if ($this->destinationPath !== null) {
            return $this->destinationPath;
        }

        $this->destinationPath = trim(Config::get('cmscanvas::config.thumbnails'), '/').'/'.$this->getNewFileName();

        return $this->destinationPath;
    }

    /**
     * Return the destination image modification time
     *
     * @return int
     */
    protected function getDestinationModificationTime()
    {
        if ($this->destinationModificationTime !== null) {
            return $this->destinationModificationTime;
        }

        $this->destinationModificationTime = @filemtime(public_path($this->getDestinationPath()));

        return $this->destinationModificationTime;
    }

}
