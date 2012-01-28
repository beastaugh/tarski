Tarski
======

[Tarski] is an elegant, flexible [WordPress] theme developed by
[Benedict Eastaugh]. As a WordPress extension it is licensed under the GPL;
please consult the COPYRIGHT file that should have been provided with this
README for more details.

This version of Tarski requires **WordPress 3.2**, but it works fine with
**WordPress 3.3** too.

  [Tarski]:            http://tarskitheme.com/
  [WordPress]:         http://wordpress.org/
  [Benedict Eastaugh]: http://extralogical.net/


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
building WordPress [child themes].

  [child themes]: http://codex.wordpress.org/Theme_Development


### Writing plugins

Tarski's theme hooks [API] is an extension of the basic WordPress hooks
API, and allows for major modifications to be made to almost every aspect of
the theme's functionality.  [Writing Plugins] explains the process of creating
a plugin that modifies some aspect of Tarski, while the [Hooks Reference] is a
complete API reference. A library of [example plugins] rounds out the
documentation with example code.

  [API]:             http://tarskitheme.com/help/hooks/
  [Writing Plugins]: http://tarskitheme.com/help/hooks/plugins/
  [Hooks Reference]: http://tarskitheme.com/help/hooks/reference/
  [example plugins]: http://tarskitheme.com/help/hooks/example-plugins/


Contributing
------------

Tarski relies on contributions from the community. The main areas of activity
are: reporting bugs; creating and updating translations; building child themes;
writing plugins; and lastly, improving the theme itself.


### Reporting bugs

Bugs should be reported on the Tarski [issue tracker]. If you're not sure
whether the behaviour you're observing is caused by Tarski or WordPress, or
whether it's intentional or not, please post on the [Tarski forum]. Security
issues should be reported directly to [Benedict].

  [issue tracker]: http://github.com/beastaugh/tarski/issues
  [Tarski forum]:  http://tarskitheme.com/forum/
  [Benedict]:      mailto:benedict@eastaugh.net


### Translations

There are a large number of translations already available for Tarski, but new
and updated localisation files are always welcome. Please see the
[localisation page] for more details.

  [localisation page]: http://tarskitheme.com/help/localisation/


### Core contributions

If you want to contribute directly to the core Tarski code, please fork the
project on [GitHub], make your changes in a topic branch and send
a pull request. Contributions are particularly welcome in the following areas:

  * Improving the hooks documentation
  * Auditing the code for potential security issues
  * Suggesting API extensions and improvements

Happy hacking!

  [GitHub]: http://github.com/beastaugh/tarski
