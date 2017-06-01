# WP Config Manager

This WP-CLI command helps you manage WordPress themes, plugins configuration better.

## Install

Via WP-CLI Package Manager (requires WP-CLI >= 0.23)

Just run `wp package install finaldream/wp-config-manager`

## Commands

```
NAME

  wp config

DESCRIPTION

  Automated load all configuration from .yaml file

SYNOPSIS

  wp config <command>

SUBCOMMANDS

  init      Create new configuration file from template
  load      Load all configuration from current file
```

### `init`

Create new configuration file from template

####`--force`
Overwrites an existing configuration file, if it exists.

####`--wp-config`
Relative path to store yaml configuration file

### `load`

Load all configuration from current file and write into WP database

####`--wp-config`
Relative path to load yaml configuration file

####`--override-network-plugins=<boolean>`
Use this to on/off overriding WordPress activated plugins, Default is `TRUE`

## Sample Config

You can review a sample configuration file here [sample-wp-config.yaml](sample-wp-config.yaml)
