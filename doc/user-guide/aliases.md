# Aliases

Aliases are simple words linked to a specific bootstrap at a specific version. 
For example, the alias 'lib' points to the package 'raphhh/php-lib-bootstrap' at last stable version.
**You can add any bootstrap you want, event yours.**

The main command to manage the modules is `alias`. Just after this command you can specify some actions.

## List the existing aliases

To list all the aliases, execute the action `list`.
```console
$ samurai alias list
```
To list a specific alias, execute the same action but with the name of the alias:
```console
$ samurai module list <alias_name>
```

## Add or redefine an alias

You can easily add any bootstrap you want, even yours! To add or redefine an alias, execute the action `save`.
```console
$ samurai alias save <alias_name> <bootstrap> [<version>] [<description>] [<source>]
```

## Remove an alias

To remove an alias, execute the the action `rm`.
```console
$ samurai alias rm <alias_name>
```
