# ArangoDB O.R.M. for PHP
=========================

This adapter eliminates the redundancy and complexity of many database interactions.

Inspired by Eloquent, we replicated many of its patterns.

_The package works closely with the official ArangoDB PHP driver, and takes care not to duplicate already provided code_

Features
--------

* Provides an easy-to-understand pattern, making it easy to scale in the right direction
* Define and initialize a DB schema with default documents
* Powerful core model classes, easy to inherit and extend

## Table of Contents
====================
1. Getting Setup
2. Extending the Model Classes
3. Defining a Collection Schema
4. Defining a Document Schema
5. Common scaling patterns

### Getting Setup
Getting setup is simple! Just provide the DB settings, and extend the model classes!

### Extending the Model Classes
We provide two core model classes for extending:
* Vertex Model
* Edge Model

The only difference is that the <b>Edge Model</b> has <em>to</em> and <em>from</em> properties ( <b>Vertex Models</b> )

### Defining a Collection Schema
You may optionally define a loose collection schema for easy initialization. Just build the object in PHP and pass it to
 <code>DB::initializeCollections( $schema )</code>
 
### Defining a Document Schema
Similarly to defining a collection schema, you may optionally define some default documents by building the schema object in PHP and passing it to
<code>DB::populateCollections</code>

### Common Scaling Patterns
There are two classic project patterns you would likely follow. 
1. Package by Layer
2. Package by Feature

In a <b>package-by-layer</b> setup, you would place all of your models in a root folder titled <em>models</em> or <em>orm</em>

In a <b>package-by-feature</b> setup, you would place all of your models in their respective feature directories