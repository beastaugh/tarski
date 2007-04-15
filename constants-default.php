<?php // Tarski constants file - constants.php
/*
constants.php allows you to insert code into certain areas of the theme without needing to edit the actual theme files, thus preserving your changes when upgrading.

This file uses heredoc syntax, which you may not be used to. For help:
http://php.net/manual/en/language.types.string.php#language.types.string.syntax.heredoc

HTML can be used here, as can pre-existing variables. Advanced users can, of course, use their own PHP code in here to do all sorts of clever things.
*/


// $headerInclude will be added to the bottom of the <head></head> block
$headerInclude = <<<HEADERINCLUDE

HEADERINCLUDE;


// $navbarInclude will be added to the end of the top navigation list. Make sure any includes you use are enclosed in <li> tags!
// E.g., <li><a href="http://tarskitheme.com/forum/">Forum</a></li>
$navbarInclude = <<<NAVBARINCLUDE

NAVBARINCLUDE;


// $frontPageInclude can be used to add content to the front page, after the first post. For example, you could add a quick intro to the site, or some adverts.
$frontPageInclude = <<<FRONTPAGEINCLUDE

FRONTPAGEINCLUDE;


// $sidebarTopInclude will be added to the very top of the sidebar block.
$sidebarTopInclude = <<<SIDEBARTOPINCLUDE

SIDEBARTOPINCLUDE;


// $sidebarBottomInclude will be added to the very bottom of the sidebar block.
$sidebarBottomInclude = <<<SIDEBARBOTTOMINCLUDE

SIDEBARBOTTOMINCLUDE;


// $noSidebarInclude allows you to have an alternate sidebar on non-homepage pages when the main sidebar is turned off.
$noSidebarInclude = <<<NOSIDEBARINCLUDE

NOSIDEBARINCLUDE;


// $404PageInclude allows you to customise the text on Error 404 (File Not Found) pages. Please note that anything added here REPLACES the default 404 error text.
$errorPageInclude = <<<ERRORPAGEINCLUDE

ERRORPAGEINCLUDE;


// $postEndInclude will be added at the bottom of each post, before the comments.
$postEndInclude = <<<POSTENDINCLUDE

POSTENDINCLUDE;


// $pageEndInclude will be added at the bottom of each page.
$pageEndInclude = <<<PAGEENDINCLUDE

PAGEENDINCLUDE;


// $commentsFormInclude will be added to the comments form. You might want to use it for things like a link to comment formatting help, comment rules, etc.
$commentsFormInclude = <<<COMMENTSFORMINCLUDE

COMMENTSFORMINCLUDE;


// $searchTopInclude will be added above the search block in the theme's footer.
$searchTopInclude = <<<SEARCHTOPINCLUDE

SEARCHTOPINCLUDE;


// $searchBottomInclude will be added below the search block in the theme's footer.
$searchBottomInclude = <<<SEARCHBOTTOMINCLUDE

SEARCHBOTTOMINCLUDE;


// $footerInclude will be added below the "Powered by WordPress and Tarski" text in the footer. You could include a copyright notice or something similar here.
$footerInclude = <<<FOOTERINCLUDE

FOOTERINCLUDE;


// $archivesPageInclude is a hook for the left-hand side of the archives page. This is most likely to be useful for those who have turned off the display of categories in the Tarski Options.
$archivesPageInclude = <<<ARCHIVESPAGEINCLUDE

ARCHIVESPAGEINCLUDE;

?>