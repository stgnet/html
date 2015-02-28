<?php

$contents=<<<END
<html>
  <body>
    <h3>HTML Editor</h3>
    <p>Hello World!</p>
  </body>
</html>
END;

if (!empty($_POST['file'])) {
	$contents=file_get_contents($_POST['file']);
}

class SortedIterator extends SplHeap
{
	public function __construct(Iterator $iter)
	{
		foreach ($iter as $item)
			$this->insert($item);
	}
	public function compare($b, $a)
	{
		return strcmp($a->getRealpath(), $b->getRealpath());
	}
}

$files = new SortedIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(".", RecursiveDirectoryIterator::FOLLOW_SYMLINKS)));

$form = '<form class="close" align="center" target="base" method="post"><select id="file" name="file">';
foreach ($files as $file)
{
	//$form.="<option value=\"$path\">$path</option>";
	$path=substr($file,2);
	if (substr($file, -5) == '.html')
		$form.="<option value=\"$path\">$path</option>";
}
$form.= '<input type="submit" id="load" value="Load"></form>';

$topframe=<<<END
<html class="expand close">
	<head>
		<style type="text/css">
			.expand { width: 100%; height: 100%; }
			.close { border: none; margin: 0px; padding: 0px; }
			html,body { overflow: hidden; }
		<\/style>
	<\/head>
	<body class="expand close" onload="document.f.ta.focus(); document.f.ta.select();">
		$form
		<form class="expand close" name="f">
			<textarea class="expand close" style="background: #def;" name="ta" wrap="hard" spellcheck="false">
			<\/textarea>
		<\/form>
	<\/body>
<\/html>
END;

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Real-time HTML Editor</title>
		<base target="base">
		<script type="text/javascript">

var editboxHTML = '<?php echo str_replace(array("\n", "\t"), "", $topframe); ?>';

var defaultStuff = <?php echo json_encode($contents); ?>;


var old = '';

function init()
{
  window.editbox.document.write(editboxHTML);
  window.editbox.document.close();
  window.editbox.document.f.ta.value = defaultStuff;
  update();
}

function update()
{
  var textarea = window.editbox.document.f.ta;
  var d = dynamicframe.document;

  if (old != textarea.value) {
    old = textarea.value;
    d.open();
    d.write(old);
    d.close();
  }

  window.setTimeout(update, 150);
}

		</script>
	</head>
	<frameset resizable="yes" rows="50%,*" onload="init();">
		<!-- about:blank confuses opera, so use javascript: URLs instead -->
		<frame name="editbox" src="javascript:'';">
		<frame name="dynamicframe" src="javascript:'';">
	</frameset>
</html>
