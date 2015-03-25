# Samurai

[![Latest Stable Version](https://poser.pugx.org/raphhh/samurai/v/stable.svg)](https://packagist.org/packages/raphhh/samurai)
[![Build Status](https://travis-ci.org/Raphhh/samurai.png)](https://travis-ci.org/Raphhh/samurai)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Raphhh/samurai/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Raphhh/samurai/)
[![Code Coverage](https://scrutinizer-ci.com/g/Raphhh/samurai/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Raphhh/samurai/)
[![Total Downloads](https://poser.pugx.org/raphhh/samurai/downloads.svg)](https://packagist.org/packages/raphhh/samurai)
[![Reference Status](https://www.versioneye.com/php/raphhh:samurai/reference_badge.svg?style=flat)](https://www.versioneye.com/php/raphhh:samurai/references)
[![License](https://poser.pugx.org/raphhh/samurai/license.svg)](https://packagist.org/packages/raphhh/samurai)

Samurai is a PHP scaffolding tool. It helps you to start a new project in PHP, generating all the base files in a simple command line.

Samurai generates a all the files you need for a basic library, a web application, a frameworked project, and so on. You can event add your own bootstrap.


## Installation

First, download Samurai with [Composer](https://getcomposer.org) in the global env.

```
composer global require raphhh/samurai
```

Make sure to place the ~/.composer/vendor/bin directory in your PATH (or C:\%HOMEPATH%\AppData\Roaming\Composer\vendor\bin if working with Windows). So, the samurai executable is found when you run the command in your terminal.

```
samurai help --version
```


## List commands and help

To list all the available command, enter the 'list' command:

```
samurai list
```

To get help on a specific command, use the 'help' command:

```
samurai help <command>
```


## Create your project

The simple samurai "new" command will create a fresh installation of a new project. 

### Choose between projects

If you do not specify a bootstrap to install, Samurai will list all the project's bootstraps.

```
samurai new
```
You just have to choose a bootstrap to install.

### Specify a pre-defined project

If you know the project, you can directly specify it in the command.

```
samurai new lib
```

In fact, you specify an alias of a project. An alias is just a defined bootstrap and version. See alias section for more information.

Samurai will create your project from this bootstrap. For example, with alias 'lib', Samurai will install a basic PHP library bootstrap.

### Specify another bootstrap and its version

You can specify any project loadable with Composer, event if you do not have alias. For example, you can create a new Symfony app by specifying its package. (This is just an example, because the 'symfony' alias already exists)

```
samurai new symfony/framework-standard-edition
```

Or if you want a specific version of a package, add the version you want just after the bootstrap.

```
samurai new symfony/framework-standard-edition 1.0.0
```

If you install a project from a non-aliased bootstrap, do not hesitate to add it in the alias list. It is very simple. See alias section for more information. For example, Symfony is already available with 'symfony' alias.

```
samurai new symfony
```


## What Samurai does during the installation of your project?

Samurai installs and params all your project:

 1. Download the bootstrap and its dependency with Composer
 2. Param the Composer config (composer.json) (todo: author + package)
 3. Dump the autoloader of Composer with your new Package name (todo)
 4. Clean some files (changelog, etc) (todo)
 5. Execute the installed modules. For example:
     5.1. Init git for the project (git module) (todo)
     5.2. Create a new repo on GitHub and link it to your project (github module) (todo)
     5.3. Link your project to Packagist (packagist module) (todo)
     5.4. Link your project to Travis-ci (travis module) (todo)
     5.5. Link your project to Scrutinizer (scrutinizer module) (todo)
     5.6. Add a file phpunit.xml (phpunit module) (todo)


## Alias

Alias are simple words linked to a specific bootstrap at a specific version. For example, the alias 'lib' points to the package 'raphhh/php-lib-bootstrap' at last stable version.

### List the existing alias

To list all the alias, execute the command:
```
samurai alias
```

### Add or redefine an alias

You can easily add any bootstrap you want, event yours! To add or redefine an alias, execute the command:
```
samurai alias <name> <bootstrap> [<version>] [<description>]
```

### Remove an alias

To remove an alias, execute the command:
```
samurai alias <name>
```


## Modules (todo)

A module is a plugin added to Samurai. This plugin will execute some specific commands. For example, git module will init Git in your project.

### Add a module

Obviously, Samurai executes only modules that you have installed. So, you can decide, according to your own needs, which modules you want to execute.

#### When you install Samurai

During the installation, Samurai will ask you if you want to load some modules.

#### When you want

Execute the module command. You must specify the package of your module.
 
```
samurai module <vendor/package>
```

For example, if you want to load the git module:

```
samurai module raphhh/samurai-module-git
```
