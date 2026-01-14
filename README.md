# Universal Question Format Importer

## Overview

Universal question format importer is a wrapper to native [Moodle's](https://moodle.org) question engine.
It uses code of Moodle format imports as is. Some classes and libraries are replaced by stub.

It supports next question's bank formats:
1. GIFT
2. AIKEN
3. MissingWord
4. MoodleXML
5. BlackBoard 6

## Installation

Simply add a dependency on dlnsk/uqfi to your project's `composer.json` file if you use Composer to manage 
the dependencies of your project. Use following command to add the package to your project's dependencies:

```bash
composer require dlnsk/uqfi
```

## Usage

```php
// Autoload the dependencies
require 'vendor/autoload.php';

// Initialize library
\Dlnsk\UQFI\Importer::init();

// Use
$format = Dlnsk\UQFI\Importer::getFormat('gift', $file_path);
$questions = $format->readQuestions(); // or
$decorated = $format->readDecoratedQuestions(); // to get questions with much nicer structure
```

### Laravel

If you're going to use this package in Laravel, you don't need to load dependencies and initialise. Just use.

## Methods

**init()**
```php
Importer::init();
```
Some simple steps to fool Moodle native library.

**getFormat()**
```php
$format = Importer::getFormat($format, $file_path);
```
Creates a *formatter class* for given format and file. The `format` parameter can be one of
"gift", "aiken", "missingword", "moodlexml" or "blackboard6". Keep in mind that "blackboard6" format
require *zip* file as source of a questions bank.

You also can directly create a necessary class:

```php
$format = new Gift($file_path);
```

**readQuestions()**
```php
$questions = $format->readQuestions();
```
Returns the set of question objects with native Moodle structure. This structure maybe good to save
questions into Moodle database, but not quite well to use. 

**readDecoratedQuestions()**
```php
$questions = $format->readDecoratedQuestions();
```
Returns a set of question objects with a slightly fixed structure. We move some dependent fields 
into parent elements, transform files' data, and so on. The structure of a decorated question is quite
close to MoodleXML file.

See `BaseFormat` class for some helpful methods.


## Question types

Currently, the package supports next question types:
1. calculated / calculatedsimple
1. essay
1. matching / match / ddmatch
1. multichoice
1. numerical
1. ordering
1. shortanswer


### Third party question types

To add support for any other type:
1. Copy third party question type sources to `qtypes` directory of the package.
2. Suppress any errors.
3. Make pull request.
