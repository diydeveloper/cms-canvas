<?php namespace CmsCanvas\Commands;

use Illuminate\Console\Command;
use CmsCanvas\Theme\ThemePublisher;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ThemePublishCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish a package's themes to the application";

    /**
     * The theme publisher instance.
     *
     * @var \CmsCanvas\Theme\ThemePublisher
     */
    protected $theme;

    /**
     * Create a new theme publish command instance.
     *
     * @param  \CmsCanvas\Theme\ThemePublisher  $theme
     * @return void
     */
    public function __construct(ThemePublisher $theme)
    {
        parent::__construct();

        $this->theme = $theme;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $package = $this->input->getArgument('package');
        $theme = $this->input->getOption('theme');
        
        if ( ! is_null($theme))
        {
            $this->theme->setTheme($theme);
        }

        if ( ! is_null($path = $this->getPath()))
        {
            $this->theme->publish($package, $path);
        }
        else
        {
            $this->theme->publishPackage($package);
        }

        $this->output->writeln('<info>Themes published for package:</info> '.$package);
    }

    /**
     * Get the specified path to the files.
     *
     * @return string
     */
    protected function getPath()
    {
        $path = $this->input->getOption('path');

        if ( ! is_null($path))
        {
            return $this->laravel['path.base'].'/'.$path;
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('package', InputArgument::OPTIONAL, 'The name of the package being published.', 'diyphpdeveloper/cmscanvas'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('path', null, InputOption::VALUE_OPTIONAL, 'The path to the source theme files.', null),
            array('theme', null, InputOption::VALUE_OPTIONAL, 'A specific theme to copy.', null),
        );
    }

}
