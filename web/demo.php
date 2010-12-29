<?php
// Input values.
$url         = (isset($_GET["url"]) && $_GET["url"] !== "" ? $_GET["url"] : FALSE);
$base        = (isset($_GET["base"]) && $_GET["base"] !== "" ? $_GET["base"] : FALSE);
$no_mhtml    = (isset($_GET["no_mhtml"]) && $_GET["no_mhtml"] !== "" ? $_GET["no_mhtml"] : FALSE);
$size_limit  = (isset($_GET["size_limit"]) && $_GET["size_limit"] !== "" ? intval($_GET["size_limit"]) : 1024);
$size_limits = array(1024, 2048, 4096);
$content     = "";

if ($url && $base)
{
    $error = "";
    $num  = preg_match('/(https?:\/\/[^\s+\"\<\>]+.css)/i', $url);
    if ( ! $num)
    {
        $error = "Not a valid CSS file URL.";
    }
    $num  = preg_match('/(https?:\/\/[^\s+\"\<\>]+)/i', $base);
    if ( ! $num)
    {
        $error = "Not a valid CSS base URL.";
    }

    if ( ! $error)
    {
        // Build working directory
        $tmp_path = microtime();
        $tmp_path = substr(md5($tmp_path), 0, 8);
        $tmp_path = "/tmp/$tmp_path/";
        mkdir($tmp_path);
        
        // Get CSS file using cURL.
        $cmd = "curl \"$url\" > {$tmp_path}" . basename($url);
        exec($cmd, $return, $error);
        if ( $error)
        {
            $error = "CSS doesn't exist.";
        }
        else
        {
            // dataurize your file.
            $no_mhtml = ($no_mhtml == 1) ? "--no-mhtml" : "";
            $cmd =  dirname(__FILE__) . "/../dataurize {$tmp_path}" . basename($url) . " $base --size-limit={$size_limit}  $no_mhtml --print";
            exec($cmd, $return, $error);
            $content = implode($return, "\n");
        }
        exec("rm -rf {$tmp_path}");
    }
} else {
    $error = "CSS URL or base URL cannot be empty!";
}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Web Interface for dataurize</title>
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.2.0/build/cssreset/reset-min.css">
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.2.0/build/cssfonts/fonts-min.css">
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.2.0/build/cssbase/base-min.css">
<style type="text/css">
form input[type=text] {
    width: 800px;
    padding: 4px;
    font-size: 123.1%;
}
form .row {
    margin-bottom: 10px;
}
form label {
    font-weight: bold;
    display: -moz-inline-box;
    display: inline-block;
    width: 8em;
}
form .error {
    color: red;
}
.output {
    background: #ffe;
    border: solid 1px #ccc;
    color: green;
    font-size: 11px;
    font-family: Verdana;
    margin-top: 10px;
    margin-bottom: 20px;
    width: 100%;
    height: 400px;
}
form .prompt {
    color: #666;
    font-size: 11px;
    font-family: Verdana;
    margin-left: 10em;
    margin-top: .3em;
}
.box {
    width: 80%;
    margin: 0 auto;
}
form .prompt em {
    color: #a00;
    cursor: pointer;
}
</style>
</head>
<body>
    <div class="box">
        <h1>dataurize Web Interface</h1>
        <p>Blog: <a href="http://josephj.com/entry.php?id=345" target="_blank">http://josephj.com/entry.php?id=345</a>, GitHub: <a href="http://github.com/josephj/dataurize" target="_blank">http://github.com/josephj/dataurize</a>
        <form method="get">
            <p class="error"><?php echo ($error) ? $error : ""; ?></p>
            <div class="row">
                <label for="url">CSS URL: </label>
                <input id="url" type="text" name="url" value="<?php echo $url; ?>">
                <div class="prompt">CSS URL path with http:// (Required)<br>Sample: <em class="sample">http://l.yimg.com/e/serv/index/index3/css/wfp-css_201012141058.css</em> (<a href="http://www.wretch.cc" target="_blank">www.wretch.cc</a>)</div>
            </div>
            <div class="row">
                <label for="base">Base URL: </label>
                <input id="base" type="text" name="base" value="<?php echo $base; ?>">
                <div class="prompt">Background image base URL with http:// (Required)<br>Sample: <em class="sample">http://l.yimg.com/e/serv/index/index3/css/</em> (<a href="http://www.wretch.cc" target="_blank">www.wretch.cc</a>)</div>
            </div>
            <div class="row">
                <label for="size_limit">Size Limit: </label>
                <select name="size_limit" id="size_limit">
<?php foreach ($size_limits as $v) : ?>
                    <option<?php echo ($v == $size_limit ? " selected" : ""); ?>><?php echo $v; ?></option>
<?php endforeach; ?>
                </select>
                <div class="prompt">Only deals with images within this file size limit.</div>
            </div>
            <div class="row">
                <label for="no_mhtml">No MHTML:</label>
                <input type="checkbox" name="no_mhtml" id="no_mhtml" value="1"<?php echo ($no_mhtml) ? " checked" : ""; ?>>
                <div class="prompt">Check this option if you want to have Data URIs only without MHTML fallback. It still has image requests for fallback situations.</div>
            </div>
            <div class="row">
                <input type="submit" value="Submit">
            </div>
        </form>
<?php if ($content) : ?>
        <hr>
        <textarea class="output"><?php  echo $content; ?></textarea>
<?php endif; ?>
    </div>
</body>
<script type="text/javascript" src="http://yui.yahooapis.com/3.0.0/build/yui/yui-min.js"></script> 
<script type="text/javascript">
YUI().use("node", function (Y) {
    var urlNode  = Y.one("#url"),
        baseNode = Y.one("#base"),
        CSS_PATTERN  = /(https?:\/\/[^\s+\"\<\>]+.css)/gi,
        BASE_PATTERN = /(https?:\/\/[^\s+\"\<\>])/gi;

    Y.all("em.sample").on("mousedown", function (e) {
        e.currentTarget.ancestor(".row").one("input").set("value", e.currentTarget.get("innerHTML"));
    });

    urlNode.on("paste", function (e) {
        if (CSS_PATTERN.test(this.get("value"))) {
            baseNode.set("value", this.get("value").substr(0, this.get("value").lastIndexOf("/") + 1));
        }
    });
    urlNode.on("keyup", function (e) {
        if (CSS_PATTERN.test(this.get("value"))) {
            baseNode.set("value", this.get("value").substr(0, this.get("value").lastIndexOf("/") + 1));
        }
    });
});
</script>
</html>
