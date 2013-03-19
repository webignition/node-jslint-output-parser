node-jslint-output-parser [![Build Status](https://secure.travis-ci.org/webignition/node-jslint-output-parser.png?branch=master)](http://travis-ci.org/webignition/node-jslint-output-parser)
===========================

Overview
---------

PHP parser for the output of [reid/node-jslint][1], get lovely things such as:

* a list of errors
* error counts
* % of content scanned
* bunnies

If you're building a PHP-based application that programmatically interfaces with
node-jslint, this is for you.

Usage
-----

### The "Hello World" example

Running JSLint from the command line through node-jslint can give you a nice
collection of error information to process:

```bash
node jslint.js --json /home/jon/www/gears.simplytestable.com/src/SimplyTestable/WebClientBundle/Resources/public/js/app.js

[
   "/home/jon/www/gears.simplytestable.com/src/SimplyTestable/WebClientBundle/Resources/public/js/app.js",
   [
      {
         "id":"(error)",
         "raw":"Missing 'use strict' statement.",
         "evidence":"    var nextTaskIdCollection = [];",
         "line":6,
         "character":5,
         "a":"var",
         "reason":"Missing 'use strict' statement."
      },
      {
         "id":"(error)",
         "raw":"Unexpected '{a}'.",
         "evidence":"    ",
         "line":7,
         "character":1,
         "a":"(space)",
         "reason":"Unexpected '(space)'."
      },
```

Let's parse that in a unit test and see what we can get:

```php
<?php
$parser = new webignition\NodeJslintOutput\Parser();

$parser->parse($rawOutputString);
$nodeJsLintOutput = $parser->getNodeJsLintOutput();
        
$this->assertNotNull($nodeJsLintOutput);
$this->assertEquals(7, $nodeJsLintOutput->getEntryCount());
$this->assertEquals(4, $nodeJsLintOutput->getPercentScanned());

$outputEntries = $nodeJsLintOutput->getEntries();
foreach ($outputEntries as $outputEntry) {
    /* @var $outputEntry \webignition\NodeJslintOutput\Entry\Entry */    
    echo $outputEntry->getReason() . "\n";
    echo $outputEntry->getEvidence() . "\n";    
}
```

See? Useful. We can see how many entries are in the output, what the entries are and how much of the linted content 
was scanned.

### Seeing Why JSLint Stopped Scanning

![I LINT YOU. Y U NO SCAN ALL CODE](http://cdn.memegenerator.net/instances/600x/31108953.jpg)

Sometimes JSLint encounters too many errors, sometimes it stops scanning if the code quality is just not up to scratch.

It's nice to see why scanning stopped.

```php
<?php
$parser = new webignition\NodeJslintOutput\Parser();

$parser->parse($rawOutputString);
$nodeJsLintOutput = $parser->getNodeJsLintOutput();
        
$this->assertTrue($nodeJsLintOutput->wasStopped());
$this->assertFalse($nodeJsLintOutput->hasTooManyErrors());
```

Building
--------

#### Using as a library in a project

If used as a dependency by another project, update that project's composer.json
and update your dependencies.

    "require": {
        "webignition/node-jslint-output-parser": "*"      
    }

#### Developing

This project has external dependencies managed with [composer][3]. Get and install this first.

    # Make a suitable project directory
    mkdir ~/node-jslint-output-parser && cd ~/node-jslint-output-parser

    # Clone repository
    git clone git@github.com:webignition/node-jslint-output-parser.git.

    # Retrieve/update dependencies
    composer.phar install

Testing
-------

Have look at the [project on travis][4] for the latest build status, or give the tests
a go yourself.

    cd ~/node-jslint-output-parser
    phpunit

[1]: https://github.com/reid/node-jslint
[3]: http://getcomposer.org
[4]: http://travis-ci.org/webignition/website-rss-feed-finder/builds
