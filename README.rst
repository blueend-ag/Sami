Sami: an API documentation generator
====================================

**WARNING**: Sami is not supported nor maintained by fabpot anymore. This fork has merged the best outstanding PRs.

Curious about what Sami generates? Have a look at the `Symfony API`_.

Installation
------------

.. caution::

    The current build requires **PHP 7.4**. Other versions may work, but have not been tested.

Get Sami as a `phar file`_ from the Github Release page. ( The phar is generated using https://github.com/humbug/box ) 

Check that everything worked as expected by executing the ``sami.phar`` file
with -vvv argument:

.. code-block:: bash

    $ php sami.phar -vvv
    

**Box Requirements Checker**

> Using PHP 7.x
> PHP is using the following php.ini file: /etc/php.ini

> Checking Box requirements:

✔ The application requires the version ">=7.0" or greater.

✔ The package "composer/ca-bundle" requires the extension "openssl". 

✔ The package "composer/ca-bundle" requires the extension "pcre".

✔ The package "nikic/php-parser" requires the extension "tokenizer".


[OK] Your system is ready to run the application.


    

.. note::

    Installing Sami as a regular Composer dependency is NOT supported. Sami is
    a tool, not a library. As such, it should be installed as a standalone
    package, so that Sami's dependencies do not interfere with your project's
    dependencies.

Configuration
-------------

Before generating documentation, you must create a configuration file. Here is
the simplest possible one:

.. code-block:: php

    <?php

    return new Sami\Sami('/path/to/symfony/src');

The configuration file must return an instance of ``Sami\Sami`` and the first
argument of the constructor is the path to the code you want to generate
documentation for.

Actually, instead of a directory, you can use any valid PHP iterator (and for
that matter any instance of the Symfony `Finder`_ class):

.. code-block:: php

    <?php

    use Sami\Sami;
    use Symfony\Component\Finder\Finder;

    $iterator = Finder::create()
        ->files()
        ->name('*.php')
        ->exclude('Resources')
        ->exclude('Tests')
        ->in('/path/to/symfony/src')
    ;

    return new Sami($iterator);

The ``Sami`` constructor optionally takes an array of options as a second
argument:

.. code-block:: php

    return new Sami($iterator, array(
        'theme'                => 'symfony',
        'title'                => 'Symfony2 API',
        'build_dir'            => __DIR__.'/build',
        'cache_dir'            => __DIR__.'/cache',
        'remote_repository'    => new GitHubRemoteRepository('username/repository', '/path/to/repository'),
        'default_opened_level' => 2,
    ));

And here is how you can configure different versions:

.. code-block:: php

    <?php

    use Sami\Sami;
    use Sami\RemoteRepository\GitHubRemoteRepository;
    use Sami\Version\GitVersionCollection;
    use Symfony\Component\Finder\Finder;

    $iterator = Finder::create()
        ->files()
        ->name('*.php')
        ->exclude('Resources')
        ->exclude('Tests')
        ->in($dir = '/path/to/symfony/src')
    ;

    // generate documentation for all v2.0.* tags, the 2.0 branch, and the master one
    $versions = GitVersionCollection::create($dir)
        ->addFromTags('v2.0.*')
        ->add('2.0', '2.0 branch')
        ->add('master', 'master branch')
    ;

    return new Sami($iterator, array(
        'theme'                => 'symfony',
        'versions'             => $versions,
        'title'                => 'Symfony2 API',
        'build_dir'            => __DIR__.'/../build/sf2/%version%',
        'cache_dir'            => __DIR__.'/../cache/sf2/%version%',
        'remote_repository'    => new GitHubRemoteRepository('symfony/symfony', dirname($dir)),
        'default_opened_level' => 2,
    ));

To generate documentation for a PHP 5.2 project, simply set the
``simulate_namespaces`` option to ``true``.

You can find more configuration examples under the ``examples/`` directory of
the source code.

Sami only documents the public API (public properties and methods); override
the default configured ``filter`` to change this behavior:

.. code-block:: php

    <?php

    use Sami\Parser\Filter\TrueFilter;

    $sami = new Sami(...);
    // document all methods and properties
    $sami['filter'] = function () {
        return new TrueFilter();
    };


Available Config Options
~~~~~~~~~~~~~~~~~~~~~~~~

Below is a list of available configuration options, their value types, their defaults, and a breif explanation:

+-----------------------+-----------------------------------------+---------------------------------------------+
| Option                | Type {default value}                    + Description                                 |
+=======================+=========================================+=============================================+
| build_dir             | string (path) {$pwd/build/}             | Directory in which to place build files     |
+-----------------------+-----------------------------------------+---------------------------------------------+
| cache_dir             | string (path) {$pwd/cache/}             | Directory in which to place cached files    |
|                       |                                         | generated by the build process              |
+-----------------------+-----------------------------------------+---------------------------------------------+
| default_opened_level  | int {2}                                 | Default level of the navigation menu        |
+-----------------------+-----------------------------------------+---------------------------------------------+
| include_parent_data   | bool {true}                             | include properties and methods from         |
|                       |                                         | anscestors on class pages                   |
+-----------------------+-----------------------------------------+---------------------------------------------+
| insert_todos          | bool {false}                            | Include @todo tags in documentation         |
+-----------------------+-----------------------------------------+---------------------------------------------+
| remote_repository     | Sami\\*RemoteRepository {null}          | The remote repository where this code is    |
|                       |                                         | stored.                                     |
+-----------------------+-----------------------------------------+---------------------------------------------+
| simulate_namespaces   | bool {false}                            | Simulate namespaces for projects based on   |
|                       |                                         | the PEAR convention                         |
+-----------------------+-----------------------------------------+---------------------------------------------+
| sort_class_constants  | bool|callable* {false}                  | Alphabetize constants in class docs         |
+-----------------------+-----------------------------------------+---------------------------------------------+
| sort_class_interfaces | bool|callable* {false}                  | Alphabetize interfaces in class docs        |
+-----------------------+-----------------------------------------+---------------------------------------------+
| sort_class_methods    | bool|callable* {false}                  | Alphabetize methods in class docs           |
+-----------------------+-----------------------------------------+---------------------------------------------+
| sort_class_properties | bool|callable* {false}                  | Alphabetize properties in class docs        |
+-----------------------+-----------------------------------------+---------------------------------------------+
| sort_class_traits     | bool|callable* {false}                  | Alphabetize traits in class docs            |
+-----------------------+-----------------------------------------+---------------------------------------------+
| source_dir            | string (path) {''}                      | The directory in which the source code to   |
|                       |                                         | document resides                            |
+-----------------------+-----------------------------------------+---------------------------------------------+
| source_url            | string (uri) {''}                       | A URL specifying where to find the source   |
|                       |                                         | code                                        |
+-----------------------+-----------------------------------------+---------------------------------------------+
| template_dirs         | string[] (paths) {[]}                   | More directories to search for templates    |
+-----------------------+-----------------------------------------+---------------------------------------------+
| theme                 | string {'default'}                      | Which theme to use for generated docs       |
+-----------------------+-----------------------------------------+---------------------------------------------+
| title                 | string {'API'}                          | The title to display in the generated docs  |
+-----------------------+-----------------------------------------+---------------------------------------------+
| versions              | Sami\\Version\\VersionCollection {null} | A collection pointing to one or more SCM    |
|                       |                                         | tags representing versions to document      |
+-----------------------+-----------------------------------------+---------------------------------------------+
| version               | string {'master'}                       | A string SCM tagname representing the       |
|                       |                                         | version to document (this is a fallback)    |
+-----------------------+-----------------------------------------+---------------------------------------------+


Rendering
---------

Now that we have a configuration file, let's generate the API documentation:

.. code-block:: bash

    $ php sami.phar update /path/to/config.php

The generated documentation can be found under the configured ``build/``
directory (note that the client side search engine does not work on Chrome due
to JavaScript execution restriction, unless Chrome is started with the
"--allow-file-access-from-files" option -- it works fine in Firefox).

By default, Sami is configured to run in "incremental" mode. It means that when
running the ``update`` command, Sami only re-generates the files that needs to
be updated based on what has changed in your code since the last execution.

Sami also detects problems in your phpdoc and can tell you what you need to fix
if you add the ``-v`` option:

.. code-block:: bash

    $ php sami.phar update /path/to/config.php -v

Creating a Theme
----------------

If the default themes do not suit your needs, you can very easily create a new
one, or just override an existing one.

A theme is just a directory with a ``manifest.yml`` file that describes the
theme (this is a YAML file):

.. code-block:: yaml

    name:   symfony
    parent: default

The above configuration creates a new ``symfony`` theme based on the
``default`` built-in theme. To override a template, just create a file with
the same name as the original one. For instance, here is how you can extend the
default class template to prefix the class name with "Class " in the class page
title:

.. code-block:: jinja

    {# pages/class.twig #}

    {% extends 'default/pages/class.twig' %}

    {% block title %}Class {{ parent() }}{% endblock %}

If you are familiar with Twig, you will be able to very easily tweak every
aspect of the templates as everything has been well isolated in named Twig
blocks.

A theme can also add more templates and static files. Here is the manifest for
the default theme:

.. code-block:: yaml

    name: default

    static:
        'css/sami.css': 'css/sami.css'
        'css/bootstrap.min.css': 'css/bootstrap.min.css'
        'css/bootstrap-theme.min.css': 'css/bootstrap-theme.min.css'
        'fonts/glyphicons-halflings-regular.eot': 'fonts/glyphicons-halflings-regular.eot'
        'fonts/glyphicons-halflings-regular.svg': 'fonts/glyphicons-halflings-regular.svg'
        'fonts/glyphicons-halflings-regular.ttf': 'fonts/glyphicons-halflings-regular.ttf'
        'fonts/glyphicons-halflings-regular.woff': 'fonts/glyphicons-halflings-regular.woff'
        'js/bootstrap.min.js': 'js/bootstrap.min.js'
        'js/jquery-1.11.1.min.js': 'js/jquery-1.11.1.min.js'
        'js/handlebars.min.js': 'js/handlebars.min.js'
        'js/typeahead.min.js': 'js/typeahead.min.js'

    global:
        'index.twig':      'index.html'
        'doc-index.twig':  'doc-index.html'
        'namespaces.twig': 'namespaces.html'
        'classes.twig':    'classes.html'
        'interfaces.twig': 'interfaces.html'
        'traits.twig':     'traits.html'
        'opensearch.twig': 'opensearch.xml'
        'search.twig':     'search.html'
        'sami.js.twig':    'sami.js'

    namespace:
        'namespace.twig': '%s.html'

    class:
        'class.twig': '%s.html'


Files are contained into sections, depending on how Sami needs to treat them:

* ``static``: Files are copied as is (for assets like images, stylesheets, or
  JavaScript files);

* ``global``: Templates that do not depend on the current class context;

* ``namespace``: Templates that should be generated for every namespace;

* ``class``: Templates that should be generated for every class.

.. _Symfony API: http://api.symfony.com/
.. _phar file:   https://github.com/blueend-ag/Sami/releases
.. _Finder:      http://symfony.com/doc/current/components/finder.html

Search Index
~~~~~~~~~~~~

The autocomplete and search functionality of Sami is provided through a
search index that is generated based on the classes, namespaces, interfaces,
and traits of a project. You can customize the search index by overriding the
``search_index_extra`` block of ``sami.js.twig``.

The ``search_index_extra`` allows you to extend the default theme and add more
entries to the index. For example, some projects implement magic methods that
are dynamically generated at runtime. You might wish to document these methods
while generating API documentation and add them to the search index.

Each entry in the search index is a JavaScript object that contains the
following keys:

type
    The type associated with the entry. Built-in types are "Class",
    "Namespace", "Interface", "Trait". You can add additional types specific
    to an application, and the type information will appear next to the search
    result.

name
    The name of the entry. This is the element in the index that is searchable
    (e.g., class name, namespace name, etc).

fromName
    The parent of the element (if any). This can be used to provide context for
    the entry. For example, the fromName of a class would be the namespace of
    the class.

fromLink
    The link to the parent of the entry (if any). This is used to link a child
    to a parent. For example, this would be a link from a class to the class
    namespace.

doc
    A short text description of the entry.

One such example of when overriding the index is useful could be documenting
dynamically generated API operations of a web service client. Here's a simple
example that adds dynamically generated API operations for a web service client
to the search index:

.. code-block:: jinja

    {% extends "default/sami.js.twig" %}

    {% block search_index_extra %}
        {% for operation in operations -%}
            {"type": "Operation", "link": "{{ operation.path }}", "name": "{{ operation.name }}", "doc": "{{ operation.doc }}"},
        {%- endfor %}
    {% endblock %}

This example assumes that the template has a variable ``operations`` available
which contains an array of operations.

.. note::

    Always include a trailing comma for each entry you add to the index. Sami
    will take care of ensuring that trailing commas are handled properly.


Building sami.phar
~~~~~~~~~~~~~~~~~~

1. Clone/Download this repo
2. Run composer to update/install all dependencies ``composer update``
2. Download box.phar from https://github.com/box-project/box/releases/ and make it executable (e.g. ``chmod 755 ./box.phar`` )
4. Run ``./box.phar compile`` to compile sami.phar from source using box.json
5. Check your phar with ``./box.phar info bin/sami.phar``