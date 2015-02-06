<?php namespace CmsCanvas\Theme;

use Illuminate\Filesystem\Filesystem;

class ThemePublisher {

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The destination of the theme files.
     *
     * @var string
     */
    protected $publishPath;

    /**
     * The path to the application's packages.
     *
     * @var string
     */
    protected $packagePath;

    /**
     * The theme to copy
     *
     * @var string
     */
    protected $theme;

    /**
     * Create a new theme publisher instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $publishPath
     * @return void
     */
    public function __construct(Filesystem $files, $publishPath)
    {
        $this->files = $files;
        $this->publishPath = $publishPath;
    }

    /**
     * Publish theme files from a given path.
     *
     * @param  string  $package
     * @param  string  $source
     * @return void
     */
    public function publish($package, $source)
    {
        $destination = $this->publishPath;

        if ( ! is_null($this->theme))
        {
            $source .= '/'.$this->theme;
            $destination .= '/'.$this->theme;
        }

        $this->makeDestination($destination);

        return $this->files->copyDirectory($source, $destination);
    }

    /**
     * Publish the theme files for a package.
     *
     * @param  string  $package
     * @param  string  $packagePath
     * @return void
     */
    public function publishPackage($package, $packagePath = null)
    {
        $source = $this->getSource($package, $packagePath ?: $this->packagePath);

        return $this->publish($package, $source);
    }

    /**
     * Get the source themes directory to publish.
     *
     * @param  string  $package
     * @param  string  $packagePath
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function getSource($package, $packagePath)
    {
        $source = $packagePath."/{$package}/src/themes";

        if ( ! $this->files->isDirectory($source))
        {
            throw new \InvalidArgumentException("Themes not found.");
        }

        return $source;
    }

    /**
     * Create the destination directory if it doesn't exist.
     *
     * @param  string  $destination
     * @return void
     */
    protected function makeDestination($destination)
    {
        if ( ! $this->files->isDirectory($destination))
        {
            $this->files->makeDirectory($destination, 0777, true);
        }
    }

    /**
     * Set the default package path.
     *
     * @param  string  $packagePath
     * @return void
     */
    public function setPackagePath($packagePath)
    {
        $this->packagePath = $packagePath;
    }

   /**
     * Set the theme.
     *
     * @param  string  $theme
     * @return void
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    } 

}
