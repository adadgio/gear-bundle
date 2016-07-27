<?php

namespace Adadgio\GearBundle\NodeRed\Configuration;

use Doctrine\Common\Inflector\Inflector;

class Settings
{
    private $file;
    private $config;
    private $templatesDir;
    private $templatesPath;
    private $settings;

    /**
     * Class constructor.
     *
     * @param string Flow template file basename
     * @return void
     */
    public function __construct($file = 'settings.js.tpl')
    {
        $this->file = $file;
    }

    /**
     * Receives config, index and path used later in variabilisation
     * necessary to create the final flow from the base json template.
     *
     * @param  integer Flow index, each one is unique and incremental
     * @param  array The bundle configuration (di)
     * @param  string Flows template base directory
     * @return object \Flow
     */
    public function injectConfig(array $config, $templatesDir)
    {
        $this->config = $config;
        $this->templatesDir = $templatesDir;
        $this->templatePath = $templatesDir.'/'.$this->file;

        if (!is_file($this->templatePath)) {
            throw new \Exception(sprintf('Node red settings template path is incorrect (no file found) "%s"', $this->templatePath));
        }

        return $this;
    }

    /**
     * Get final flow name.
     *
     * @return array Flow
     */
    public function getFinalName()
    {
        return 'settings.js';
    }

    /**
     * Get final flow as JSON reprensentation.
     *
     * @return string
     */
    public function getContents()
    {
        return $this->settings;
    }

    /**
     * Get flow json file basename
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->file;
    }

    /**
     * Get flow json file template server path.
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Parse the flow from the flow template file contents
     * and replace variable parameters and indexes.
     *
     * @return object \Flow
     */
    public function parseSettings()
    {
        $this->settings = file_get_contents($this->templatePath);
        $this->settings = $this->replaceSettingsParameters($this->settings);

        return $this;
    }

    /**
     * Replace array flow variable parameters.
     *
     * @param string Settings file contents
     * @return array \Settings
     */
    private function replaceSettingsParameters($settings)
    {
        foreach ($this->config['settings'] as $key => $param) {

            if (true === is_array($param)) {
                $param = json_encode($param); // do NOT pretty print ? , JSON_PRETTY_PRINT
                $param = static::removeKeyQuotes($param);
                // $param = $this->jsonEncodeNoKeyQuotes($param); // @todo

            } else if (!is_array($param) && null === $param) {

            } else {
                // a param is just param !
            }

            $settings = str_replace('%'.$key.'%', $param, $settings);
        }

        $settings = static::quoteUnmatchedParams($settings);
        return $settings;
    }

    private static function removeKeyQuotes($string)
    {
        return preg_replace('~"([a-z_0-9]+)":~', '$1:', $string);
    }
    
    private static function quoteUnmatchedParams($contents)
    {
        $contents = preg_replace('~([a-zA-Z0-9_]+:\s%[a-z0-9_]+%,)~', '// $1 // disabled by Sf2 config', $contents);
        $contents = preg_replace("~([a-zA-Z0-9_]+:\s'',)~", '// $1 // disabled by Sf2 config', $contents);
        return $contents;
    }
}
