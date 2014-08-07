Massive Build Bundle
====================

[![](https://travis-ci.org/massiveart/MassiveBuildBundle.png)](https://travis-ci.org/massiveart/MassiveBuildBundle)

The Massive Build Bundle provides a `massive:build` command which runs build
targets.

Targets are effectively classes which execute arbitary code, and are
registred in the dependency injection container via. tags. Targets can
depend on other targets.

The aim of this bundle is to provide an extensible, decoupled, way of building
software project environments, especially in a development context.

This tool is not mean to replace Make or Ant or Phing. The bundle should only
be used to execute build steps which are contained in the client application.

## Requirements

* Symfony: 2.4+
