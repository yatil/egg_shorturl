<?php
# <?php // Tricking Textmate into displaying syntax highlighting

function egg_shorturl_link() {
	if (isset($GLOBALS['thisarticle']['thisid'])) {
		return '<link rel="shorturl" href="http://'.$GLOBALS['prefs']['siteurl'].$GLOBALS['prefs']['path_from_root'].$GLOBALS['thisarticle']['thisid'].'" />';
	}
}

function egg_shorturl_a() {
	return '<a href="http://'.$GLOBALS['prefs']['siteurl'].$GLOBALS['prefs']['path_from_root'].$GLOBALS['thisarticle']['thisid'].'" rel="shorturl">'.$GLOBALS['prefs']['siteurl'].$GLOBALS['prefs']['path_from_root'].$GLOBALS['thisarticle']['thisid'].'</a>';
}

function egg_shorturl_tweet($atts) {

	if (is_array($atts)) extract($atts);

	$linktext = isset($linktext) ? $linktext : 'Diesen Artikel twittern';
	$tweetpre = isset($tweetpre) ? $tweetpre : 'Lese gerade:'; 
	$class = isset($class) ? $class : 'tweetme'; 
	$href = 'http://'.$GLOBALS['prefs']['siteurl'].$GLOBALS['prefs']['path_from_root'].$GLOBALS['thisarticle']['thisid'];

	$title = $GLOBALS['thisarticle']['title'];

	$tweetspace = 140 - (strlen($tweetpre) + strlen($href) + 2);

	/* This part trims the title */
	/* By @toscho - see http://toscho.de/2009/php-funktion-end_on_word/ */

	if(strlen($title) > $tweetspace){
		$title = substr($title, 0, $tweetspace);
		$arr   = explode(' ', trim($title) );
		array_pop($arr);
		$title = rtrim( implode(' ', $arr), ',;');
		$title = $title."?";
	}

	$message = $tweetpre." ".$title." ".$href;

	return '<a href="http://twitter.com/home?status='.urlencode($message).'" class="'.$class.'">'.$linktext.'</a>';

}

function egg_shorturl_handler() {

	$subpath = preg_replace("/https?:\/\/.*(\/.*)/Ui","$1",hu);
	$regsafesubpath = preg_quote($subpath, '/');
	$req = preg_replace("/^$regsafesubpath/i",'/',$_SERVER['REQUEST_URI']);

	$qs = strpos($req, '?');
	$qatts = ($qs ? '&'.substr($req, $qs + 1) : '');
	if ($qs) $req = substr($req, 0, $qs);

	$parts = array_values(array_filter(split('/', $req)));
	$rs = safe_row("Section, url_title","textpattern", "id=".$parts[1]);
	
	extract($rs);
	
	$url = 'http://'.$GLOBALS['prefs']['siteurl'].$GLOBALS['prefs']['path_from_root'].$Section.'/'.$url_title;
	
	header('Location: ' . $url);
	txp_die('', '301');
}
/*
--- PLUGIN METADATA ---
Name: egg_shorturl
Version: 0.3
Type: 0
Description: Helpers for short URLs
Author: Eric Eggert
Link: http://yatil.de
--- BEGIN PLUGIN HELP ---
<h1>egg_shorturl</h1>

	<p>This plugin needs to be used with following lines in your <kbd>.htaccess</kbd> file:</p>
<pre><code>RewriteEngine On
RewriteRule ^(\d+)$ http://example.com/s/$1 [R=301,L]</code></pre>
	<p>(with example.com = your domain)</p>
	<p>and a section <code>s</code> with the following template:</p>
<pre><code>&lt;txp:egg_shorturl_handler /&gt;</code></pre>
	<p><strong>This plugin works only with the <code>/section/title</code> URL schema.</strong></p>


	<h2>Helper functions</h2>
	<h3>&lt;txp:egg_shorturl_link /></h3>
	<p>Outputs a link element for the header area of your <span class="caps">HTML</span> document. (Empty on article list documents.)</p>
	<h3>&lt;txp:egg_shorturl_a /></h3>
	<p>Outputs a normal a href element to use in article context (eg. in your default form).</p>
	<h3>&lt;txp:egg_shorturl_tweet /></h3>
	<p>Outputs a link to twitter filling the textbox with the title and a link to your article. Parameters:</p>
<ul>
	<li><code>class</code> = <span class="caps">CSS</span> class, defaults to <code>tweetme</code></li>
		<li><code>tweetpre</code> = Text preceding your tweet, defaults to <code>Lese gerade:</code> (en: <code>Now reading:</code>)</li>
		<li><code>linktext</code> = Text in your a element, defaults to <code>Diesen Artikel twittern</code> (en: <code>Tweet this article</code>)</li>
	</ul>
--- END PLUGIN HELP & METADATA ---
*/
?>