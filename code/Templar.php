<?php

/**
 *
 *  Templar - Efficient PHP as a templating engine for php .
 *
 *  @author Chris Gray <chris.w.gray@gmail.com>
 *  @since 2013-05-16
 *  @package Templar
 **/

class Templar {

    protected  $templateCache;
    
    protected $templatePaths;
    
    protected static $emulateShortEchoTags = false;
    
    protected $templatePreprocessor;
    
    public function __construct(){
        $this->templateCache = array();
        $this->templatePaths = array();
        
        // make sure the stream wrapper is registered
        if (!in_array("templar.template", stream_get_wrappers())){
            stream_register_wrapper("templar.template", "Templar_StreamWrapper");
        }
        
    }
    

    /**
     * Set if we should expand <?= to <?php echo
     *
     * @param boolean $val
     * @return Templar fluent interface
     **/
    public static function setEmulateShortEchoTags(bool $val){
        self::$emulateShortEchoTags = $val;
        return $this;
    }

    /**
     * Determine value of short echo tag emulation
     *
     * @return boolean
     **/ 
    public static function getEmulateShortEchoTags(){
        return self::$emulateShortEchoTags;
    }
    
    /**
     * Add a directory to the path cache
     *
     * @param string $path The path to the template
     * @return Templar fluent interface
     **/
    public function addTemplatePath($path){
        // Make sure the directory ends in exactly one DIRECTORY_SEPARATOR
        
        $path = rtrim($path,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        
        // if the path doesn't exist the fail silently
        if (file_exists($path)){
            $this->templatePaths[] = $path;
        }
        return $this;
    }
    
    /**
     * Add Template Paths to the template
     *
     * @param array $paths
     * @return Templar fluent interface
     **/   
    public function addTemplatePaths($paths){
        foreach($paths as $path){
            $this->addTemplatePath($path);
        }
    }
    
    
    /**
     * Sets the preprocessor
     *
     * @param callable $preprocessor;
     * @return Templar fluent interface
     **/
    public function setTemplatePreprocessor($preprocessor){
        if(is_callable($preprocessor)){
            $this->templatePreprocessor = $preprocessor;
        } else {
            throw new Templar_Exception("Argument supplied to setTemplateProcessor is not callable [" . gettype($processor). print_r($processor, true) . "]");
        }
    }
    
    /**
     * Creates a template function
     *
     * @param string $path The path to the template
     * @return callable the compiled function
     * @throws Templar_Exception if the template can't be found or parsed
     **/
    protected function createTemplate($path){
        // Jail the template resolution to directories under the template directories - per Sébastien Renauld @ Stackoverflow
        // Make sure template exist in one of the template directories -- no ../ paths
        if (strpos("..", $path) !== false || strpos("//", $path) !== false) {
            throw new Templar_Exception("Templates must exist in one of the template directories");
        }
        $path = ltrim($path, DIRECTORY_SEPARATOR);
        if (!file_exists($path)) {
            foreach($this->templatePaths as $testPath){
                if (file_exists($testPath . $path)){
                    $targetPath = realpath($testPath . $path);
                    break; // break out of if and foreach;
                }
            }
            // if I make it here and I don't have a valid template there a problem
            if (!$targetPath){
                throw new Templar_Exception("Could not load template [" . $path . "]");    
            }
            
        }
        $template = new Templar_Template($this, include('templar.template://'.$targetPath));
        $this->templateCache[$path] = $template;
    }
    
    /**
     * Returns an instance of a template function
     *
     * @param string $path The Path to the Template
     * @return Callable The template function
     **/
    public function getTemplateFunction($path){
        if (!array_key_exists($path, $this->templateCache)){
            $this->createTemplate($path);
        }
        return $this->templateCache[$path];
    }
    
    /**
     *  Returns a rendered Template
     *
     *  @param string $template Path to the template
     *  @param array $data
     *  @return string The renderer Template
     **/
    public function render($template, $data = array()){
        ob_start();
        $this->display($template, $data);
        return ob_get_clean();
    }
    
    /**
     *  Outputs a rendered Template
     *
     *  @param string $template Path to the template
     *  @param array $data
     *  @return string The renderer Template
     **/
    public function display($template, $data = array()){
        $tmplFunction = $this->getTemplateFunction($template);
        $tmplFunction($data);
    }
}