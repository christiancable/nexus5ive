/*
	written by chris wetherell
	http://www.massless.org
	chris [THE AT SIGN] massless.org
	
	warning: it only works for IE4+/Win and Moz1.1+
	feel free to take it for your site
	if there are any problems, let chris know.
*/
	
	
var thisForm;    /* make sure to change the onload handler of the
		    <body> tag to the form you're using!... */
	
	
function mozWrap(txtarea, lft, rgt) 
{
    var selLength = txtarea.textLength;
    var selStart = txtarea.selectionStart;
    var selEnd = txtarea.selectionEnd;
    if (selEnd==1 || selEnd==2) selEnd=selLength;
    var s1 = (txtarea.value).substring(0,selStart);
    var s2 = (txtarea.value).substring(selStart, selEnd)
	var s3 = (txtarea.value).substring(selEnd, selLength);
    txtarea.value = s1 + lft + s2 + rgt + s3;
}
	
function IEWrap(txtarea, lft, rgt) 
{
    strSelection = document.selection.createRange().text;
    if (strSelection!="") 
	{
	    document.selection.createRange().text = lft + strSelection + rgt;
	} 
    else
	{
	    txtarea.value = txtarea.value + lft + rgt;
	}
}
	
function wrapSelection(txtarea, lft, rgt) 
{
    if (document.all) 
	{
	    IEWrap(txtarea, lft, rgt);
	}
    else if (document.getElementById) 
	{
	    mozWrap(txtarea, lft, rgt);
	}
}

function wrapSelectionWithLink(txtarea) 
{
    var my_link = prompt("Enter URL of Page:","http://");
    if (my_link != null) 
	{
	    lft="[WWW-]" + my_link;
	    rgt="[-WWW]";
	    wrapSelection(txtarea, lft, rgt);
	}
    return;
}

function wrapSelectionWithImgLink(txtarea) 
{
    var my_link = prompt("Enter URL of Image:","http://");
    if (my_link != null) 
	{
	    lft="[PICTURE-]" + my_link;
	    rgt="[-PICTURE]";
	    wrapSelection(txtarea, lft, rgt);
	}
    return;
}	

/*  chris w. script */

function wrapSelectionWithYouTube(txtarea) 
{
    var my_link = prompt("Enter URL of Video:","http://");
    if (my_link != null) 
	{
	    lft="[YOUTUBE-]" + my_link;
	    rgt="[-YOUTUBE]";
	    wrapSelection(txtarea, lft, rgt);
	}
    return;
}


