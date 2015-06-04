# Scaffolding new project

The samurai `new` command will create a **fresh installation of a new project**. 

## Choose between projects

If you do not specify a bootstrap to install, Samurai will list all the project's bootstraps.

```console
$ samurai new
```
You just have to select a bootstrap to install.

## Specify a pre-defined project

If you know the project, you can directly specify it in the command. Samurai will create your project from this bootstrap. 

```console
$ samurai new <alias>
```

In fact, you specify an alias of a project. An alias is just a defined bootstrap and version. See alias section for more information.

For example, with alias 'lib', Samurai will install a basic PHP library bootstrap.

```console
$ samurai new lib
```

## Specify another bootstrap and its version

You can specify any project loadable with Composer, event if you do not have alias. 

```console
$ samurai new <vendor/package> [<version>]
```

For example, you can create a new Puppy app by specifying its package.

```console
$ samurai new raphhh/puppy
```

Or if you want a specific version of a package, add the version you want just after the bootstrap. If you do not specify a version, Samurai will take the last stable version of the bootstrap.

```console
$ samurai new raphhh/puppy 1.1.0
```

If you install a project from a non-aliased bootstrap, do not hesitate to add it in the alias list. It is very simple. See alias section for more information.

```console
$ samurai alias save puppy raphhh/puppy
$ samurai new puppy
```

## Specify a project dir

By default, Samurai will put your project into the same directory as your project name.

For example, if you run Samurai from `~/projects` and you name your project `my/lib`, it will put your project in `~/projects/my/lib`.

But you can specify another directory with the option `--dir` or `-d`. This must be a relative path from cwd.

```console
$ samurai new lib -d specific/path/to/my/project
```
