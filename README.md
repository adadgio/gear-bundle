Adadgio RocketBundle
================

The AdadgioRocketBundle gives you very useful simple or more advanced libaries to speed up and mainting your Symfony 2 project development.

# Installation

Add AdadgioRocketBundle with composer. Add in your composer file:

	"adadgio/rocket-bundle": "1.0.*@dev"

# Features

## ApiEngine

An annotation class which lets your create REST services in no time. Support Hmac authentication, required param and params assertions.

## Validator

A set of shortcuts usgin symfony build in validators (Email, Url)

## Cache

A very simple but highly effective file cache system using Sf2 cache folder. Lets you group cache files per folder easily and handle cache expiration.

## Reflection analysis utility

A very precise set of tools to use in kernel or event listeners to analyse methods and classes your events hooks.


## Third party connectors

### IronMQ helper

Dead simple class to push messages to [IronMQ](http://www.iron.io) and add/remove subscribers.

### Box View API helper

Lets you work with the [Box View API]https://developers.box.com/view/) awesome tools.