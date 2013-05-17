TEMPLAR
=======
"The PHP as a PHP Templating Engine"
------------

Template Syntax
----------

    <div>Hello <?php echo $who ?>!</div>

Invokation
----------

    Templar::display("/path/to/template.phtml", array("who"=>"Ladies"));

or
    Templar::render("/path/to/template.phtml", array("who"=>"Gentlemen"));

or

    $func = Templar::getInstance()->getTemplateFunction("/path/to/template.phtml");
    echo $func(array("who"=>"Girls &amp; Bodys");

Thats right functions. Internally Templar works by creating a function out of your template and caching a reference to it. This will bypass the file system hit for template partials that are reused, say like if you
had a template for one of your rows for a table and we wanted to echo out 1000 database records using that template. The method of just including a file would result in 1000 file system hits. Templar can name that tune in 1. 

Based on a simple template templar showed a 750% speed increase and a 49% drop in peak memory usage.  
