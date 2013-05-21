<?php
/**
 *
 *  StreamWrapper for Templar Templates
 *
 *  @credit Shamelessly stolen from Zend_View_Stream
 **/ 
    class Templar_StreamWrapper {
    
        /**
         *  The head matter for the callback function
         **/
        protected $functionHead = '<?php return function($__data){extract($__data);unset($__data);?>';
    
        /**
         * The foot matter for the callback function
         **/
        protected $functionFoot = '<?php };';
    
        /**
         * Current stream position.
         *
         * @var int
         */
        protected $_pos = 0;
    
        /**
         * Data for streaming.
         *
         * @var string
         */
        protected $_data;
    
        /**
         * Stream stats.
         *
         * @var array
         */
        protected $_stat;
    
        /**
         * Opens the script file and converts markup.
         */
        public function stream_open($path, $mode, $options, &$opened_path)
        {
            // get the view script source
            $path        = str_replace('templar.template://', '', $path);
            if (strpos($path, "//") != false){
                throw new Templar_Exception("Local Urls Please [".$path."]");
            }
            $template = trim(file_get_contents($path));
            
            // expand the <?= to <?php echo if the server doesn't support it.
            if (phpversion() < '5.4.0' && !ini_get('short_open_tag')){
                $template = str_replace("<?=", "<?php echo");
            }
            
            // allow production code to opt out of static analysis
            if (!(defined("TEMPLAR_DISABLE_STATIC_TEMPLATE_ANALYSIS") && TEMPLAR_DISABLE_STATIC_TEMPLATE_ANALYSIS == true)){
                $toks = token_get_all($template);
                $openTags = count(array_filter($toks, function($tok){ return $tok[0] != T_OPEN_TAG && $tok[0] != T_OPEN_TAG_WITH_ECHO; }));
                $closeTags = count(array_filter($toks, function($tok){ return $tok[0] != T_CLOSE_TAG; }));
                if ($openTags != $closeTags){
                    throw new Templar_Exception("Opening/Closing Tag Mismatch in template [".$path."] (".$openTags." vs ".$closeTags.")");             
                }
            }
            $this->_data = $this->functionHead.file_get_contents($path).$this->functionFoot;
     
            /**
             * If reading the file failed, update our local stat store
             * to reflect the real stat of the file, then return on failure
             */
            if ($this->_data === false) {
                $this->_stat = stat($path);
                return false;
            }
    
            $this->_stat = stat($path);
            return true;
        }
    
        /**
         * Included so that __FILE__ returns the appropriate info
         *
         * @return array
         */
        public function url_stat()
        {
            return $this->_stat;
        }
    
        /**
         * Reads from the stream.
         */
        public function stream_read($count)
        {
            $ret = substr($this->_data, $this->_pos, $count);
            $this->_pos += strlen($ret);
            return $ret;
        }
    
    
        /**
         * Tells the current position in the stream.
         */
        public function stream_tell()
        {
            return $this->_pos;
        }
    
    
        /**
         * Tells if we are at the end of the stream.
         */
        public function stream_eof()
        {
            return $this->_pos >= strlen($this->_data);
        }
    
    
        /**
         * Stream statistics.
         */
        public function stream_stat()
        {
            return $this->_stat;
        }
    
    
        /**
         * Seek to a specific point in the stream.
         */
        public function stream_seek($offset, $whence)
        {
            switch ($whence) {
                case SEEK_SET:
                    if ($offset < strlen($this->_data) && $offset >= 0) {
                    $this->_pos = $offset;
                        return true;
                    } else {
                        return false;
                    }
                    break;
    
                case SEEK_CUR:
                    if ($offset >= 0) {
                        $this->_pos += $offset;
                        return true;
                    } else {
                        return false;
                    }
                    break;
    
                case SEEK_END:
                    if (strlen($this->_data) + $offset >= 0) {
                        $this->_pos = strlen($this->_data) + $offset;
                        return true;
                    } else {
                        return false;
                    }
                    break;
    
                default:
                    return false;
            }
        }
    }
