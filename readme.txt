=== POST2PDF Converter ===
Contributors: redcocker
Donate link: http://www.near-mint.com/blog/donate
Tags: pdf, post, page, convert, download, tcpdf
Requires at least: 2.8
Tested up to: 3.3
Stable tag: 0.1

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
1. Enable "Font" option and enter font name in "PDF Settings" section.
1. If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, disable "Font subsetting" option.

* If your font file is xxxx.z, font name is xxxx.

You can also use following fonts.

[日本語フリーFONT](http://www.monzen.org/Refdoc/tcpdf_freefontj/ "日本語フリーFONT")

Note: When this plugin is updated, added fonts will be removed.

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

"FreeFont" supports International Phonetic Alphabet, Arabic, Hebrew, Armenian, Georgian, Ethiopian and Thai characters.

= Add new fonts =

You can add new fonts.

**How to convert a font**

1. Read [TCPDF Fonts](http://www.tcpdf.org/fonts.php "TCPDF Fonts").

**How to install a font**

1. Upload all *.php, *.z, *.ctg.z files to `/wp-content/plugins/post2pdf-converter/tcpdf/fonts` directory.

**How to use the added font**

1. Go to "Settings" -> "POST2PDF Converter" to configure.
1. Enable "Font" option and enter font name in "PDF Settings" section.
1. If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, disable "Font subsetting" option.

* If your font file is xxxx.z, font name is xxxx.

Note: When this plugin is updated, added fonts will be removed.

== Screenshots ==

1. This is the download link.
2. This is setting panel.

== Changelog ==

= 0.1 =
* This is the initial release.

== Upgrade Notice ==

= 0.1 =
This is the initial release.
