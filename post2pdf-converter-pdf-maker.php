<?php
/*
by Redcocker
Last modified: 2012/1/28
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
	var $post2pdf_conv_sig;
	var $target_post_id;

	function __construct() {
		$this->post2pdf_conv_plugin_url = plugin_dir_url(__FILE__);
		$this->post2pdf_conv_setting_opt = get_option('post2pdf_conv_setting_opt');
		$this->post2pdf_conv_sig = get_option('post2pdf_conv_sig');

		if(function_exists("home_url")) {
			$wp_url = home_url();
		} else {
			$wp_url = get_option('home');
		}

		$this->target_post_id = 0;

		if (isset($_POST['POST2PDF_Converter_PDF_Generater']) && $_POST['post2pdf_conv_pdf_generater_hidden_value'] == "true" && check_admin_referer("post2pdf_conv_pdf_generater", "_wpnonce_pdf_generater")) {
			if (!file_exists(WP_CONTENT_DIR."/tcpdf-pdf/")) {
				mkdir(WP_CONTENT_DIR."/tcpdf-pdf", 0755 );
			}
			$this->target_post_id = intval($_POST['target_id']);
		}

		if (($this->post2pdf_conv_setting_opt['access'] == "referrer" && strpos($_SERVER['HTTP_REFERER'], $wp_url) === false) ||
			($this->post2pdf_conv_setting_opt['access'] == "login" && !is_user_logged_in())
		) {
			wp_die(__("You are not allowed to access this file.", "post2pdf_conv"));
		} else {
			$this->post2pdf_conv_post_to_pdf();
		}
	}

	function post2pdf_conv_post_to_pdf() {
		$post_id = 0;
		if (!empty($_GET['id'])) {
			$post_id = intval($_GET['id']);
		}

		if ($this->target_post_id != 0) {
			$post_id = $this->target_post_id;
		}

		$post_data = get_post($post_id);

		if (!$post_data) {
			wp_die(__('Post does not exists.', 'post2pdf_conv'));
		}

		$title = strip_tags($post_data->post_title);

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
			$tags = implode(' ', $tag);
		}

		$content = $post_data->post_content;

		if (!empty($_GET['lang'])) {
			$config_lang = substr(esc_html($_GET['lang']), 0, 3);
		} else {
			$config_lang = substr($this->post2pdf_conv_setting_opt['lang'], 0, 3);
		}

		if (!empty($_GET['file'])) {
			$filename_type = $_GET['file'];
		} else {
			$filename_type = $this->post2pdf_conv_setting_opt['file'];
		}
		if ($filename_type == "title" && $this->target_post_id == 0) {
			$filename = $post_data->post_title;
		} else {
			$filename = $post_id;
		}

		$filename = substr($filename, 0 ,255);

		if ($this->target_post_id != 0) {
			$filename = WP_CONTENT_DIR."/tcpdf-pdf/".$filename;
		}

		if (!empty($_GET['font'])) {
			$font = esc_html($_GET['font']);
		} else {
			$font = $this->post2pdf_conv_setting_opt['font'];
		}

		if (!empty($_GET['monospaced'])) {
			$monospaced_font = esc_html($_GET['monospaced']);
		} else {
			$monospaced_font = $this->post2pdf_conv_setting_opt['monospaced_font'];
		}

		if (!empty($_GET['fontsize'])) {
			$font_size = intval($_GET['fontsize']);
		} else {
			$font_size = $this->post2pdf_conv_setting_opt['font_size'];
		}

		if (!empty($_GET['subsetting']) && ($_GET['subsetting'] == 1 || $_GET['subsetting'] == 0)) {
			$subsetting_enable = $_GET['subsetting'];
		} else {
			$subsetting_enable = $this->post2pdf_conv_setting_opt['font_subsetting'];
		}

		if ($subsetting_enable == 1) {
			$subsetting = "true";
		} else {
			$subsetting = "false";
		}

		if (!empty($_GET['ratio'])) {
			$ratio = floatval($_GET['ratio']);
		} else {
			$ratio = $this->post2pdf_conv_setting_opt['image_ratio'];
		}

		if (!empty($_GET['header'])) {
			$header_enable = $_GET['header'];
		} else {
			$header_enable = $this->post2pdf_conv_setting_opt['header'];
		}

		if (!empty($_GET['logo'])) {
			$logo_enable = $_GET['logo'];
		} else {
			$logo_enable = $this->post2pdf_conv_setting_opt['logo_enable'];
		}

		if (!empty($_GET['logo_file'])) {
			$logo_file = esc_html($_GET['logo_file']);
		} else {
			$logo_file = $this->post2pdf_conv_setting_opt['logo_file'];
		}

		if (!empty($_GET['logo_width'])) {
			$logo_width = intval($_GET['logo_width']);
		} else {
			$logo_width = $this->post2pdf_conv_setting_opt['logo_width'];
		}

		if (!empty($_GET['wrap_title'])) {
			$wrap_title = $_GET['wrap_title'];
		} else {
			$wrap_title = $this->post2pdf_conv_setting_opt['wrap_title'];
		}

		if (!empty($_GET['footer'])) {
			$footer_enable = $_GET['footer'];
		} else {
			$footer_enable = $this->post2pdf_conv_setting_opt['footer'];
		}

		if (!empty($_GET['filters'])) {
			$filters = $_GET['filters'];
		} else {
			$filters = $this->post2pdf_conv_setting_opt['filters'];
		}

		if (!empty($_GET['shortcode'])) {
			$shortcode = esc_html($_GET['shortcode']);
		} else {
			$shortcode = $this->post2pdf_conv_setting_opt['shortcode_handling'];
		}

		$destination = $this->post2pdf_conv_setting_opt['destination'];

		if ($this->target_post_id != 0) {
			$destination = "F";
		}

		// Apply default filters to title and content
		if ($filters == 1) {
			if (has_filter('the_title', 'wptexturize')) {
				$title = wptexturize($title);
			}
			if (has_filter('the_title', 'convert_chars')) {
				$title = convert_chars($title);
			}
			if (has_filter('the_title', 'trim')) {
				$title = trim($title);
			}
			if (has_filter('the_title', 'capital_P_dangit')) {
				$title = capital_P_dangit($title);
			}

			if (has_filter('the_content', 'wptexturize')) {
				$content = wptexturize($content);
			}
			if (has_filter('the_content', 'convert_smilies')) {
				$content = convert_smilies($content);
			}
			if (has_filter('the_content', 'convert_chars')) {
				$content = convert_chars($content);
			}
			if (has_filter('the_content', 'wpautop')) {
				$content = wpautop($content);
			}
			if (has_filter('the_content', 'shortcode_unautop')) {
				$content = shortcode_unautop($content);
			}
			if (has_filter('the_content', 'prepend_attachment')) {
				$content = prepend_attachment($content);
			}
			if (has_filter('the_content', 'capital_P_dangit')) {
				$content = capital_P_dangit($content);
			}
		}

		// Include TCPDF
		require_once('tcpdf/config/lang/'.$config_lang.'.php');
		require_once('tcpdf/tcpdf.php');

		// Create a new object
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, false);

		// Set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($author);
		$pdf->SetTitle($title . get_option('blogname'));
		$pdf->SetSubject(strip_tags(get_the_category_list(',', '', $post_id)));
		$pdf->SetKeywords($tags);

		// Set header data
		if (mb_strlen($title, 'UTF-8') < 42) {
			$header_title = $title;
		} else {
			$header_title = mb_substr($title, 0, 42, 'UTF-8')."...";
		}

		if ($header_enable == 1) {
			if ($logo_enable == 1 && $logo_file) {
				$pdf->SetHeaderData($logo_file, $logo_width, $header_title, "by " .$author. " - ". $permalink);
			} else {
				$pdf->SetHeaderData('', 0, $header_title, "by " .$author. " - ". $permalink);
			}
		}

		// Set header and footer fonts
		if ($header_enable == 1) {
			$pdf->setHeaderFont(Array($font, '', PDF_FONT_SIZE_MAIN));
		}
		if ($footer_enable == 1) {
			$pdf->setFooterFont(Array($font, '', PDF_FONT_SIZE_DATA));
		}

		// Remove header/footer
		if ($header_enable == 0) {
			$pdf->setPrintHeader(false);
		}
		if ($header_enable == 0) {
			$pdf->setPrintFooter(false);
		}

		// Set default monospaced font
		$pdf->SetDefaultMonospacedFont($monospaced_font);

		// Set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		if ($header_enable == 1) {
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		}
		if ($footer_enable == 1) {
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		}

		// Set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// Set image scale factor
		$pdf->setImageScale($ratio);

		// Set some language-dependent strings
		$pdf->setLanguageArray($l);

		// Set fontsubsetting mode
		$pdf->setFontSubsetting($subsetting);

		// Set font
		$pdf->SetFont($font, '', $font_size, true);

		// Add a page
		$pdf->AddPage();

		// Create post content to print
		if ($wrap_title == 1) {
			if (mb_strlen($title, 'UTF-8') < 33) {
				$title = $title;
			} else {
				$title = mb_substr($title, 0, 33, 'UTF-8')."<br />".mb_substr($title, 33, 222, 'UTF-8');
			}
		}

		if ($shortcode == "parse") {
			// For WP SyntaxHighlighter
			if (function_exists('wp_sh_do_shortcode')) {
				$content = wp_sh_do_shortcode($content);
			}
			// For QuickLaTeX
			if (function_exists('quicklatex_parser')) {
				$content = quicklatex_parser($content);
				$content = preg_replace_callback("/(<p class=\"ql-(center|left|right)-displayed-equation\" style=\"line-height: )([0-9]+?)(px;)(\">)/i", array($this, post2pdf_conv_qlatex_displayed_equation), $content);
				$content = preg_replace("/<p class=\"ql-center-picture\">/i", "<p class=\"ql-center-picture\" style=\"text-align: center;\"><span class=\"ql-right-eqno\"> &nbsp; <\/span><span class=\"ql-left-eqno\"> &nbsp; <\/span>", $content);
			}
			$content = do_shortcode($content);
		} else if ($this->post2pdf_conv_setting_opt['shortcode_handling'] == "remove") {
			$content = strip_shortcodes($content);
		}

		// Convert relative image path to absolute image path
		$content = preg_replace("/<img([^>]*?)src=['\"]((?!(http:\/\/|https:\/\/|\/))[^'\"]+?)['\"]([^>]*?)>/i", "<img$1src=\"".site_url()."/$2\"$4>", $content);

		// Add width and height into image tag
		$content = preg_replace_callback("/(<img[^>]*?src=['\"]((http:\/\/|https:\/\/|\/)[^'\"]*?(jpg|jpeg|gif|png))['\"])([^>]*?>)/i", array($this, post2pdf_conv_img_size), $content);

		// For common SyntaxHighlighter
		$content = preg_replace("/<pre[^>]*?brush:[^>]*?>(.*?)<\/pre>/is", "<pre style=\"word-wrap:break-word; color: #406040; background-color: #F1F1F1; border: 1px solid #9F9F9F;\">$1</pre>", $content);
		$content = preg_replace("/<script[^>]*?type=['\"]syntaxhighlighter['\"][^>]*?>(.*?)<\/script>/is", "<pre style=\"word-wrap:break-word; color: #406040; background-color: #F1F1F1; border: 1px solid #9F9F9F;\">$1</pre>", $content);
		$content = preg_replace("/<pre[^>]*?name=['\"][^>]*?>(.*?)<\/pre>/is", "<pre style=\"word-wrap:break-word; color: #406040; background-color: #F1F1F1; border: 1px solid #9F9F9F;\">$1</pre>", $content);
		$content = preg_replace("/<textarea[^>]*?name=['\"][^>]*?>(.*?)<\/textarea>/is", "<pre style=\"word-wrap:break-word; color: #406040; background-color: #F1F1F1; border: 1px solid #9F9F9F;\">$1</pre>", $content);
		// For GeSHi(WP-Syntax, CodeColorer, WP-CodeBox, WP-SynHighlight etc)
		$content = preg_replace("/<pre[^>]*?lang=['\"][^>]*?>(.*?)<\/pre>/is", "<pre style=\"word-wrap:break-word; color: #406040; background-color: #F1F1F1; border: 1px solid #9F9F9F;\">$1</pre>", $content);
		$content = preg_replace("/<code[^>]*?lang=['\"][^>]*?>(.*?)<\/code>/is", "<pre style=\"word-wrap:break-word; color: #406040; background-color: #F1F1F1; border: 1px solid #9F9F9F;\">$1</pre>", $content);
		// For other sourcecode
		$content = preg_replace("/<pre[^>]*?><code[^>]*?>(.*?)<\/code><\/pre>/is", "<pre style=\"word-wrap:break-word; color: #406040; background-color: #F1F1F1; border: 1px solid #9F9F9F;\">$1</pre>", $content);
		// For blockquote
		$content = preg_replace("/<blockquote[^>]*?>(.*?)<\/blockquote>/is", "<blockquote style=\"color: #406040;\">$1</blockquote>", $content);

		$formatted_title = '<h1 style="text-align:center;">' . $title . '</h1>';
		$formatted_post = $formatted_title . '<br/><br/>' . $content;

		if ($this->post2pdf_conv_setting_opt['add_to_font_family'] == 1) {
			$formatted_post = preg_replace('/(<[^>]*?font-family[^:]*?:)([^;]*?;[^>]*?>)/is', "$1".$font.",$2", $formatted_post);
		}

		if ($this->post2pdf_conv_setting_opt['sig_enable'] == 1) {
			$formatted_post = $formatted_post."<br />";
		}

		// Print post
		$pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $formatted_post, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

		// Set background
		$pdf->SetFillColor(255, 255, 127);
		$pdf->setCellPaddings(5, 5, 0, 0);

		// Print signature
		if ($this->post2pdf_conv_setting_opt['sig_enable'] == 1) {
			if ($this->post2pdf_conv_setting_opt['sig_border'] == 1) {
				$border = "1";
			} else {
				$border = "0";
			}
			if ($this->post2pdf_conv_setting_opt['sig_fill'] == 1) {
				$fill = "1";
			} else {
				$fill = "0";
			}
			$pdf->MultiCell(0, 0, $this->post2pdf_conv_sig."<br />", $border, 'L', $fill, 2, '', '', true, 0, true, true, 0, 'T', false);
		}

		// Output pdf document
		// Clean the output buffer
		ob_clean();
		$pdf->Output($filename.'.pdf', $destination);

		if ($this->target_post_id != 0) {
			wp_die(__("<strong>Generating completed successfully.</strong><br /><br />Post/Page title: ", "post2pdf_conv").$title.__("<br />Output path: ", "post2pdf_conv").WP_CONTENT_DIR."/tcpdf-pdf/".$this->target_post_id.".pdf".__("<br /><br />Go back to ", "post2pdf_conv")."<a href=\"".site_url()."/wp-admin/options-general.php?page=post2pdf-converter-options\">".__("the settig panel</a>.", "post2pdf_conv"), __("POST2PDF Converter", "post2pdf_conv"));
		}

	}

	function post2pdf_conv_qlatex_displayed_equation($matches) {
		$line_height = intval($matches[3]);
		if ($line_height > 40) {
			$line_height = $line_height/3;
		} else {
			$line_height = round($line_height/2);
		}
		return $matches[1].$line_height.$matches[4]." text-align: ".$matches[2].";".$matches[5];
	}

	function post2pdf_conv_img_size($matches) {
		$size = getimagesize($matches[2]);
		$img_tag = $matches[1]." ".$size[3].$matches[5];
		return $img_tag;
	}

}

// Start this plugin
$POST2PDF_Converter_PDF_Maker = new POST2PDF_Converter_PDF_Maker();

?>