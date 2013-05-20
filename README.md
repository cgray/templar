TEMPLAR
=======
"The PHP as a Template" PHP Template Processor
------------

Template Syntax
----------

    <div>Hello <?= $who ?>!</div>

Invocation
----------

    Templar::display("/path/to/template.phtml", array("who"=>"Ladies"));

or

    echo Templar::render("/path/to/template.phtml", array("who"=>"Gentlemen"));

or

    $func = Templar::getInstance()->getTemplateFunction("/path/to/template.phtml");
    $func(array("who"=>"Girls &amp; Bodys");

When you call `Templar::display` an internal function cache is examined to see if there 
is a function for the requested template. If not one is created. So the above template your template
gets turned into:

    function lambda_1($__data){extract($__data); unset($__data); ?><div>Hello <?= $who ?>!</div><?php}

And cached into the template function registry. All subsequent requests for that template reuse this function.

Example
----------

Take this example for a more real world example:

__product-list.phtml__

    <table class="product-grid">
        <?php 
            foreach($products as $product){
                Templar::display("product-row.phtml", $product);
            } 
        ?>
    </table>
    
__product-row.phtml__

    <tr><td><?= $id ?></td><td><?= $sku ?></td><td><?= $product_name ?></td><td><?= $sale_price ?></td></tr>
    
__Controller__
    
    $this->view->assign("products", $product) = $productTable->getProducts();
    
__View__

    <div class='box widget'>
    <?php Templar::display("/templates/product-row.phtml", $product); ?>
    </div>

API 
----------

**Templar Templar::getInstance()** - For good of for bad implemented as a singleton, for ease in configuration during bootstrap.

**Templar Templar::setEmulateShortEchoTags(boolean)** - Determines if we should replace all occurances of `<?=` with `<?php echo `

**boolean Templar::getEmulateShortEchoTags()** - Get the short tag emulation setting.

**callable Templar->getTemplateFunction(string path)** - get or create and get a function to render template

**string Template::render(array $data)** - render data to string

**void Template::display(array $data)** - render data to the output

**Templar Templar::addTemplatePath(string $path)** - add a directory to the template path array

**Templar Templar::addTemplatePaths(array $paths)** - adds an array of paths to the template path array

**Templar Templar::setTemplatePreprocessor(callable $preprocessor)** - filter contents of function prior to creating.
