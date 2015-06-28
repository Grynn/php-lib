<?php namespace Grynn\PhpLib;


/**
 * Simple perl like quote word
 * For example qw("hello world") => [ "hello", "world" ]
 * @param $x string     space or comma separated words
 * @return array        each space or comma separated word in $x
 */
function qw($x) {
	return preg_split('/[\s,]+/', $x);
}


/**
 * Return true if client sends Accept header with application/json or
 * if the request is AJAX
 * @return bool
 */
function wantsJson() {
	$hdr = false;
	$hdrs = getallheaders();
	foreach ($hdrs as $k=>$v)
	{
		if ($k == "Accept" && strstr($v, "application/json")!==false) {
			$hdr = true;
			break;
		}
	}

	return $hdr || isAjax();
}


/**
 * Return true is SAPI is cli (i.e. php code is running via CLI)
 * NOTE: output may be redirected, this does not check that output is a TTY
 * @return bool
 */
function isConsole() {
	return php_sapi_name() == "cli";
}


/**
 * Returns true if called via xmlhttp from browser
 * @return bool
 */
function isAjax() {
	return strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest';
}


/**
 * Returns true if request method is POST
 * @return bool
 */
function isPost() {
	return ($_SERVER['REQUEST_METHOD'] == "POST");
}

/**
 * Returns true if request method is GET
 * @return bool
 */
function isGet() {
	return ($_SERVER['REQUEST_METHOD'] == "GET");
}


/**
 * Returns Request Method (GET|PUT|POST..)
 * NOTE: could be null if running via cli
 * @return string
 */
function requestMethod() {
	return $_SERVER['REQUEST_METHOD'];
}


/**
 * Also see @link http://us2.php.net/manual/en/function.get-browser.php get_browser()
 * @return mixed
 */
function userAgent() {
	return $_SERVER['HTTP_USER_AGENT'];
}


/**
 * Abort processing with a 400 Bad Request. (HTTP status code 400)
 * If client wantsJson (or request is AJAX) send JSON { error: "Bad Request"|$msg } with HTTP status code 200 instead
 * Optionally override the status code by specifying code, and msg ...
 * NOTE: Always dies after sending headers. To prevent forgetting by caller
 * @param string $msg   Optional; Defaults to "Bad Request", no good reason to override
 * @param int $code     Optional; Defaults to 400 if not AJAX/JSON, else 200
 * @param null $detail  Optional; Defaults to null (no content)
 */
function badRequest($msg = "Bad Request", $code = 400, $detail = null)
{
	if (wantsJson()) {
		header("Content-Type: application/json");
		echo json_encode(['error' => $msg]);
		die;
	} else {
		header("HTTP/1.1 $code $msg");
		if (!empty($detail)) {
			header("Content-Type: plain/text");
			echo htmlentities($detail);
		}
		die;
	}
}
