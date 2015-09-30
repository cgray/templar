TEMPLAR
=======
"The PHP as a Template" PHP Template Processor
------------

Template Syntax
----------

    <div>Hello <?= $who ?>!</div>

Invocation
----------

    $tmpl = new Templar();
    $tmpl->display("/path/to/template.phtml", array("who"=>"Ladies"));


When you call `Templar::display` an internal function cache is examined to see if there 
is a function for the requested template. If not one is created. So the above template your template
gets turned into:

    function($__data){extract($__data); unset($__data); ?><div>Hello <?= $who ?>!</div><?php}

And cached into the template function registry. All subsequent requests for that template reuse this function.

Example
----------

Take this example for a more real world example:

__product-list.phtml__

    <?php
       $tmpl = new Templar();
       $tmpl->addTemplateDirectory(APPLICATION_PATH."/templates/");
    ?>
    <table class="product-grid">
        <?php 
            foreach($products as $product){
                $tmpl->display("product-row.phtml", $product);
            } 
        ?>
    </table>
    
__product-row.phtml__

    <tr><td><?= $id ?></td><td><?= $sku ?></td><td><?= $product_name ?></td><td><?= $sale_price ?></td></tr>
    

API 
=======

Templar
-------

**string Templar::render(string $template, array $data)** - render data to string

**void Templar::display(string $template, array $data)** - renders a template to stdout.

**Templar_Template Templar::getTemplateFunction(string $template)** - Returns invokable Templar_Template function.

**Templar Templar::addTemplatePath(string $path)** - add a directory to the template path array

**Templar Templar::addTemplatePaths(array $paths)** - adds an array of paths to the template path array


Templar_Template
-----------

**string Templar_Template::render(array $data)** - renders data to a string

**void Templar_Template::display(array $data)** - renders data to stdout


TODO
---------

Handle View Helpers
     
     Helpers will be registered to a Templar instance but will be proxied through the Templar_Template by the call back 
