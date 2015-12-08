# Installation of Samurai
  
Samurai uses Composer and Git. So, you have to install them first.

## Install Composer

### Set the executable

See the Composer documentation for [installation process](https://getcomposer.org/doc/00-intro.md).

For Unix system be sure that you have installed [Composer globally](https://getcomposer.org/doc/00-intro.md#globally):

```console
# mv composer.phar /usr/local/bin/composer
```

So, you can execute directly the composer command:
```console
$ composer --version
```

### Set the PATH of the composer bin

Second, to enable the execution of the samurai command, **make sure to place the [COMPOSER_BIN_DIR](https://getcomposer.org/doc/03-cli.md#composer-bin-dir) directory in your PATH**.

#### Unix system

By default, the COMPOSER_BIN_DIR is the directory `~/.composer/vendor/bin`.

For all users (restart):

```console
# echo 'PATH="$PATH:~/.composer/vendor/bin"' > /etc/profile.d/composer.sh
```

For current user (relogin):

```console
$ echo 'PATH="$PATH:~/.composer/vendor/bin"' >> ~/.profile
```

#### Windows

By default, the COMPOSER_BIN_DIR is the directory `C:\Users\<user>\AppData\Roaming\Composer\vendor\bin`. 

```console
setx PATH "%PATH%;C:\%HOMEPATH%\AppData\Roaming\Composer\vendor\bin"
```

## Install Git

For a better experience, you should also install [Git](http://git-scm.com/).

Do not forget to add a [global .gitignore](https://help.github.com/articles/ignoring-files/#explicit-repository-excludes), to exclude files or folders of your [IDE or OS](https://github.com/github/gitignore/tree/master/Global).

To test if git is installed:
```console
$ git --version
```

## Install Samurai

First, download Samurai with [Composer](https://getcomposer.org) **in the global env**.

```console
$ composer global require raphhh/samurai
```

## Test Samurai

The samurai executable is found when you run the following command in your terminal.

```console
$ samurai --version
```

## Install modules

Note, by default, no modules are installed. To install the recommended modules, execute the following command:
```console
$ samurai module install
```
See [modules docs](http://samurai.readthedocs.org/en/latest/modules/#add-or-redefine-a-module) for more information.

## List commands and help

To list all the available commands, enter the 'list' command:

```console
$ samurai list
```

To get help on a specific command, use the 'help' command:

```console
$ samurai help <command>
```
