<?php
namespace Nexus\Helpers;

/**
* a class to return standard chunks of text
*
**/

class BoilerplateHelper
{
    public static function formattingHelp()
    {

        $helpText = <<<TEXT
<strong>Basics</strong>
<pre>
some **bold** text
</pre>
<pre>
some _italics_
</pre>
<pre>
> some quoted text
</pre>
<hr/>
<strong>Spoilers</strong>
<pre>
[spoiler-]he's a ghost[-spoiler]
</pre>
<hr/>
<strong>Links</strong>
<pre>
Here is a link [click here](https://nexus5.org.uk).
</pre>
or just paste in the address and it will be clickable
<hr/>
<strong>Images</strong>
<pre>
![Look a picture](http://example.com/picture.jpg)
</pre>
or
<pre>
[picture-]http://example.com/picture.jpg[-picture]
</pre>
<hr/>
<strong>Lists</strong>
<pre>
Friends:

- Chandler Bing
- Joey Tribbiani
- Monica Geller
- Phoebe Buffay
- Rachel Green
- Ross Geller
</pre>
TEXT;

        return $helpText;

    }
}
