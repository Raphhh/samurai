# Samurai

[![Latest Stable Version](https://poser.pugx.org/raphhh/samurai/v/stable.svg)](https://packagist.org/packages/raphhh/samurai)
[![Docs](https://readthedocs.org/projects/samurai/badge/?version=latest)](http://samurai.readthedocs.org)
[![Build Status](https://travis-ci.org/Raphhh/samurai.png)](https://travis-ci.org/Raphhh/samurai)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Raphhh/samurai/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Raphhh/samurai/)
[![Code Coverage](https://scrutinizer-ci.com/g/Raphhh/samurai/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Raphhh/samurai/)
[![Total Downloads](https://poser.pugx.org/raphhh/samurai/downloads.svg)](https://packagist.org/packages/raphhh/samurai)
[![License](https://poser.pugx.org/raphhh/samurai/license.svg)](https://packagist.org/packages/raphhh/samurai)

Samurai is a PHP scaffolding tool. 
It helps you to :

 - **start a new project in PHP**, generating all the base files in a simple command line.
 - **improve an existing project**, running several modules with specific actions.

Samurai generates all the files you need for a library, a web application, a frameworked project, and so on. 
You can even **load your own bootstrap**.

Samurai will run several modules during the scaffolding of a new project, or independently on an existing project. 
You can **choose which module** to install according to your own needs. 
You can also create your own module.

![Samurai during project scaffolding](https://raw.githubusercontent.com/Raphhh/samurai/master/doc/samurai-new.png)


## What does Samurai scaffold?

Samurai installs and params your project:

 1. Download the bootstrap and its dependencies with Composer
 2. Param the Composer config (composer.json)
 3. Dump the autoloader of Composer with your new package name
 4. Execute the installed modules.
 
 
### Examples of bootstrap

 - A simple PHP library
 - Symfony
 - Laravel
 - Zend
 - CakePHP
 - CodeIgniter
 - Yii
 - Drupal
 - Joomla
 - WordPress
 - Silex
 - Slim
 - .. what you want!
 

### Examples of modules

 - Init git for the project. See [raphhh/samurai-module-git](https://github.com/Raphhh/samurai-module-git).
 - Create a new repo on GitHub and link it to your project (github module) (todo)
 - Clean some files (changelog, ...). See [raphhh/samurai-module-cleaner](https://github.com/Raphhh/samurai-module-cleaner).
 - Init PHPUnit (todo)
 - Init Behat (todo)
 - Link your project to Travis-ci (todo)
 - ... what you want!


## Installation

Install Samurai with Composer:

```console
$ composer global require raphhh/samurai
```

Be sure you have set the COMPOSER_BIN_DIR in your path. 
For more information, see the detailed [installation doc](http://samurai.readthedocs.org/) of Samurai.


## Scaffold your project

To create a new project, run the `new` command of Samurai and choose your bootstrap:

```console
$ samurai new
```

For more information, see the detailed [scaffolding doc](http://samurai.readthedocs.org/en/latest/scaffolding/) of Samurai.


## Modules

A module is a plugin added to Samurai. 
This plugin will execute some specific actions. 
For example, the git module will init Git in your project.

You can easily develop your own module and add it to Samurai.

The modules can run during the scaffolding of a new project, or improve an existing project.

For more information, see the detailed [modules doc](http://samurai.readthedocs.org/en/latest/modules/) of Samurai.

### Install pre-defined modules

```console
$ samurai module install
```

### Run modules on an existing project

```console
$ samurai module run
```

## Documentation

See the [Samurai documentation](http://samurai.readthedocs.org/).
 - [Installation](http://samurai.readthedocs.org/en/latest/installation/)
 - [Usage](http://samurai.readthedocs.org/en/latest/scaffolding/)
 - [Aliases](http://samurai.readthedocs.org/en/latest/aliases/)
 - [Modules](http://samurai.readthedocs.org/en/latest/modules/)

## Contribution and roadmap

See the [Samurai wiki](https://github.com/Raphhh/samurai/wiki).

