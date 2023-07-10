<script src="https://code.jquery.com/jquery-1.12.4.min.js">
</script>
<style type="text/css">
	iframe {
		width: 500px;
		border: 1px solid #000000;
	}
</style>
<h1 style="color:green;">
	GeeksForGeeks
</h1>
<h3>How to insert HTML content
	into an iFrame using jQuery</h3>

<h4>Text to be insert : "GEEKSFORGEEKS
	- A computer science portal for geeks."</h4>
<iframe style="text-align:center;" id="iframe1" src="https://yomovies.hair/guardians-of-the-galaxy-vol-3-2023-Watch-online-full-movie/">
</iframe>
<script>
	$("#iframe1").contents().find("#main").remove();
	// document.documentElement.innerHTML = '<iframe id="frame"></iframe>';
/*var frame = document.getElementById('iframe').contentWindow.document;
frame.open();
frame.write('<html><body>It Works!<a class="lnk-lnk lnk-1" href="https://speedostream.pm/60kiaoy3ru40.html" target="_blank">adad</a></body></html>');
frame.close();*/
</script>
