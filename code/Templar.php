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

    protected static $instance;
    
    protected $templatePaths;
    
    protected function __construct(){
        $this->templateCache = array();
        $this->templatePaths = array();
        
    }
    
    /**
     * Return singlton instance
     *
     * @return Templar The template Engine.
     **/
    public static function getInstance(){
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Add a directory to the path cache
     *
     * @param string $path The path to the template
     **/
    public function addTemplatePath($path){
        // Make sure the directory ends in exactly one DIRECTORY_SEPARATOR
        
        $path = rtrim($path,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        
        // if the path doesn't exist the fail silently
        if (file_exists($path)){
            $this->templatePaths[] = $path;
        }
    }
    
    /**
     * Creates a template function
     *
     * @param string $path The path to the template
     * @return the compiled function
     * @throws Templar_Exception if the template can't be found or parsed
     **/
    protected function createTemplate($path){
        // Make sure template exist in one of the template directories -- no ../ paths
        if (strpos("..", $path) !== false) {
            throw new Templar_Exception("Templates must exist in one of the template directories");
        }
        $path = ltrim($path, DIRECTORY_SEPARATOR);
        if (!file_exists($path)) {
            foreach($this->templatePaths as $testPath){
                if (file_exists($testPath . $path)){
                    $targetPath = $testPath . $path;
                    break; // break out of if and foreach;
                }
            }
            // if I make it here and I don't have a valid template there a problem
            if (!$targetPath){
                throw new Templar_Exception("Could not load template [" . $path . "]");    
            }
            
        }
        $code = 'if (is_array($data)) {foreach($data as $k=>$v){${$k} = $v;}}  ?>' . file_get_contents($targetPath) . '<?php '; 
        
        $func = create_function('$data = array()', $code);
        
        
        if (!$func){
            throw new Templar_Exception("Could not parse template [" . $targetPath . "]");
        }
        // add to the templateCache
        $this->templateCache[$path] = $func;
    }
    
    /**
     * Returns an instance of a template function
     *
     * @param string $path The Path to the Template
     * @return Callable The template function
     **/
    public function getTemplate($path){
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
    public static function render($template, $data = array()){
        ob_start();
        self::display($template, $data);
        return ob_get_clean();
    }
    
    /**
     *  Outputs a rendered Template
     *
     *  @param string $template Path to the template
     *  @param array $data
     *  @return string The renderer Template
     **/
    public static function display($template, $data = array()){
        $tmplFunction = self::getInstance()->getTemplate($template);
        $tmplFunction($data);
    }
}
 

 