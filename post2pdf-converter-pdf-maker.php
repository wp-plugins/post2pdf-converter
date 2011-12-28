<?php
/*
by Redcocker
Last modified: 2011/12/29
License: GPL v2
http://www.near-mint.com/blog/
*/

//Load bootstrap file
require_once('post2pdf-converter-bootstrap.php');

if (!class_exists('POST2PDF_Converter'))
	wp_die(__("You are not allowed to access this file.", "post2pdf_conv"));

class POST2PDF_Converter_PDF_Maker {

	var $post2pdf_conv_plugin_url;
	var $post2pdf_conv_setting_opt;

	function __construct() {
		$this->post2pdf_conv_plugin_url = plugin_dir_url(__FILE__);
		$this->post2pdf_conv_setting_opt = get_option('post2pdf_conv_setting_opt');
		if (($this->post2pdf_conv_setting_opt['access'] == "referrer" && strpos($_SERVER['HTTP_REFERER'], site_url()) === false) ||
			($this->post2pdf_conv_setting_opt['access'] == "login" && !is_user_logged_in())
		) {
			wp_die(__("You are not allowed to access this file.", "post2pdf_conv"));
		} else {
			$this->post2pdf_conv_post_to_pdf();
		}
	}

	function post2pdf_conv_post_to_pdf() {
		$post_id = 0;
		$post_id = intval($_GET['id']);
		$post_data = get_post($post_id);

		if (!$post_data) {
			wp_die(__('Post does not exists.', 'post2pdf_conv'));
		}

		$title = $post_data->post_title;
		if (mb_strlen($title, 'UTF-8') < 42) {
			$header_title = strip_tags($title);
		} else {
			$header_title = mb_substr(strip_tags($title), 0, 42, 'UTF-8')."...";
		}
		$permalink = get_permalink($post_data->ID);

		$author_data = get_userdata($post_data->post_author);
		if ($author_data->display_name) {
			$author = $author_data->display_name;
		} else {
			$author = $author_data->user_nicename;
		}
		$tag = array();
		$tags = '';
		$tags_data = wp_get_post_tags($post_data->ID);
		if ($tags_data) {
			foreach ($tags_data as $val) {
				$tag[] = $val->name;
			}
			$tags = implode(',', $tag);
		}
		$content = $post_data->post_content;
		$config_lang = substr($this->post2pdf_conv_setting_opt['lang'], 0, 3);
		$font = $this->post2pdf_conv_setting_opt['font'];
		$monospaced_font = $this->post2pdf_conv_setting_opt['monospaced_font'];
		$font_size = $this->post2pdf_conv_setting_opt['font_size'];

		$logo_file = $this->post2pdf_conv_setting_opt['logo_file'];
		$logo_width = $this->post2pdf_conv_setting_opt['logo_width'];

		if ($this->post2pdf_conv_setting_opt['file'] == "title") {
			$filename = $post_data->post_title;
		} else {
			$filename = $post_id;
		}

		// Include TCPDF
		require_once('tcpdf/config/lang/'.$config_lang.'.php');
		require_once('tcpdf/tcpdf.php');
		//$logo_file_path = validate_file($logo_file_path);

		// Create a new object
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($author);
		$pdf->SetTitle($title . get_option('blogname'));
		$pdf->SetSubject(get_the_category_list(',', '', $post_id));
		$pdf->SetKeywords($tags);

		// set header data
		if ($this->post2pdf_conv_setting_opt['logo_enable'] == 1 && $logo_file) {
			$pdf->SetHeaderData($logo_file, $logo_width, $header_title, "by " .$author. " - ". $permalink);
		} else {
			$pdf->SetHeaderData('', 0, $header_title, "by " .$author. " - ". $permalink);
		}

		// set header and footer fonts
		$pdf->setHeaderFont(Array($font, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array($font, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont($monospaced_font);

		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// Set fontsubsetting mode
		if ($this->post2pdf_conv_setting_opt['font_subsetting'] == 1) {
			$pdf->setFontSubsetting(true);
		} else if ($this->post2pdf_conv_setting_opt['font_subsetting'] == 0) {
			$pdf->setFontSubsetting(false);
		}

		// Set font
		$pdf->SetFont($font, '', $font_size, true);

		// Add a page
		$pdf->AddPage();

		// Create post content to print
		if ($this->post2pdf_conv_setting_opt['shortcode_handling'] == "parse") {
			if (function_exists('wp_sh_do_shortcode')) {
				$content = wp_sh_do_shortcode($content);
			}
			$pre_filterd_content = do_shortcode($content);
		} else if ($this->post2pdf_conv_setting_opt['shortcode_handling'] == "remove") {
			$pre_filterd_content = strip_shortcodes($content);
		} else {
			$pre_filterd_content = $content;
		}

		$filterd_content = wpautop($pre_filterd_content);

		$formatted_title = '<h1 style="text-align:center;">' . $title . '</h1>';
		$formatted_post = $formatted_title . '<br/><br/>' . $filterd_content;
		if ($this->post2pdf_conv_setting_opt['add_to_font_family'] == 1) {
			$formatted_post = preg_replace('/(<[^>]*?font-family[^:]*?:)([^;]*?;[^>]*?>)/is', "$1".$font.",$2", $formatted_post);
		}
		// Print post
		$pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $formatted_post, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

		// Set background
		$pdf->SetFillColor(255, 255, 127);
		$pdf->setCellPaddings(5, 5, 0, 0);

		// Output pdf document
		$pdf->Output(substr($filename, 0 ,255).'.pdf', $this->post2pdf_conv_setting_opt['destination']);

	}

}

// Start this plugin
$POST2PDF_Converter_PDF_Maker = new POST2PDF_Converter_PDF_Maker();

?>