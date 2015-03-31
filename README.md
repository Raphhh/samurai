# Samurai

[![Latest Stable Version](https://poser.pugx.org/raphhh/samurai/v/stable.svg)](https://packagist.org/packages/raphhh/samurai)
[![Build Status](https://travis-ci.org/Raphhh/samurai.png)](https://travis-ci.org/Raphhh/samurai)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Raphhh/samurai/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Raphhh/samurai/)
[![Code Coverage](https://scrutinizer-ci.com/g/Raphhh/samurai/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Raphhh/samurai/)
[![Total Downloads](https://poser.pugx.org/raphhh/samurai/downloads.svg)](https://packagist.org/packages/raphhh/samurai)
[![Reference Status](https://www.versioneye.com/php/raphhh:samurai/reference_badge.svg?style=flat)](https://www.versioneye.com/php/raphhh:samurai/references)
[![License](https://poser.pugx.org/raphhh/samurai/license.svg)](https://packagist.org/packages/raphhh/samurai)

Samurai is a PHP scaffolding tool. It helps you to start a new project in PHP, generating all the base files in a simple command line.

Samurai generates all the files you need for a library, a web application, a frameworked project, and so on. You can even add your own bootstrap.

![Samurai during project installation](https://raw.githubusercontent.com/Raphhh/samurai/master/doc/samurai-new.png)


## What does Samurai scaffold?

Samurai installs and params your project:

 1. Download the bootstrap and its dependency with Composer
 2. Param the Composer config (composer.json) (todo: author + package)
 3. Dump the autoloader of Composer with your new Package name (todo)
 4. Clean some files (changelog, etc) (todo)
 5. Execute the installed modules. For example:
     - Init git for the project (git module) (todo)
     - Create a new repo on GitHub and link it to your project (github module) (todo)
     - Link your project to Packagist (packagist module) (todo)
     - Link your project to Travis-ci (travis module) (todo)
     - Link your project to Scrutinizer (scrutinizer module) (todo)
     - Add a file phpunit.xml (phpunit module) (todo)


## Installation of Samurai

### Download Samurai

First, download Samurai with [Composer](https://getcomposer.org) in the global env.

```console
$ composer global require raphhh/samurai
```

### Set the PATH of Composer

#### Linux

Make sure to place the ~/.composer/vendor/bin directory in your PATH.

For all users (restart):

```console
# echo "export PATH=$PATH:~/.composer/vendor/bin" >> /etc/profile
```

For current user (relogin):

```console
$ echo "export PATH=$PATH:~/.composer/vendor/bin" >> ~/.profile
```

#### Windows

Make sure to place the C:\%HOMEPATH%\AppData\Roaming\Composer\vendor\bin in your PATH. 

```console
setx PATH "%PATH%;C:\%HOMEPATH%\AppData\Roaming\Composer\vendor\bin"
```

### Test Samurai

So, the samurai executable is found when you run the command in your terminal.

```console
$ samurai help --version
```

## List commands and help

To list all the available commands, enter the 'list' command:

```console
$ samurai list
```

To get help on a specific command, use the 'help' command:

```console
$ samurai help <command>
```


## Create your project

The samurai "new" command will create a fresh installation of a new project. 

### Choose between projects

If you do not specify a bootstrap to install, Samurai will list all the project's bootstraps.

```console
$ samurai new
```
You just have to select a bootstrap to install.

### Specify a pre-defined project

If you know the project, you can directly specify it in the command. Samurai will create your project from this bootstrap. 

```console
$ samurai new <bootstrap>
```

In fact, you specify an alias of a project. An alias is just a defined bootstrap and version. See alias section for more information.

For example, with alias 'lib', Samurai will install a basic PHP library bootstrap.

```console
$ samurai new lib
```

### Specify another bootstrap and its version

You can specify any project loadable with Composer, event if you do not have alias. 

```console
$ samurai new <vendor/package> [<version>]
```

For example, you can create a new Symfony app by specifying its package. (This is just an example, because the 'symfony' alias already exists)

```console
$ samurai new symfony/framework-standard-edition
```

Or if you want a specific version of a package, add the version you want just after the bootstrap. If you do not specify a version, Samurai will take the last stable version of the bootstrap.

```console
$ samurai new symfony/framework-standard-edition 1.0.0
```

If you install a project from a non-aliased bootstrap, do not hesitate to add it in the alias list. It is very simple. See alias section for more information. For example, Symfony is already available with 'symfony' alias.

```console
$ samurai new symfony
```

### Specify a project dir

By default, Samurai will put your project into the same directory as your project name.

For example, if you run Samurai from "~/projects" and you name your project "my/lib", it will put your project in "~/projects/my/lib".

But you can specify another directory with the option "--dir" or "-d".

```console
$ samurai new lib -d specific/path/to/my/project
```

## Alias

Alias are simple words linked to a specific bootstrap at a specific version. For example, the alias 'lib' points to the package 'raphhh/php-lib-bootstrap' at last stable version.

### List the existing alias

To list all the alias, execute the command:
```console
$ samurai alias
```

### Add or redefine an alias

You can easily add any bootstrap you want, event yours! To add or redefine an alias, execute the command:
```console
$ samurai alias <name> <bootstrap> [<version>] [<description>] [<source>]
```

### Remove an alias

To remove an alias, execute the command:
```console
$ samurai alias <name>
```


## Modules (todo)

A module is a plugin added to Samurai. This plugin will execute some specific commands. For example, git module will init Git in your project.

### Add a module

Obviously, Samurai executes only modules that you have installed. So, you can decide, according to your own needs, which modules you want to execute.

#### When you install Samurai

During the installation, Samurai will ask you if you want to load some modules.

#### When you want

Execute the module command. You must specify the package of your module.
 
```console
$ samurai module <vendor/package>
```

For example, if you want to load the git module:

```console
$ samurai module raphhh/samurai-module-git
```
