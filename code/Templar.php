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
    
    protected $viewHelpers;
    
    public function __construct(){
        $this->templateCache = array();
        $this->templatePaths = array(__DIR__."/Templar/helpers/");
        $this->viewHelpers = array();
        
        // make sure the stream wrapper is registered
        if (!in_array("templar.template", stream_get_wrappers())){
            stream_register_wrapper("templar.template", "Templar_StreamWrapper");
        }
        
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
     * Creates a template function
     *
     * @param string $path The path to the template
     * @return callable the compiled function
     * @throws Templar_Exception if the template can't be found or parsed
     **/
    protected function createTemplate($path){
        // Jail the template resolution to directories under the template directories - per Sébastien Renauld @ Stackoverflow
        // Make sure template exist in one of the template directories -- no ../ paths and no // 
        if (strpos($path, "..") !== false || strpos($path, "//") !== false) {
            throw new Templar_Exception("Templates must exist in one of the template directories [".$path."]");
        } 
        $path = ltrim($path, DIRECTORY_SEPARATOR);
        $targetPath = null;
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
    public function renderTemplate($template, $data = array()){
        return $this->getTemplateFunction($template)->render($data);
    }
    
    /**
     *  Outputs a rendered Template
     *
     *  @param string $template Path to the template
     *  @param array $data
     *  @return string The renderer Template
     **/
    public function displayTemplate($template, $data = array()){
        $this->getTemplateFunction($template)->display($data);
    }
    
    /**
     * Bind a template to be used as a view helper
     *
     * No heroic measures will be taken to make sure that the template exists.
     * This will error at at the first call to createTemplate
     *
     * @param string $template the template that implements the code.
     * @param array $params list of arguments for the template
     **/
    public function defineViewHelper($name, $template, $params){
        //echo "defined $name as $template ", print_r($params, true);
        $this->viewHelpers[$name] = array($template, $params);
    }
    
    /**
     * Call a view helper
     *
     * @param string $name the name of a previously registered view helper
     * @param array $data The parameters for the view helper
     * @return void
     **/
    public function callViewHelper($name, $data){
        if (array_key_exists($name, $this->viewHelpers)){
            if (count($data) == count($this->viewHelpers[$name][1])){
                
            } elseif (count($data)< count($this->viewHelpers[$name][1])){
                $data = array_pad($data, count($this->viewHelpers[$name][1]), "");
            } else {
                $data = array_slice($data, 0, count($this->viewHelpers[$name][1]));
            }
            $this->displayTemplate($this->viewHelpers[$name][0], array_combine($this->viewHelpers[$name][1], $data));
        } else {
            throw new Templar_Exception("Call to undefined view helper [".$name."]");
        }
    }
    
 
}
