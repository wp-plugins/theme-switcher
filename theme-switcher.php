<?php

/*
Plugin Name: Theme Switcher
Plugin URI: http://wordpress.org/
Description: Allow your readers to switch themes.
Version: 0.2
Author: Ryan Boren
Author URI: http://boren.nu/

Adapted from Alex King's style switcher.
http://www.alexking.org/software/wordpress/

To use, add the following to your sidebar menu:

  <li>Themes:
	<?php wp_theme_switcher(); ?>
  </li>

This will create a list of themes for your readers to select.

If you would like a dropdown box rather than a list, add this:

  <li>Themes:
	<?php wp_theme_switcher('dropdown'); ?>
  </li>

*/ 

function ts_set_theme_cookie() {
	$expire = time() + 30000000;
	if (!empty($_GET["wptheme"])) {
		setcookie("wptheme" . COOKIEHASH,
							stripslashes($_GET["wptheme"]),
							$expire,
							COOKIEPATH
							);
		
		header("Location: ".get_settings('home').'/');
	}
}

function ts_get_theme() {
	if (!empty($_COOKIE["wptheme" . COOKIEHASH])) {
		return $_COOKIE["wptheme" . COOKIEHASH];
	}	else {
		return '';
	}
}

function ts_get_template($template) {
	$theme = ts_get_theme();

	if (empty($theme)) {
		return $template;
	}

	$theme = get_theme($theme);
	
	if (empty($theme)) {
		return $template;
	}

	return $theme['Template'];
}

function ts_get_stylesheet($stylesheet) {
	$theme = ts_get_theme();

	if (empty($theme)) {
		return $stylesheet;
	}

	$theme = get_theme($theme);
	
	if (empty($theme)) {
		return $stylesheet;
	}

	return $theme['Stylesheet'];
}

function wp_theme_switcher($style = "text") {
	$themes = get_themes();

	$default_theme = get_current_theme();

	if (count($themes) > 1) {
		$theme_names = array_keys($themes);
		natcasesort($theme_names);

		$ts = '<ul id="themeswitcher">'."\n";		

		if ($style == 'dropdown') {
			$ts .= '<li>'."\n"
				. '	<select name="themeswitcher" onchange="location.href=\''.get_settings('home').'/index.php?wptheme=\' + this.options[this.selectedIndex].value;">'."\n"	;

			foreach ($theme_names as $theme_name) {
					
				if ((!empty($_COOKIE["wptheme" . COOKIEHASH]) && $_COOKIE["wptheme" . COOKIEHASH] == $theme_name)
						|| (empty($_COOKIE["wptheme" . COOKIEHASH]) && ($theme_name == $default_theme))) {
					$ts .= '		<option value="'.$theme_name.'" selected="selected">'
						. htmlspecialchars($theme_name)
						. '</option>'."\n"
						;
				}	else {
					$ts .= '		<option value="'.$theme_name.'">'
						. htmlspecialchars($theme_name)
						. '</option>'."\n"
						;
				}				
			}
			$ts .= '	</select>'."\n"
				. '</li>'."\n"
				;
		}	else {
			foreach ($theme_names as $theme_name) {
				$display = htmlspecialchars($theme_name);
				
				if ((!empty($_COOKIE["wptheme" . COOKIEHASH]) && $_COOKIE["wptheme" . COOKIEHASH] == $theme_name)
						|| (empty($_COOKIE["wptheme" . COOKIEHASH]) && ($theme_name == $default_theme))) {
					$ts .= '	<li>'.$display.'</li>'."\n";
				}	else {
					$ts .= '	<li><a href="'
						.get_settings('home').'/'. 'index.php'
						.'?wptheme='.urlencode($theme_name).'">'
						.$display.'</a></li>'."\n";
				}
			}
		}
		$ts .= '</ul>';
	}

	echo $ts;
}

ts_set_theme_cookie();

add_filter('template', 'ts_get_template');
add_filter('stylesheet', 'ts_get_stylesheet');

?>
