# dataurize

A command-line tool that replaces CSS background image to both Data URI and MTHML .

## Warning

After Microsoft published a hotfix for "[Vulnerability in MHTML Could Allow Information Disclosure (2544893)](http://technet.microsoft.com/en-us/security/bulletin/ms11-037)", MHTML became no longer works in IE browser. That means you should only use dataurize to convert background image to Data URI. Or uses better CSS Sprite technology like Compass.

## Why 

### Performance

Using Data URI instead of binary images is a very good idea in web development. One binary image means an extra HTTP request. It's very common to have 30+ background images of a single web page. The more requests means the poor performance of your website. 

### Comparing with CSS Sprites

A common solution to this is CSS Sprites, combining multiple images into one.
However, the maintenance cost of choosing this apporach is really expensive. 
You always have to use some softwares like Photoshop or Fireworks to maintain.
And carefully using CSS attributes like background-position, overflow: hidden, width, and height to make it work. A engineering industry shouldn't have such ridiculous development process.

### What is Data URI

Data URI transforms image to Base64 encodeed string. That means you can have a image without any HTTP Request. Though the file size blows to 2 to 3 times larger, proper GZip makes it worthy of the investment. And remember request is expensive than file size in most of situations.

### MHTML - Alternative Solution for Legend Browsers

The only thing we need to take care is browser compatibility issues. Internet Explorer version before 8 doesn't support Data URI. However, something similar called MHTML makes it up.

dataurize is a simple PHP CLI script converting your tranditional CSS file to 
the one using Data URI and MHTML.  You don't have to worry about browser compatibility because it also attaches equivalent MHTML into your target CSS.

## Demo

* [Web interface using *dataurize*](http://josephj.com/lab/dataurize/web/demo.php).

## Dependencies

* ImageMagick 6.3.7+
* PHP 5.2.6+

## Installation

```
$ git clone git://github.com/josephj/dataurize.git ~/dataurize
$ sudo mv ~/dataurize/dataurize /usr/bin/
$ sudo chmod +x /usr/bin/dataurize
```

## Syntax

```
Usage: dataurize <input> <base> [options]

        -o
        --output=<output>    Assign an output file. By default it overwrites your original file.
        --print              Print output directly instead of generating or overwriting a file.
                             By default, this option is disabled.
        --separate=<output>  Separate MHTML to a single file. By default, this option is disabled.
        --no-mhtml           Don't enable MHTML. Use this option if you don't want to use MHTML for legend IE.
                             It however doesn't hurt browser compatibility by adding *background-image(<Original Image Path>).
                             By default, this option is disabled.
        --size-limit=<bytes> It might still damage website performance if you transform an image with large file size.
                             The default file size is 1024 bytes.
        -h
        --help               Show this help.

    Sample: ./dataurize foo.css http://bar.com/ --output=foo2.css
```

## Note

Currently all CSS files in miiiCasa.com are *dataurized* by this script before deployment. Our best practice is not converting images which is larger than 5 KB.
Or it causes CSS file size becomes too large to be affordable.

```
$ dataurize <input> <base> --size-limit=5120
```
