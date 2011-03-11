Tarski
======

[Tarski][tarski] is an elegant, flexible [WordPress][wp] theme developed by
[Benedict Eastaugh][exl]. As a WordPress extension it is licensed under the
GPL; please consult the COPYRIGHT file that should have been provided with this
README for more details.


Installing
----------

Tarski is installed like any other WordPress theme: upload the files to the
`wp-content/themes` directory of your WordPress installation, and activate it
from the Appearance page in your WordPress admin panel.


Customising
-----------

Tarski provides its own options page, allowing for the easy customisation of a
number of aspects of the theme. Just go to the Tarski options page in the
Appearance section of your WordPress admin panel and adjust the settings.


Extending
---------

Apart from the options which Tarski supports two basic extension mechanisms:
plugins and child themes. Both of these are provided by WordPress, but Tarski
improves upon them in several ways, firstly by providing a larger repertoire of
API hooks which plugins can use, and secondly by allowing users to select
alternate styles and header images from child themes as well as Tarski itself.


### Child themes

Tarski's extensive API and large library of utility functions, allied to the
customisability provided by its options page, makes it an excellent base for
building [WordPress child themes][td].


### Writing plugins

[Tarski's theme hooks API][hooks] is an extension of the basic WordPress hooks
API, and allows for major modifications to be made to almost every aspect of
the theme's functionality. [Writing Plugins for Tarski][plugins] explains the
process of creating a plugin that modifies some aspect of Tarski, while the
[Hooks Reference][ref] is a complete API reference. A library of
[example plugins][examples] rounds out the documentation with example code.


Contributing
------------

Tarski relies on contributions from the community. The main areas of activity
are: reporting bugs; creating and updating translations; building child themes;
writing plugins; and lastly, improving the theme itself.


### Reporting bugs

Bugs should be reported on the [Tarski issue tracker][issues]. If you're not
sure whether the behaviour you're observing is caused by Tarski or WordPress,
or whether it's intentional or not, please post on the [Tarski forum][forum].
Security issues should be reported [directly to Benedict][contact].


### Translations

There are a large number of translations already available for Tarski, but new
and updated localisation files are always welcome. Please see the
[localisation page][i18n] for more details.


### Core contributions

If you want to contribute directly to the core Tarski code, please
[fork the project on GitHub][gh], make your changes in a topic branch and send
a pull request. Contributions are particularly welcome in the following areas:

  * Improving the hooks documentation
  * Auditing the code for potential security issues
  * Suggesting API extensions and improvements

Happy hacking!


  [tarski]:   http://tarskitheme.com/
  [wp]:       http://wordpress.org/
  [exl]:       http://extralogical.net/
  [issues]:   http://github.com/ionfish/tarski/issues
  [forum]:    http://tarskitheme.com/forum/
  [contact]:  http://extralogical.net/about/
  [i18n]:     http://tarskitheme.com/help/localisation/
  [td]:       http://codex.wordpress.org/Theme_Development
  [hooks]:    http://tarskitheme.com/help/hooks/
  [plugins]:  http://tarskitheme.com/help/hooks/plugins/
  [ref]:      http://tarskitheme.com/help/hooks/reference/
  [examples]: http://tarskitheme.com/help/hooks/example-plugins/
  [gh]:       http://github.com/ionfish/tarski
