=== POST2PDF Converter ===
Contributors: redcocker
Donate link: http://www.near-mint.com/blog/donate
Tags: pdf, post, page, convert, download, tcpdf
Requires at least: 2.8
Tested up to: 3.3
Stable tag: 0.1.3

This plugin converts your post/page to PDF for visitors and visitor can download it easily.

== Description ==

This plugin converts your post/page to PDF for visitors and visitor can download it easily.

You can add a download link above/below every posts/pages.

Note: This plugin requires PHP 5.

= Features =

* Base on "[TCPDF](http://www.tcpdf.org/ "TCPDF")".
* Easy to add a download link into every posts/pages.
* Easy to configure.
* Localization: English(Default), 日本語(Japanese, UTF-8).

== Installation ==

= Installation =

1. Upload plugin folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Go to "Settings" -> "POST2PDF Converter" to configure.

= Fonts for Japanese =

This plugin includes following Japanese fonts.

Font name: cid0jp, kozgopromedium, kozminproregular

This plugin can't quite convert contents which include Japanese characters to PDF file using bundled fonts.

However, you can download better Japanese fonts and use them.

If your contents are written in Japanese, please install one of these fonts. You have only to upload font files without converting and configure some setting options.

[TCPDF用日本語フォント](http://www.near-mint.com/blog/software/rcjfont-for-tcpdf "TCPDF用日本語フォント")

1. Upload all *.php, *.z, *.ctg.z files to `/wp-content/plugins/post2pdf-converter/tcpdf/fonts` directory.
1. Go to "Settings" -> "POST2PDF Converter" to configure.
1. Enter font name to "Font" option in "PDF Settings" section.
1. If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, disable "Font subsetting" option.

* If your font file is xxxx.z, font name is xxxx.

You can also use following fonts.

[日本語フリーFONT](http://www.monzen.org/Refdoc/tcpdf_freefontj/ "日本語フリーFONT")

= Fonts for Latin, Greek and Cyrillic =

This plugin includes following Latin fonts.

Font name: courier, helvetica, times

You can also use following fonts. However, If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, you had better disable "Font subsetting" option.

Font name: pdfacourier, pdfahelvetica, pdfatimes

You can also use "FreeFont" or "DejaVu fonts". However, If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, you had better disable "Font subsetting" option.

= Fonts for Simplified Chinese =

This plugin includes following Simplified Chinese fonts.

Font name: cid0cs, stsongstdlight

= Fonts for Traditional Chinese =

This plugin includes following Traditional Chinese fonts.

Font name: cid0ct, msungstdlight

= Fonts for Korean =

This plugin includes following Korean fonts.

Font name: cid0kr, hysmyeongjostdmedium

= Fonts for Cyrillic =

You can use "FreeFont" or "DejaVu fonts". However, If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, you had better disable "Font subsetting" option.

= Fonts for Arabic =

This plugin includes following Arabic fonts.

Font name: aealarabiya, aefurat

You can also use "FreeFont". However, If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, you had better disable "Font subsetting" option.

= Fonts for Hebrew, Armenian, Georgian, Ethiopian and Thai =

You can use "FreeFont". However, If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, you had better disable "Font subsetting" option.

= DejaVu fonts =

This plugin inclues "DejaVu fonts". However, If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, you had better disable "Font subsetting" option.

Font name: dejavusans, dejavusanscondensed, dejavusansextralight, dejavusansmono, dejavuserif, dejavuserifcondensed

"DejaVu fonts" support Latin, Greek and Cyrillic characters.

= FreeFont =

This plugin inclues "FreeFont". However, If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, you had better disable "Font subsetting" option.

Font name: freemono, freesans, freeserif

"FreeFont" supports Latin, Greek, Cyrillic, Arabic, Hebrew, Armenian, Georgian, Ethiopian and Thai characters.

= Add new fonts =

You can add new fonts.

**How to convert a font**

1. Read [TCPDF Fonts](http://www.tcpdf.org/fonts.php "TCPDF Fonts").

**How to install a font**

1. Upload all *.php, *.z, *.ctg.z files to `/wp-content/plugins/post2pdf-converter/tcpdf/fonts` directory.

**How to use the added font**

1. Go to "Settings" -> "POST2PDF Converter" to configure.
1. Enter font name to "Font" option in "PDF Settings" section.
1. If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, disable "Font subsetting" option.

* If your font file is xxxx.z, font name is xxxx.

= Safe fonts directory =

After automatic updating, your added fonts will be removed.

If you enable "Safe fonts directory" option in the setting panel, your fonts will never be removed.

Before you enable "Safe fonts directory", you must make /wp-content/tcpdf-fonts/ directory on your server manually. And upload/move original fonts and your fonts to new directory and enable "Safe fonts directory".

Now you can remove original fonts directory.

* Orignal fonts: Unzip a plugin zip file and you can find orignal fonts in /post2pdf-converter/tcpdf/fonts.
* Original fonts directory: /YOUR PLUGIN DIRECTORY/post2pdf-converter/tcpdf/fonts

However, after automatic updating, fonts in the new directory will never be updated. You must maintain the new fonts directory by yourself.

== Frequently Asked Questions ==

= Q. Created PDF file has garbled characters. =

A. Go to the setting panel and enable "Add default font to font-family" option.

== Screenshots ==

1. This is the download link.
2. This is setting panel.

== Changelog ==

= 0.1.3 =
* Added new setting option to define PDF file name.
* Added new setting option to set default monospaced font.
* Added new setting option to change fonts directory location.
* Updated post2pdf_conv_add_style() to load CSS on only posts or pages.
* Fix a bug: In some languages, created PDF has garbled characters.
* Fix a bug: When content contains font properties(font-family), created PDF has garbled characters.
* Fix a bug: When only log-in users are allowed to access, download link appears for guest users.
* Fix a bug: When a post has long title, the title on header sticks out from the right edge.
* Fix a bug: PDF file name length possibly exceed system limit.

= 0.1 =
* This is the initial release.

== Upgrade Notice ==

= 0.1.3 =
This version has new features, changes and bug fixes.

= 0.1 =
This is the initial release.
