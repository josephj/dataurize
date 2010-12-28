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
    }
}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Web Interface for dataurize</title>
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
    display: inline-block;
    display: -moz-inline-box;
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
    margin: 10px;
    width: 800px;
    height: 400px;
}
</style>
</head>
<body>
    <form method="get">
        <p class="error"><?php echo ($error) ? $error : ""; ?></p>
        <div class="row">
            <label>CSS URL: </label>
            <input type="text" name="url" value="<?php echo $url; ?>">
        </div>
        <div class="row">
            <label>Base URL: </label>
            <input type="text" name="base" value="<?php echo $base; ?>">
        </div>
        <div class="row">
            <label>Size Limit: </label>
            <select name="size_limit">
<?php foreach ($size_limits as $v) : ?>
                <option<?php echo ($v == $size_limit ? " selected" : ""); ?>><?php echo $v; ?></option>
<?php endforeach; ?>
            </select>
        </div>
        <div class="row">
            <label>No MHTML:</label>
            <input type="checkbox" name="no_mhtml" value="1"<?php echo ($no_mhtml) ? " checked" : ""; ?>>
        </div>
        <div class="row">
            <input type="submit" value="Submit">
        </div>
    </form>
    <hr>
    <textarea class="output"><?php  echo $content; ?></textarea>
</body>
</html>
