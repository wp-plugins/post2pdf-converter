<?php
/*
Plugin Name: POST2PDF Converter
Plugin URI: http://www.near-mint.com/blog/software/post2pdf-converter
Description: This plugin converts your post/page to PDF for visitors and visitors can download it easily.
Version: 0.2
Author: redcocker
Author URI: http://www.near-mint.com/blog/
Text Domain: post2pdf_conv
Domain Path: /languages
*/
/*
Last modified: 2012/1/2
License: GPL v2(Except "TCPDF" libraries)
*/
/*  Copyright 2011 M. Sumitomo

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/*
"POST2PDF Converter" uses TCPDF by Nicola Asuni.
http://www.tcpdf.org/
TCPDF is licensed under the LGPL 3.
*/

class POST2PDF_Converter {

	var $post2pdf_conv_plugin_url;
	var $post2pdf_conv_db_ver = "0.2";
	var $post2pdf_allowed_str = "3";
	var $post2pdf_conv_setting_opt;
	var $post2pdf_conv_sig;

	function __construct() {
		load_plugin_textdomain('post2pdf_conv', false, dirname(plugin_basename(__FILE__)).'/languages');
		$this->post2pdf_conv_plugin_url = plugin_dir_url(__FILE__);

		$this->post2pdf_conv_setting_opt = get_option('post2pdf_conv_setting_opt');
		$this->post2pdf_conv_sig = get_option('post2pdf_conv_sig');

		add_action('plugins_loaded', array(&$this, 'post2pdf_conv_check_db_ver'));
		add_action('admin_menu', array(&$this, 'post2pdf_conv_register_menu_item'));
		add_filter('plugin_action_links', array(&$this, 'post2pdf_conv_setting_link'), 10, 2);
		add_filter('the_content', array(&$this, 'post2pdf_conv_add_download_lnk'));
		add_filter('wp_head', array(&$this, 'post2pdf_conv_add_style'));
		if ($this->post2pdf_conv_setting_opt['shortcode'] == 1) {
			add_shortcode('pdf', array(&$this, 'post2pdf_conv_shortcode_handler'));
		}
	}

	// Create settings array
	function post2pdf_conv_setting_array() {
		$this->post2pdf_conv_setting_opt = array(
			"post" => 1,
			"page" => 1,
			"icon_size" => '16',
			"link_text" => __("Download this page in PDF format", "post2pdf_conv"),
			"link_text_size" => '12',
			"position" => 'before',
			"margin_top" => '10',
			"margin_bottom" => '10',
			"right_justify" => 1,
			"destination" => 'D',
			"access" => 'referrer',
			"shortcode" => 0,
			"lang" => 'eng',
			"file" => 'title',
			"font" => 'helvetica',
			"monospaced_font" => 'courier',
			"font_path" => 0,
			"font_size" => '10',
			"font_subsetting" => 1,
			"header" => 1,
			"image_ratio" => '1.25',
			"logo_enable" => 1,
			"logo_file" => 'tcpdf_logo.jpg',
			"logo_width" => '30',
			"wrap_title" => 0,
			"sig_enable" => 0,
			"sig_border" => 0,
			"sig_fill" => 0,
			"footer" => 1,
			"filters" => 1,
			"shortcode_handling" => 'parse',
			"add_to_font_family" => 0,
			);

		// Re-define setting value
		switch (WPLANG) {
			case 'ja';
				$this->post2pdf_conv_setting_opt['lang'] = "jpn";
				$this->post2pdf_conv_setting_opt['font'] = "cid0jp";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "cid0jp";
				break;
			case 'af':
				$this->post2pdf_conv_setting_opt['lang'] = "afr";
				break;
			case 'ar':
				$this->post2pdf_conv_setting_opt['lang'] = "ara";
				$this->post2pdf_conv_setting_opt['font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "aealarabiya";
				break;
			case 'azb_AZB':
				$this->post2pdf_conv_setting_opt['lang'] = "rtl";
				$this->post2pdf_conv_setting_opt['font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "aealarabiya";
				break;
			case 'azr_AZR':
				$this->post2pdf_conv_setting_opt['lang'] = "aze";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				break;
			case 'be_BY':
				$this->post2pdf_conv_setting_opt['lang'] = "bel";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				break;
			case 'bg_BG':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				break;
			case 'bs_BA':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				break;
			case 'pt_BR':
				$this->post2pdf_conv_setting_opt['lang'] = "bra";
				break;
			case 'ca':
				$this->post2pdf_conv_setting_opt['lang'] = "cat";
				break;
			case 'ckb':
				$this->post2pdf_conv_setting_opt['lang'] = "rtl";
				$this->post2pdf_conv_setting_opt['font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "aealarabiya";
				break;
			case 'cs_CZ':
				$this->post2pdf_conv_setting_opt['lang'] = "ces";
				break;
			case 'cy':
				$this->post2pdf_conv_setting_opt['lang'] = "cym";
				break;
			case 'da_DK':
				$this->post2pdf_conv_setting_opt['lang'] = "dan";
				break;
			case 'el':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				break;
			case 'es_CL':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'es_ES':
				$this->post2pdf_conv_setting_opt['lang'] = "spa";
				break;
			case 'es_PE':
				$this->post2pdf_conv_setting_opt['lang'] = "spa";
				break;
			case 'eo':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'et':
				$this->post2pdf_conv_setting_opt['lang'] = "est";
				break;
			case 'eu':
				$this->post2pdf_conv_setting_opt['lang'] = "eus";
				break;
			case 'fa_IR':
				$this->post2pdf_conv_setting_opt['lang'] = "far";
				$this->post2pdf_conv_setting_opt['font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "aealarabiya";
				break;
			case 'fi':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'fo':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'fr_FR':
				$this->post2pdf_conv_setting_opt['lang'] = "fra";
				break;
			case 'de_DE':
				$this->post2pdf_conv_setting_opt['lang'] = "ger";
				break;
			case 'ge_GE':
				$this->post2pdf_conv_setting_opt['lang'] = "kat";
				$this->post2pdf_conv_setting_opt['font'] = "freesans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "freemono";
				break;
			case 'gl_ES':
				$this->post2pdf_conv_setting_opt['lang'] = "glg";
				break;
			case 'he_IL':
				$this->post2pdf_conv_setting_opt['lang'] = "heb";
				$this->post2pdf_conv_setting_opt['font'] = "freesans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "freemono";
				break;
			case 'hr':
				$this->post2pdf_conv_setting_opt['lang'] = "hrv";
				break;
			case 'hu_HU':
				$this->post2pdf_conv_setting_opt['lang'] = "hun";
				break;
			case 'id_ID':
				$this->post2pdf_conv_setting_opt['lang'] = "ind";
				break;
			case 'is_IS':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'it_IT':
				$this->post2pdf_conv_setting_opt['lang'] = "ita";
				break;
			case 'kk':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				break;
			case 'kk_KZ':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				break;
			case 'kk-Cyrl':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				break;
			case 'kk_CN':
				$this->post2pdf_conv_setting_opt['lang'] = "rtl";
				$this->post2pdf_conv_setting_opt['font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "aealarabiya";
				break;
			case 'kk-Arab':
				$this->post2pdf_conv_setting_opt['lang'] = "rtl";
				$this->post2pdf_conv_setting_opt['font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "aealarabiya";
				break;
			case 'kk_TR':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'kk-Latn':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'ko_KR':
				$this->post2pdf_conv_setting_opt['lang'] = "kor";
				$this->post2pdf_conv_setting_opt['font'] = "cid0kr";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "cid0kr";
				break;
			case 'lt_LT':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'lv':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'mk_MK':
				$this->post2pdf_conv_setting_opt['lang'] = "mkd";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				break;
			case 'mg_MG':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'ms_MY':
				$this->post2pdf_conv_setting_opt['lang'] = "msa";
				break;
			case 'ni_ID':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'nb_NO':
				$this->post2pdf_conv_setting_opt['lang'] = "nob";
				break;
			case 'nl_NL':
				$this->post2pdf_conv_setting_opt['lang'] = "nld";
				break;
			case 'nn_NO':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'pl_PL':
				$this->post2pdf_conv_setting_opt['lang'] = "pol";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				break;
			case 'pt_PT':
				$this->post2pdf_conv_setting_opt['lang'] = "por";
				break;
			case 'ro_RO':
				$this->post2pdf_conv_setting_opt['lang'] = "ron";
				break;
			case 'ru_RU':
				$this->post2pdf_conv_setting_opt['lang'] = "rus";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				break;
			case 'sk_SK':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'sl_SI':
				$this->post2pdf_conv_setting_opt['lang'] = "slv";
				break;

			case 'su_ID':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'sq':
				$this->post2pdf_conv_setting_opt['lang'] = "sqi";
				break;
			case 'sr_RS':
				$this->post2pdf_conv_setting_opt['lang'] = "srp";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				break;
			case 'sv_SE':
				$this->post2pdf_conv_setting_opt['lang'] = "swe";
				break;
			case 'tg':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				break;
			case 'th':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "freesans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "freemono";
				break;
			case 'tr_TR':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'uk':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				break;
			case 'ug_CN':
				$this->post2pdf_conv_setting_opt['lang'] = "rtl";
				$this->post2pdf_conv_setting_opt['font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "aealarabiya";
				break;
			case 'uz_UZ':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'vi':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'zh_CN':
				$this->post2pdf_conv_setting_opt['lang'] = "chi";
				$this->post2pdf_conv_setting_opt['font'] = "cid0cs";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "cid0cs";
				break;
			case 'zh_TW':
				$this->post2pdf_conv_setting_opt['lang'] = "zho";
				$this->post2pdf_conv_setting_opt['font'] = "cid0ct";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "cid0ct";
				break;
			case 'zh_HK':
				$this->post2pdf_conv_setting_opt['lang'] = "zho";
				$this->post2pdf_conv_setting_opt['font'] = "cid0ct";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "cid0ct";
				break;
		}

		$this->post2pdf_conv_sig = "";

		// Store in DB
		add_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);
		add_option('post2pdf_conv_sig', $this->post2pdf_conv_sig);
		add_option('post2pdf_conv_updated', 'false');
	}

	// Check DB table version and create table
	function post2pdf_conv_check_db_ver() {
		$current_checkver_stamp = get_option('post2pdf_conv_checkver_stamp');
		if (!$current_checkver_stamp || version_compare($current_checkver_stamp, $this->post2pdf_conv_db_ver, "!=")) {
			$updated_count = 0;
			// For new installation
			if (!$current_checkver_stamp) {
				// Register array
				$this->post2pdf_conv_setting_array();

				$updated_count = $updated_count + 1;
			}
			// For update from ver.0.1
			if ($current_checkver_stamp && version_compare($current_checkver_stamp, "0.1", "<=")) {
				$this->post2pdf_conv_setting_opt['file'] = "title";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "courier";
				$this->post2pdf_conv_setting_opt['font_path'] = 0;
				$this->post2pdf_conv_setting_opt['add_to_font_family'] = 0;
				unset($this->post2pdf_conv_setting_opt['enable_font']);
				update_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);

				$updated_count = $updated_count + 1;
			}
			// For update from ver.0.1.3 or older
			if ($current_checkver_stamp && version_compare($current_checkver_stamp, "0.1.3", "<=")) {
				$this->post2pdf_conv_setting_opt['logo_enable'] = 1;
				$this->post2pdf_conv_setting_opt['logo_file'] = "tcpdf_logo.jpg";
				$this->post2pdf_conv_setting_opt['logo_width'] = "30";
				update_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);

				$updated_count = $updated_count + 1;
			}
			// For update from ver.0.1.5 or older
			if ($current_checkver_stamp && version_compare($current_checkver_stamp, "0.1.5", "<=")) {
				$this->post2pdf_conv_setting_opt['image_ratio'] = "1.25";
				update_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);

				$updated_count = $updated_count + 1;
			}
			// For update from ver.0.1.6 or older
			if ($current_checkver_stamp && version_compare($current_checkver_stamp, "0.1.6", "<=")) {
				$this->post2pdf_conv_setting_opt['shortcode'] = 0;
				$this->post2pdf_conv_setting_opt['header'] = 1;
				$this->post2pdf_conv_setting_opt['wrap_title'] = 0;
				$this->post2pdf_conv_setting_opt['sig_enable'] = 0;
				$this->post2pdf_conv_sig = "";
				$this->post2pdf_conv_setting_opt['sig_border'] = 0;
				$this->post2pdf_conv_setting_opt['sig_fill'] = 0;
				$this->post2pdf_conv_setting_opt['footer'] = 1;
				$this->post2pdf_conv_setting_opt['filters'] = 1;
				update_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);

				update_option('post2pdf_conv_sig', $this->post2pdf_conv_sig);

				$updated_count = $updated_count + 1;
			}
			update_option('post2pdf_conv_checkver_stamp', $this->post2pdf_conv_db_ver);
			// Stamp for showing messages
			if ($updated_count != 0) {
				update_option('post2pdf_conv_updated', 'true');
			}
		}
	}

	// Register the setting panel and hooks
	function post2pdf_conv_register_menu_item() {
		$post2pdf_conv_page_hook = add_options_page('POST2PDF Converter Options', 'POST2PDF Converter', 'manage_options', 'post2pdf-converter-options', array(&$this, 'post2pdf_conv_options_panel'));
		if ($post2pdf_conv_page_hook != null) {
			$post2pdf_conv_page_hook = '-'.$post2pdf_conv_page_hook;
		}
		add_action('admin_print_scripts'.$post2pdf_conv_page_hook, array(&$this, 'post2pdf_conv_load_jscript_for_admin'));
		if (get_option('post2pdf_conv_updated') == "true" && !(isset($_POST['POST2PDF_Converter_Setting_Submit']) && $_POST['post2pdf_conv_hidden_value'] == "true") && !(isset($_POST['POST2PDF_Converter_Reset']) && $_POST['post2pdf_conv_reset'] == "true")) {
			add_action('admin_notices', array(&$this, 'post2pdf_conv_admin_updated_notice'));
		}
	}

	// Message for admin when DB table updated
	function post2pdf_conv_admin_updated_notice(){
		echo '<div id="message" class="updated"><p>'.__("POST2PDF Converter has successfully created new DB table.<br />If you upgraded to this version, some setting options may be added or reset to the default values.<br />Go to the <a href=\"options-general.php?page=post2pdf-converter-options\">setting panel</a> and configure POST2PDF Converter now. Once you save your settings, this message will be cleared.", "post2pdf_conv").'</p></div>';
	}

	// Show plugin info in the footer
	function post2pdf_conv_add_admin_footer() {
		$post2pdf_conv_plugin_data = get_plugin_data(__FILE__);
		printf('%1$s by %2$s<br />', $post2pdf_conv_plugin_data['Title'].' '.$post2pdf_conv_plugin_data['Version'], $post2pdf_conv_plugin_data['Author']);
	}

	// Register the setting panel
	function post2pdf_conv_setting_link($links, $file) {
		static $this_plugin;
		if (! $this_plugin) $this_plugin = plugin_basename(__FILE__);
		if ($file == $this_plugin){
			$settings_link = '<a href="options-general.php?page=post2pdf-converter-options">'.__("Settings", "post2pdf_conv").'</a>';
			array_unshift($links, $settings_link);
		}  
		return $links;
	}

	// Load script in setting panel
	function post2pdf_conv_load_jscript_for_admin() {
		wp_enqueue_script('rc_admin_js', $this->post2pdf_conv_plugin_url.'rc-admin-js.js', false, '1.1');
	}

	function post2pdf_conv_add_download_lnk($content) {
		global $post;

		if ($this->post2pdf_conv_setting_opt['icon_size'] == "none") {
			$link = '<div id="downloadpdf"><a href="'.$this->post2pdf_conv_plugin_url.'post2pdf-converter-pdf-maker.php?id='.$post->ID.'">'.$this->post2pdf_conv_setting_opt['link_text'].'</a></div>';
		} else {
			$link = '<div id="downloadpdf"><a href="'.$this->post2pdf_conv_plugin_url.'post2pdf-converter-pdf-maker.php?id='.$post->ID.'"><img src="'.$this->post2pdf_conv_plugin_url.'img/pdf'.$this->post2pdf_conv_setting_opt['icon_size'].'.png" alt="download as a pdf file" /> '.$this->post2pdf_conv_setting_opt['link_text'].'</a></div>';
		}

		if ($this->post2pdf_conv_setting_opt['access'] == "login" && !is_user_logged_in()) {
			$link = "";
		}

		if (($this->post2pdf_conv_setting_opt['post'] == 1 && is_single()) || ($this->post2pdf_conv_setting_opt['page'] == 1 && is_page())) {
			if ($this->post2pdf_conv_setting_opt['position'] == "before") {
				return $link.$content;
			} else if ($this->post2pdf_conv_setting_opt['position'] == "after") {
				return $content.$link;
			} else if ($this->post2pdf_conv_setting_opt['position'] == "both") {
				return $link.$content.$link;
			} else {
				return $content;
			}
		} else {
			return $content;
		}
	}

	function post2pdf_conv_add_style() {
		global $post;

		if ($this->post2pdf_conv_setting_opt['right_justify'] == 1) {
			$aligin = "text-align: right; ";
		} else {
			$aligin = "";
		}
		$margin_top = $this->post2pdf_conv_setting_opt['margin_top'];
		if ($margin_top == "") {
			$margin_top = "0";
		}
		$margin_bottom = $this->post2pdf_conv_setting_opt['margin_bottom'];
		if ($margin_bottom == "") {
			$margin_bottom = "0";
		}
		$css = "\n<!-- POST2PDF Converter CSS Begin -->
<style type='text/css'>
#downloadpdf {".$aligin."font-size: ".$this->post2pdf_conv_setting_opt['link_text_size']."px; margin: ".$margin_top."px 0px ".$margin_top."px 0px;}
#downloadpdf a {text-decoration: none;}
</style>
<!-- POST2PDF Converter CSS End -->\n";

		if (!($this->post2pdf_conv_setting_opt['access'] == "login" && !is_user_logged_in())) {
			if (($this->post2pdf_conv_setting_opt['post'] == 1 && is_single()) || ($this->post2pdf_conv_setting_opt['page'] == 1 && is_page())) {
				echo $css;
			} else {
				return;
			}
		}
	}

	// Shortcode handler
	function post2pdf_conv_shortcode_handler($atts, $content = null) {
		global $post;

		extract(shortcode_atts(array(
			'id' => $post->ID,
			'lang' => $this->post2pdf_conv_setting_opt['lang'],
			'file' => $this->post2pdf_conv_setting_opt['file'],
			'font' => $this->post2pdf_conv_setting_opt['font'],
			'monospaced' => $this->post2pdf_conv_setting_opt['monospaced_font'],
			'fontsize' => $this->post2pdf_conv_setting_opt['font_size'],
			'subsetting' => $this->post2pdf_conv_setting_opt['font_subsetting'],
			'ratio' => $this->post2pdf_conv_setting_opt['image_ratio'],
			'header' => $this->post2pdf_conv_setting_opt['header'],
			'logo' => $this->post2pdf_conv_setting_opt['logo_enable'],
			'logo_file' => $this->post2pdf_conv_setting_opt['logo_file'],
			'logo_width' => $this->post2pdf_conv_setting_opt['logo_width'],
			'wrap_title' => $this->post2pdf_conv_setting_opt['wrap_title'],
			'footer' => $this->post2pdf_conv_setting_opt['header'],
			'filters' => $this->post2pdf_conv_setting_opt['filters'],
			'shortcode' => $this->post2pdf_conv_setting_opt['shortcode_handling'],
			'ffamily' => $this->post2pdf_conv_setting_opt['add_to_font_family'],
		), $atts));

		$id = intval($id);
		$lang = esc_html($lang);
		$file = esc_html($file);
		if ($file != "title" && $file != "id") {
			$file = $this->post2pdf_conv_setting_opt['file'];
		}
		$font = esc_html($font);
		$monospaced = esc_html($monospaced);
		$fontsize = intval($fontsize);
		if ($subsetting != 1 && $subsetting != 0) {
			$subsetting = $this->post2pdf_conv_setting_opt['font_subsetting'];
		}
		$ratio = floatval($ratio);
		if ($header != 1 && $header != 0) {
			$header = $this->post2pdf_conv_setting_opt['header'];
		}
		if ($logo != 1 && $logo != 0) {
			$logo = $this->post2pdf_conv_setting_opt['logo_enable'];
		}
		$logo_file = esc_html($logo_file);
		$logo_width = intval($logo_width);
		if ($wrap_title != 1 && $wrap_title != 0) {
			$wrap_title = $this->post2pdf_conv_setting_opt['wrap_title'];
		}
		if ($footer != 1 && $footer != 0) {
			$footer = $this->post2pdf_conv_setting_opt['footer'];
		}
		if ($filters != 1 && $filters != 0) {
			$filters = $this->post2pdf_conv_setting_opt['filters'];
		}
		$shortcode = esc_html($shortcode);
		if ($ffamily != 1 && $ffamily != 0) {
			$ffamily = $this->post2pdf_conv_setting_opt['add_to_font_family'];
		}

		return '<a href="'.$this->post2pdf_conv_plugin_url.'post2pdf-converter-pdf-maker.php?id='.$id.'&file='.$file.'&font='.$font.'&monospaced='.$monospaced.'&fontsize='.$fontsize.'&subsetting='.$subsetting.'&ratio='.$ratio.'&header='.$header.'&wrap_title='.$wrap_title.'&logo='.$logo.'&logo_file='.$logo_file.'&logo_width='.$logo_width.'&footer='.$footer.'&filters='.$filters.'&shortcode='.$shortcode.'&ffamily='.$ffamily.'">'.$content.'</a>';
	}

	// Validate free style text data
	function post2pdf_conv_valid_text($text, $level) {
		global $allowedposttags, $allowedtags;

		if ($level == "0") {
			return $text;
		} elseif ($level == "1") {
			if (preg_match("/<meta[^>]*?>/is", $text) ||
			preg_match("/<title[^>]*?>/is", $text) ||
			preg_match("/<plaintext[^>]*?>/is", $text) ||
			preg_match("/<marquee[^>]*?>/is", $text) ||
			preg_match("/<isindex[^>]*?>/is", $text) ||
			preg_match("/<xmp[^>]*?>/is", $text) ||
			preg_match("/<listing[^>]*?>/is", $text)) {
				return "invalid";
			} else {
				return $text;
			}
		} elseif ($level == "2") {
			if (preg_match("/<script[^>]*?>/is", $text) ||
			preg_match("/<input[^>]*?>/is", $text) ||
			preg_match("/<textarea[^>]*?>/is", $text) ||
			preg_match("/<\/textarea>/is", $text) ||
			preg_match("/<object[^>]*?>/is", $text) ||
			preg_match("/<applet[^>]*?>/is", $text) ||
			preg_match("/<embed[^>]*?>/i", $text) ||
			preg_match("/<table[^>]*?>/is", $text) ||
			preg_match("/<form[^>]*?>/is", $text) ||
			preg_match("/<meta[^>]*?>/is", $text) ||
			preg_match("/<title[^>]*?>/is", $text) ||
			preg_match("/<frame[^>]*?>/is", $text) ||
			preg_match("/<plaintext[^>]*?>/is", $text) ||
			preg_match("/<marquee[^>]*?>/is", $text) ||
			preg_match("/<isindex[^>]*?>/is", $text) ||
			preg_match("/<xmp[^>]*?>/is", $text) ||
			preg_match("/<listing[^>]*?>/is", $text) ||
			preg_match("/<body[^>]*?>/is", $text) ||
			preg_match("/<style[^>]*?>/is", $text) ||
			preg_match("/<link[^>]*?>/is", $text) ||
			preg_match("/on.{4,}?=/is", $text) ||
			preg_match("/background[^=]*?=/is", $text) ||
			preg_match("/(http|https):\/\/.*?\.js/is", $text)) {
				return "invalid";
			} else {
				return $text;
			}
		} elseif ($level == "3") {
			$filtered_text = wp_kses($text, $allowedposttags);
			if ($text != $filtered_text) {
				return "invalid";
			} else {
				return $filtered_text;
			}
		} elseif ($level == "4") {
			$filtered_text = wp_kses($text, $allowedtags);
			if ($text != $filtered_text) {
				return "invalid";
			} else {
				return $filtered_text;
			}
		} elseif ($level == "5") {
			$text = esc_html($text);
			return $text;
		} else {
			return $text;
		}
	}

	// Setting panel
	function post2pdf_conv_options_panel(){
		if(!function_exists('current_user_can') || !current_user_can('manage_options')){
			die(__('Cheatin&#8217; uh?'));
		} 
		add_action('in_admin_footer', array(&$this, 'post2pdf_conv_add_admin_footer'));

		// Update setting options
		if (isset($_POST['POST2PDF_Converter_Setting_Submit']) && $_POST['post2pdf_conv_hidden_value'] == "true" && check_admin_referer("post2pdf_conv_update_options", "_wpnonce_update_options")) {
			if ($_POST['post'] == 1) {
				$this->post2pdf_conv_setting_opt['post'] = 1;
			} else {
				$this->post2pdf_conv_setting_opt['post'] = 0;
			}
			if ($_POST['page'] == 1) {
				$this->post2pdf_conv_setting_opt['page'] = 1;
			} else {
				$this->post2pdf_conv_setting_opt['page'] = 0;
			}
			$this->post2pdf_conv_setting_opt['icon_size'] = $_POST['icon_size'];
			$this->post2pdf_conv_setting_opt['link_text'] = stripslashes($_POST['link_text']);
			$this->post2pdf_conv_setting_opt['link_text_size'] = $_POST['link_text_size'];
			$this->post2pdf_conv_setting_opt['position'] = $_POST['position'];
			$this->post2pdf_conv_setting_opt['margin_top'] = stripslashes($_POST['margin_top']);
			$this->post2pdf_conv_setting_opt['margin_bottom'] = stripslashes($_POST['margin_bottom']);
			if ($_POST['right_justify'] == 1) {
				$this->post2pdf_conv_setting_opt['right_justify'] = 1;
			} else {
				$this->post2pdf_conv_setting_opt['right_justify'] = 0;
			}
			$this->post2pdf_conv_setting_opt['destination'] = $_POST['destination'];
			$this->post2pdf_conv_setting_opt['access'] = $_POST['access'];
			if ($_POST['shortcode'] == 1) {
				$this->post2pdf_conv_setting_opt['shortcode'] = 1;
			} else {
				$this->post2pdf_conv_setting_opt['shortcode'] = 0;
			}
			$this->post2pdf_conv_setting_opt['lang'] = $_POST['lang'];
			$this->post2pdf_conv_setting_opt['file'] = $_POST['file'];
			$this->post2pdf_conv_setting_opt['font'] = stripslashes($_POST['font']);
			$this->post2pdf_conv_setting_opt['monospaced_font'] = stripslashes($_POST['monospaced_font']);
			if ($_POST['font_path'] == 1) {
				$this->post2pdf_conv_setting_opt['font_path'] = 1;
			} else {
				$this->post2pdf_conv_setting_opt['font_path'] = 0;
			}
			$this->post2pdf_conv_setting_opt['font_size'] = $_POST['font_size'];
			if ($_POST['font_subsetting'] == 1) {
				$this->post2pdf_conv_setting_opt['font_subsetting'] = 1;
			} else {
				$this->post2pdf_conv_setting_opt['font_subsetting'] = 0;
			}
			$this->post2pdf_conv_setting_opt['image_ratio'] = $_POST['image_ratio'];
			if ($_POST['header'] == 1) {
				$this->post2pdf_conv_setting_opt['header'] = 1;
			} else {
				$this->post2pdf_conv_setting_opt['header'] = 0;
			}
			if ($_POST['logo_enable'] == 1) {
				$this->post2pdf_conv_setting_opt['logo_enable'] = 1;
			} else {
				$this->post2pdf_conv_setting_opt['logo_enable'] = 0;
			}
			$this->post2pdf_conv_setting_opt['logo_file'] = stripslashes($_POST['logo_file']);
			$this->post2pdf_conv_setting_opt['logo_width'] = stripslashes($_POST['logo_width']);
			if ($_POST['wrap_title'] == 1) {
				$this->post2pdf_conv_setting_opt['wrap_title'] = 1;
			} else {
				$this->post2pdf_conv_setting_opt['wrap_title'] = 0;
			}
			if ($_POST['sig_enable'] == 1) {
				$this->post2pdf_conv_setting_opt['sig_enable'] = 1;
			} else {
				$this->post2pdf_conv_setting_opt['sig_enable'] = 0;
			}
			$this->post2pdf_conv_sig = stripslashes($_POST['post2pdf_conv_sig']);
			if ($_POST['sig_border'] == 1) {
				$this->post2pdf_conv_setting_opt['sig_border'] = 1;
			} else {
				$this->post2pdf_conv_setting_opt['sig_border'] = 0;
			}
			if ($_POST['sig_fill'] == 1) {
				$this->post2pdf_conv_setting_opt['sig_fill'] = 1;
			} else {
				$this->post2pdf_conv_setting_opt['sig_fill'] = 0;
			}
			if ($_POST['footer'] == 1) {
				$this->post2pdf_conv_setting_opt['footer'] = 1;
			} else {
				$this->post2pdf_conv_setting_opt['footer'] = 0;
			}
			if ($_POST['filters'] == 1) {
				$this->post2pdf_conv_setting_opt['filters'] = 1;
			} else {
				$this->post2pdf_conv_setting_opt['filters'] = 0;
			}
			$this->post2pdf_conv_setting_opt['shortcode_handling'] = $_POST['shortcode_handling'];
			if ($_POST['add_to_font_family'] == 1) {
				$this->post2pdf_conv_setting_opt['add_to_font_family'] = 1;
			} else {
				$this->post2pdf_conv_setting_opt['add_to_font_family'] = 0;
			}
			// Transforming
			$this->post2pdf_conv_setting_opt['link_text'] = strip_tags($this->post2pdf_conv_setting_opt['link_text']);
			$this->post2pdf_conv_setting_opt['margin_top']  = intval($this->post2pdf_conv_setting_opt['margin_top']);
			$this->post2pdf_conv_setting_opt['margin_bottom']  = intval($this->post2pdf_conv_setting_opt['margin_bottom']);
			$this->post2pdf_conv_setting_opt['font'] = strip_tags($this->post2pdf_conv_setting_opt['font']);
			if (preg_match('/^[^\.].+?\.(jpg|jpeg|png|gif)$/i', $this->post2pdf_conv_setting_opt['logo_file'])) {
				$this->post2pdf_conv_setting_opt['logo_file'] = $this->post2pdf_conv_setting_opt['logo_file'];
			} else {
				$this->post2pdf_conv_setting_opt['logo_file'] = "";
			}
			$this->post2pdf_conv_setting_opt['logo_width']  = intval($this->post2pdf_conv_setting_opt['logo_width']);
			$this->post2pdf_conv_sig = str_replace(array("\r\n", "\r", "\n"), "<br />", $this->post2pdf_conv_sig);
			// Validate values
			if ($this->post2pdf_conv_valid_text($this->post2pdf_conv_sig, $this->post2pdf_conv_allowed_str) == "invalid") {
				wp_die(__('Invalid value. Settings could not be saved.<br />Your "Signature" contains some character strings that are not allowed to use.', 'post2pdf_conv'));
			} else {
				$this->post2pdf_conv_sig = $this->post2pdf_conv_valid_text($this->post2pdf_conv_sig, $this->post2pdf_conv_allowed_str);
			}
			// Store in DB
			update_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);
			update_option('post2pdf_conv_sig', $this->post2pdf_conv_sig);
			update_option('post2pdf_conv_updated', 'false');
			// Show message for admin
			echo "<div id='setting-error-settings_updated' class='updated fade'><p><strong>".__("Settings saved.","post2pdf_conv")."</strong></p></div>";
		}
		// Reset all settings
		if (isset($_POST['POST2PDF_Converter_Reset']) && $_POST['post2pdf_conv_reset'] == "true" && check_admin_referer("post2pdf_conv_reset_options", "_wpnonce_reset_options")) {
			include_once('uninstall.php');
			$this->post2pdf_conv_setting_array();
			update_option('post2pdf_conv_checkver_stamp', $this->post2pdf_conv_db_ver);
			// Show message for admin
			echo "<div id='setting-error-settings_updated' class='updated fade'><p><strong>".__("All settings were reset. Please <a href=\"options-general.php?page=post2pdf-converter-options\">reload the page</a>.", "post2pdf_conv")."</strong></p></div>";
		}

		// Define language array to select config file and font
		$languages = array(
			"afr" => __("Afrikaans", "post2pdf_conv"),
			"sqi" => __("Albanian", "post2pdf_conv"),
			"ara" => __("Arabic", "post2pdf_conv"),
			"aze" => __("Azerbaijanian", "post2pdf_conv"),
			"eus" => __("Basque", "post2pdf_conv"),
			"bel" => __("Belarusian", "post2pdf_conv"),
			"bra" => __("Portuguese(Brazil)", "post2pdf_conv"),
			"cat" => __("Catalan", "post2pdf_conv"),
			"chi" => __("Chinese(Simplified)", "post2pdf_conv"),
			"zho" => __("Chinese(Traditional)", "post2pdf_conv"),
			"hrv" => __("Croatian", "post2pdf_conv"),
			"ces" => __("Czech", "post2pdf_conv"),
			"dan" => __("Danish", "post2pdf_conv"),
			"nld" => __("Dutch", "post2pdf_conv"),
			"eng" => __("English", "post2pdf_conv"),
			"est" => __("Estonian", "post2pdf_conv"),
			"far" => __("Farsi", "post2pdf_conv"),
			"fra" => __("French", "post2pdf_conv"),
			"ger" => __("German", "post2pdf_conv"),
			"gle" => __("Irish", "post2pdf_conv"),
			"glg" => __("Galician", "post2pdf_conv"),
			"kat" => __("Georgian", "post2pdf_conv"),
			"hat" => __("Haitian Creole", "post2pdf_conv"),
			"heb" => __("Hebrew", "post2pdf_conv"),
			"hun" => __("Hungarian", "post2pdf_conv"),
			"hye" => __("Armenian", "post2pdf_conv"),
			"ind" => __("Indonesian", "post2pdf_conv"),
			"ita" => __("Italian", "post2pdf_conv"),
			"jpn" => __("Japanese", "post2pdf_conv"),
			"kor" => __("Korean", "post2pdf_conv"),
			"mkd" => __("Macedonian", "post2pdf_conv"),
			"msa" => __("Malay", "post2pdf_conv"),
			"mlt" => __("Maltese", "post2pdf_conv"),
			"ron2" => __("Moldavian", "post2pdf_conv"),
			"ron3" => __("Moldovan", "post2pdf_conv"),
			"nob" => __("Norwegian Bokmål", "post2pdf_conv"),
			"pol" => __("Polish", "post2pdf_conv"),
			"por" => __("Portuguese", "post2pdf_conv"),
			"ron1" => __("Romanian", "post2pdf_conv"),
			"rus" => __("Russian", "post2pdf_conv"),
			"srp" => __("Serbian", "post2pdf_conv"),
			"slv" => __("Slovenian", "post2pdf_conv"),
			"spa" => __("Spanish", "post2pdf_conv"),
			"swa" => __("Swahili", "post2pdf_conv"),
			"swe" => __("Swedish", "post2pdf_conv"),
			"urd" => __("Urdu", "post2pdf_conv"),
			"cym" => __("Welsh", "post2pdf_conv"),
			"yid" => __("Yiddish", "post2pdf_conv"),
			"ltr" => __("Other(Text direction: LTR)", "post2pdf_conv"),
			"rtl" => __("Other(Text direction: RTL)", "post2pdf_conv"),
			);

	?> 
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2>POST2PDF Converter</h2>
	<form method="post" action="">
	<?php wp_nonce_field("post2pdf_conv_update_options", "_wpnonce_update_options"); ?>
	<input type="hidden" name="post2pdf_conv_hidden_value" value="true" />
	<h3><?php _e("1. Download Link Settings", "post2pdf_conv") ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Add download link to', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="post" value="1" <?php if($this->post2pdf_conv_setting_opt['post'] == 1){echo 'checked="checked" ';} ?>/><?php _e("Posts", "post2pdf_conv") ?> <input type="checkbox" name="page" value="1" <?php if($this->post2pdf_conv_setting_opt['page'] == 1){echo 'checked="checked" ';} ?>/><?php _e("Pages", "post2pdf_conv") ?><br />
					<p><small><?php _e("Put a download link on the posts/pages.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Icon size", "post2pdf_conv") ?></th> 
				<td>
					<select name="icon_size">
						<option value="16" <?php if ($this->post2pdf_conv_setting_opt['icon_size'] == "16") {echo 'selected="selected"';} ?>>16 px</option>
						<option value="22" <?php if ($this->post2pdf_conv_setting_opt['icon_size'] == "22") {echo 'selected="selected"';} ?>>22 px</option>
						<option value="32" <?php if ($this->post2pdf_conv_setting_opt['icon_size'] == "32") {echo 'selected="selected"';} ?>>32 px</option>
						<option value="48" <?php if ($this->post2pdf_conv_setting_opt['icon_size'] == "48") {echo 'selected="selected"';} ?>>48 px</option>
						<option value="64" <?php if ($this->post2pdf_conv_setting_opt['icon_size'] == "64") {echo 'selected="selected"';} ?>>64 px</option>
						<option value="128" <?php if ($this->post2pdf_conv_setting_opt['icon_size'] == "128") {echo 'selected="selected"';} ?>>128 px</option>
						<option value="none" <?php if ($this->post2pdf_conv_setting_opt['icon_size'] == "none") {echo 'selected="selected"';} ?>>None</option>
					</select>
					<p><small><?php _e("Choose PDF icon size.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Link text', 'post2pdf_conv') ?></th>
				<td>
					<input type="text" name="link_text" size="80" value="<?php echo esc_html($this->post2pdf_conv_setting_opt['link_text']); ?>" /><br />
					<p><small><?php _e("Enter text for the download link.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Font size", "post2pdf_conv") ?></th> 
				<td>
					<select name="link_text_size">
						<option value="6" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "6") {echo 'selected="selected"';} ?>>6 px</option>
						<option value="7" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "7") {echo 'selected="selected"';} ?>>7 px</option>
						<option value="8" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "8") {echo 'selected="selected"';} ?>>8 px</option>
						<option value="9" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "9") {echo 'selected="selected"';} ?>>9 px</option>
						<option value="10" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "10") {echo 'selected="selected"';} ?>>10 px</option>
						<option value="11" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "11") {echo 'selected="selected"';} ?>>11 px</option>
						<option value="12" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "12") {echo 'selected="selected"';} ?>>12 px</option>
						<option value="13" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "13") {echo 'selected="selected"';} ?>>13 px</option>
						<option value="14" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "14") {echo 'selected="selected"';} ?>>14 px</option>
						<option value="15" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "15") {echo 'selected="selected"';} ?>>15 px</option>
						<option value="16" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "16") {echo 'selected="selected"';} ?>>16 px</option>
						<option value="17" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "17") {echo 'selected="selected"';} ?>>17 px</option>
						<option value="18" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "18") {echo 'selected="selected"';} ?>>18 px</option>
					</select>
					<p><small><?php _e("Choose font size for the link text.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Position", "post2pdf_conv") ?></th> 
				<td>
					<select name="position">
						<option value="before" <?php if ($this->post2pdf_conv_setting_opt['position'] == "before") {echo 'selected="selected"';} ?>><?php _e("Before the post/page content block", "post2pdf_conv") ?></option>
						<option value="after" <?php if ($this->post2pdf_conv_setting_opt['position'] == "after") {echo 'selected="selected"';} ?>><?php _e("After the post/page content block", "post2pdf_conv") ?></option>
						<option value="both" <?php if ($this->post2pdf_conv_setting_opt['position'] == "both") {echo 'selected="selected"';} ?>><?php _e("Before and After the post/page content block", "post2pdf_conv") ?></option>
					</select>
					<p><small><?php _e("Choose display position of the download link.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Margin', 'post2pdf_conv') ?></th>
				<td>
					<?php _e("Top margin", "post2pdf_conv") ?> <input type="text" name="margin_top" size="2" value="<?php echo esc_html($this->post2pdf_conv_setting_opt['margin_top']); ?>" /><?php _e("px", "post2pdf_conv") ?> <?php _e("Bottom margin", "post2pdf_conv") ?> <input type="text" name="margin_bottom" size="2" value="<?php echo esc_html($this->post2pdf_conv_setting_opt['margin_bottom']); ?>" /><?php _e("px", "post2pdf_conv") ?><br />
					<p><small><?php _e("Set margin for the download link block.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Right justification', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="right_justify" value="1" <?php if($this->post2pdf_conv_setting_opt['right_justify'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("Justify the download link block to the right.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Shortcode', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="shortcode" value="1" <?php if($this->post2pdf_conv_setting_opt['shortcode'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("You can insert the download link into your posts/pages using 'Shortcode'.<br />e.g.: <code>[pdf]Click here to get a PDF[/pdf]</code>", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Link action", "post2pdf_conv") ?></th> 
				<td>
					<select name="destination">
						<option value="D" <?php if ($this->post2pdf_conv_setting_opt['destination'] == "D") {echo 'selected="selected"';} ?>><?php _e("Download", "post2pdf_conv") ?></option>
						<option value="I" <?php if ($this->post2pdf_conv_setting_opt['destination'] == "I") {echo 'selected="selected"';} ?>><?php _e("Open with the browser", "post2pdf_conv") ?></option>
					</select>
					<p><small><?php _e("When 'Open with the browser' is chosen, This plugin will output PDF to the browser.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Access", "post2pdf_conv") ?></th> 
				<td>
					<select name="access">
						<option value="unrestraint" <?php if ($this->post2pdf_conv_setting_opt['access'] == "unrestraint") {echo 'selected="selected"';} ?>><?php _e("Unrestraint", "post2pdf_conv") ?></option>
						<option value="referrer" <?php if ($this->post2pdf_conv_setting_opt['access'] == "referrer") {echo 'selected="selected"';} ?>><?php _e("Deny any access with the download URL directly", "post2pdf_conv") ?></option>
						<option value="login" <?php if ($this->post2pdf_conv_setting_opt['access'] == "login") {echo 'selected="selected"';} ?>><?php _e("Allow only registered users", "post2pdf_conv") ?></option>
					</select>
					<p><small><?php _e("You can restrict access to the download link.<br/>When 'Deny any access with the download URL directly' is chosen, HTTP referrer must contain your WordPress URL.<br />When 'Allow only registered users' is chosen, Only registered users can download a PDF.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
		</table>
	<h3><?php _e("2. PDF Settings", 'post2pdf_conv') ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e("Language", "post2pdf_conv") ?></th> 
				<td>
					<select name="lang">
					<?php foreach ($languages as $lang => $lang_name) { ?>
						<option value="<?php echo $lang ?>" <?php if ($this->post2pdf_conv_setting_opt['lang'] == $lang) {echo 'selected="selected"';} ?>><?php echo $lang_name ?></option>
					<?php } ?>
					</select>
					<p><small><?php _e("Choose content language.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("File name", "post2pdf_conv") ?></th> 
				<td>
					<select name="file">
						<option value="title" <?php if ($this->post2pdf_conv_setting_opt['file'] == "title") {echo 'selected="selected"';} ?>><?php _e("Title", "post2pdf_conv") ?></option>
						<option value="id" <?php if ($this->post2pdf_conv_setting_opt['file'] == "id") {echo 'selected="selected"';} ?>><?php _e("Post id", "post2pdf_conv") ?></option>
					</select>
					<p><small><?php _e("Define PDF file name.<br />You can choose title-based filename or post id-based filename.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Font', 'post2pdf_conv') ?></th>
				<td>
					<input type="text" name="font" value="<?php echo esc_html($this->post2pdf_conv_setting_opt['font']); ?>" /><br />
					<p><small><?php _e("This plugin choose the best suited font and set it as the default font automatically.<br />You can also enter another font name to change font.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Monospaced font', 'post2pdf_conv') ?></th>
				<td>
					<input type="text" name="monospaced_font" value="<?php echo esc_html($this->post2pdf_conv_setting_opt['monospaced_font']); ?>" /><br />
					<p><small><?php _e("Set default monospaced font.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Safe fonts directory', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="font_path" value="1" <?php if($this->post2pdf_conv_setting_opt['font_path'] == 1){echo 'checked="checked" ';} ?>/><?php _e("Place font files in /wp-content/tcpdf-fonts", "post2pdf_conv") ?><br />
					<p><small><?php _e("This plugin allow you to place font files in /wp-content/tcpdf-fonts.<br />Once you enable this option, after automatic updating, your fonts will never be removed.<br />If you adds your own fonts, you should enable this option.<br />Note: Before this option is enabled, you must create /wp-content/tcpdf-fonts/ directory manually.<br />Next, upload/move fonts to new directory and enable this option.<br />Orignal fonts: Unzip a plugin zip file and you can find orignal fonts in /post2pdf-converter/tcpdf/fonts.<br />Original fonts directory: /YOUR PLUGIN DIRECTORY/post2pdf-converter/tcpdf/fonts<br />Now you can remove original fonts directory.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Font size", "post2pdf_conv") ?></th> 
				<td>
					<select name="font_size">
						<option value="6" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "6") {echo 'selected="selected"';} ?>>6 px</option>
						<option value="7" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "7") {echo 'selected="selected"';} ?>>7 px</option>
						<option value="8" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "8") {echo 'selected="selected"';} ?>>8 px</option>
						<option value="9" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "9") {echo 'selected="selected"';} ?>>9 px</option>
						<option value="10" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "10") {echo 'selected="selected"';} ?>>10 px</option>
						<option value="11" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "11") {echo 'selected="selected"';} ?>>11 px</option>
						<option value="12" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "12") {echo 'selected="selected"';} ?>>12 px</option>
						<option value="13" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "13") {echo 'selected="selected"';} ?>>13 px</option>
						<option value="14" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "14") {echo 'selected="selected"';} ?>>14 px</option>
						<option value="15" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "15") {echo 'selected="selected"';} ?>>15 px</option>
						<option value="16" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "16") {echo 'selected="selected"';} ?>>16 px</option>
						<option value="17" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "17") {echo 'selected="selected"';} ?>>17 px</option>
						<option value="18" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "18") {echo 'selected="selected"';} ?>>18 px</option>
					</select>
					<p><small><?php _e("Choose font size.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Font subsetting', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="font_subsetting" value="1" <?php if($this->post2pdf_conv_setting_opt['font_subsetting'] == 1){echo 'checked="checked" ';} ?>/><?php _e("enable", "post2pdf_conv") ?><br />
					<p><small><?php _e("Enable/Disable 'Font subsetting' to reduce the size of documents using large unicode font files.<br />If this option is diseabled, the whole font will be embeded in the PDF file and file size will become enlarged.<br />Note: there are some fonts that can not be embedded.(e.g. Courier, Helvetica, Times New Roman etc.)", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Image ratio", "post2pdf_conv") ?></th> 
				<td>
					<select name="image_ratio">
						<option value="1.0" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.0") {echo 'selected="selected"';} ?>>1.0</option>
						<option value="1.05" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.05") {echo 'selected="selected"';} ?>>1.05</option>
						<option value="1.1" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.1") {echo 'selected="selected"';} ?>>1.1</option>
						<option value="1.15" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.15") {echo 'selected="selected"';} ?>>1.15</option>
						<option value="1.2" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.2") {echo 'selected="selected"';} ?>>1.2</option>
						<option value="1.25" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.25") {echo 'selected="selected"';} ?>>1.25</option>
						<option value="1.3" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.3") {echo 'selected="selected"';} ?>>1.3</option>
						<option value="1.35" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.35") {echo 'selected="selected"';} ?>>1.35</option>
						<option value="1.4" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.4") {echo 'selected="selected"';} ?>>1.4</option>
						<option value="1.45" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.45") {echo 'selected="selected"';} ?>>1.45</option>
						<option value="1.5" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.5") {echo 'selected="selected"';} ?>>1.5</option>
					</select>
					<p><small><?php _e("Set image ratio.<br />Note: With increasing numerical value, you will get smaller images.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Header', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="header" value="1" <?php if($this->post2pdf_conv_setting_opt['header'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("Show/Hide header.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Header logo', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="logo_enable" value="1" <?php if($this->post2pdf_conv_setting_opt['logo_enable'] == 1){echo 'checked="checked" ';} ?>/><?php _e("Show a logo on the header", "post2pdf_conv") ?><br /><?php _e("Image file", "post2pdf_conv") ?> <input type="text" name="logo_file" size="15" value="<?php echo esc_html($this->post2pdf_conv_setting_opt['logo_file']); ?>" /> <?php _e("Logo width", "post2pdf_conv") ?> <input type="text" name="logo_width" size="2" value="<?php echo esc_html($this->post2pdf_conv_setting_opt['logo_width']); ?>" />mm<br />
					<p><small><?php _e("You can set your own logo on the header.<br />First, create /wp-content/tcpdf-images/ directory manually.<br />Next, upload iamge file(jpeg, jpg, png or gif) there and enter your image file name.<br />You can also upload your image to /YOUR PLUGIN DIRECTORY/post2pdf-converter/tcpdf/images directory.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Wrap title', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="wrap_title" value="1" <?php if($this->post2pdf_conv_setting_opt['wrap_title'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("If title is too long, wrap the title on a new line.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Signature', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="sig_enable" value="1" <?php if($this->post2pdf_conv_setting_opt['sig_enable'] == 1){echo 'checked="checked" ';} ?>/> <?php _e('Add signature', 'post2pdf_conv') ?><br />
					<textarea name="post2pdf_conv_sig" rows="8" style="width:500px"><?php echo esc_html($this->post2pdf_conv_valid_text($this->post2pdf_conv_sig, $this->post2pdf_conv_allowed_str)); ?></textarea><br />
					<input type="checkbox" name="sig_border" value="1" <?php if($this->post2pdf_conv_setting_opt['sig_border'] == 1){echo 'checked="checked" ';} ?>/> <?php _e('Border', 'post2pdf_conv') ?> <input type="checkbox" name="sig_fill" value="1" <?php if($this->post2pdf_conv_setting_opt['sig_fill'] == 1){echo 'checked="checked" ';} ?>/> <?php _e('Fill', 'post2pdf_conv') ?>
					<p><small><?php _e("Enter TEXT or HTML. It will be inserted below your content.<br />For showing your signature, license agreement, message.", 'post2pdf_conv') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Footer', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="footer" value="1" <?php if($this->post2pdf_conv_setting_opt['footer'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("Show/Hide footer.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Filters', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="filters" value="1" <?php if($this->post2pdf_conv_setting_opt['filters'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("Apply WordPress default filters to the title and content.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Shortcode handling", "post2pdf_conv") ?></th> 
				<td>
					<select name="shortcode_handling">
						<option value="parse" <?php if ($this->post2pdf_conv_setting_opt['shortcode_handling'] == "parse") {echo 'selected="selected"';} ?>><?php _e("Parse", "post2pdf_conv") ?></option>
						<option value="remove" <?php if ($this->post2pdf_conv_setting_opt['shortcode_handling'] == "remove") {echo 'selected="selected"';} ?>><?php _e("Remove", "post2pdf_conv") ?></option>
					</select>
					<p><small><?php _e("Choose shortcode handling.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Add default font to font-family', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="add_to_font_family" value="1" <?php if($this->post2pdf_conv_setting_opt['add_to_font_family'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("If a PDF file has garbled characters, try to enable this option.<br />When enabled, if your post has 'font-family' properties in 'style' attributes,<br />this plugin will add the default font for PDF to them.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
		</table>
		<p class="submit">
		<input type="submit" name="POST2PDF_Converter_Setting_Submit" value="<?php _e("Save Changes", "post2pdf_conv") ?>" />
		</p>
	</form>
	<h3><?php _e("3. Restore all settings to default", "post2pdf_conv") ?></h3>
	<form method="post" action="" onsubmit="return confirmreset()">
	<?php wp_nonce_field("post2pdf_conv_reset_options", "_wpnonce_reset_options"); ?>
		<p class="submit">
		<input type="hidden" name="post2pdf_conv_reset" value="true" />
		<input type="submit" name="POST2PDF_Converter_Reset" value="<?php _e("Reset All Settings", "post2pdf_conv") ?>" />
		</p>
	</form>
	<h3><a href="javascript:showhide('id1');" name="system_info"><?php _e("4. Your System Info", "post2pdf_conv") ?></a></h3>
	<div id="id1" style="display:none; margin-left:20px">
	<p>
	<?php _e("Server OS:", "post2pdf_conv") ?> <?php echo php_uname('s').' '.php_uname('r'); ?><br />
	<?php _e("PHP version:", "post2pdf_conv") ?> <?php echo phpversion(); ?><br />
	<?php _e("MySQL version:", "post2pdf_conv") ?> <?php echo mysql_get_server_info(); ?><br />
	<?php _e("WordPress version:", "post2pdf_conv") ?> <?php bloginfo("version"); ?><br />
	<?php _e("Site URL:", "post2pdf_conv") ?> <?php if(function_exists("home_url")) { echo home_url(); } else { echo get_option('home'); } ?><br />
	<?php _e("WordPress URL:", "post2pdf_conv") ?> <?php echo site_url(); ?><br />
	<?php _e("WordPress language:", "post2pdf_conv") ?> <?php bloginfo("language"); ?><br />
	<?php _e("WordPress character set:", "post2pdf_conv") ?> <?php bloginfo("charset"); ?><br />
	<?php _e("WordPress theme:", "post2pdf_conv") ?> <?php $post2pdf_conv_theme = get_theme(get_current_theme()); echo $post2pdf_conv_theme['Name'].' '.$post2pdf_conv_theme['Version']; ?><br />
	<?php _e("POST2PDF Converter version:", "post2pdf_conv") ?> <?php $post2pdf_conv_plugin_data = get_plugin_data(__FILE__); echo $post2pdf_conv_plugin_data['Version']; ?><br />
	<?php _e("POST2PDF Converter DB version:", "post2pdf_conv") ?> <?php echo get_option('post2pdf_conv_checkver_stamp'); ?><br />
	<?php _e("POST2PDF Converter URL:", "post2pdf_conv") ?> <?php echo $this->post2pdf_conv_plugin_url; ?><br />
	<?php _e("Your browser:", "post2pdf_conv") ?> <?php echo esc_html($_SERVER['HTTP_USER_AGENT']); ?>
	</p>
	</div>
	<p>
	<?php _e("To report a bug ,submit requests and feedback, ", "post2pdf_conv") ?><?php _e("Use <a href=\"http://wordpress.org/tags/post2pdf-converter?forum_id=10\">Forum</a> or <a href=\"http://www.near-mint.com/blog/contact\">Mail From</a>", "post2pdf_conv") ?>
	</p>
	</div>
	<?php } 
}

// Start this plugin
$POST2PDF_Converter = new POST2PDF_Converter();

?>