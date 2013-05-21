<?php

/**
 *
 *   Templar_Template
 *
 *   @author Chris Gray <chris.w.gray@gmail.com>
 *   @since 2013-05-18
 **/

    class Templar_Template {
        protected $processor;
        protected $tmplFunction;
        
        public function __construct(Templar $processor, $tmplFunction){
            if (!is_callable($tmplFunction)) {
                throw new Templar_Exception("Passed a [".gettype($tmplFunction)."] to Template Constructor expecting [Callable]");
            }
            $this->processor = $processor;
            $this->tmplFunction = $tmplFunction->bindTo($this);
            $that = $this;
        }
        
        public function __invoke(array $data){
            $this->display($data);
        }
        
        public function display($data = array()){
            $c = $this->tmplFunction;
            return $c($data);
        }
        
        public function render($data = array()){
            ob_start($data);
            $this->display($data);
            return ob_get_clean();
        }
        
        public function renderTemplate($template, $data = array()){
            return $this->processor->renderTemplate($template, $data);
        }
        
        public function displayTemplate($template, $data = array()){
            $this->processor->displayTemplate($template, $data);
        }
    }
