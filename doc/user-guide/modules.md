# Modules

A module is a plugin added to Samurai. This plugin will execute some specific actions. For example, the git module will init Git in your project.

**You can easily develop your own module and add it to Samurai.** See the [module creation doc](https://github.com/Raphhh/samurai/wiki/Module-creation).

The main command to manage the modules is `module`. Just after this command you can specify some actions.

## Execute the modules

### During the scaffoling

By default, all the enable modules you have installed will be called during the `new` command.

```console
$ samurai new
```

If you want to avoid to execute the modules during this command, you can specify the option `--no-module`.
```console
$ samurai new --no-module
```

### Separately

Separately of the `new` command, you can (re)execute all the enable modules with the action `run` of the command `module`.

```console
$ samurai module run
```

You can also specify a module to execute only this one.

```console
$ samurai module run <module_name>
```

## List the installed modules

To list all the modules, execute the action `list`.

```console
$ samurai module list
```

To list a specific module, execute the same action but with the name of the module.

```console
$ samurai module list <module_name>
```

## Add or redefine a module

### Install recommended modules

You can install all the recommended packages with the `install` action.

```console
$ samurai module install
```

Note that you can only install pre-selected modules.

### Install a specific module

To install any module you want, you must specify its package.
 
```console
$ samurai module install <module_name> <vendor/package> [<version>] [<description>] [<source>]
```

The `module_name` is just a shortcut you will use in the module actions. Choose any name you want.

For example, if you want to load the git module:

```console
$ samurai module install git raphhh/samurai-module-git
```

Note that if the module was already present, it will be overridden after confirmation.


## Enable/disable a module

If you disable a module, it will be not called during the `new` command, neither with the `module run` command.

```console
$ samurai module disable <module_name>
```

If you want to enable a module, execute the action `enable`.

```console
$ samurai module enable <module_name>
```

## Update a module

Updating means that your module will be update to a more recent build version, according to its version constraints. 
Note that the update command will respect the version restriction as specified by Composer. 
See the [Composer update documentation](https://getcomposer.org/doc/03-cli.md#update) for more information.
                                                                                                                      

To update all the modules, just execute the action `update`. 

```console
$ samurai module update
```

If you want to update a specific module to a more recent version, execute the action with the module name.

```console
$ samurai module update <module_name>
```


## Remove a module

If you want to remove a module, execute the action `rm` with the name of the module.

```console
$ samurai module rm <module_name>
```
