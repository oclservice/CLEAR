<?php
include_once('simplehtmldom/simple_html_dom.php');
/*
Changed by Galina June23,2020 changed file access from URL to local, because of error "failed to open stream: Connection timed out"
$html = file_get_html('https://www.ocls.ca/virtualhelp/coll_template.html');
*/
$html = file_get_html('coll_template.html');

if(isset($_GET["college"])){

	$college = $_GET["college"];

	$elem = $html->find("div[id=" . $college . "]", 0);
	$library = $elem->find('span[class=library-name]',0)->innertext;
	$librarylink = $elem->find('span[class=library-link]',0)->innertext;

	if($college === "boreal" || $college === "lacite"){

	    $contactprefix = $html->find('span[id=contact-prefix-fre]', 0)->innertext;
	    $title = $contactprefix . lcfirst($library);
		$logo = $html->find('img[id=vh-logo-fre]', 0);

		$message = $html->find('div[id=message-nochat-fre]', 0);
		$contacthead = $html->find('h2[id=contact-head-fre]', 0);
		$linklabel = $html->find('span[id=website-label-fre]', 0)->innertext;
		$hours = "";
		$footer = $html->find('div[id=vh-footer-fre]', 0);

	}else{

	    $contactprefix = $html->find('span[id=contact-prefix-eng]', 0)->innertext;
	    $title = $contactprefix . $library;
		$logo = $html->find('img[id=vh-logo-eng]', 0);

		$message = $html->find('div[id=message-askon-eng]', 0);
		$contacthead = $html->find('h2[id=contact-head-eng]', 0);
		$linklabel = $html->find('span[id=website-label-eng]', 0)->innertext;
		$hours = $html->find('div[id=askon-hours-eng]', 0);
		$footer = $html->find('div[id=vh-footer-eng]', 0);

	}

}


?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
<link rel="stylesheet" href="vh-offline.css">
<title><?php echo $title;?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

	<div id="vh-header">
		<div class="vh-logo">
			<?php echo $logo; ?>
		</div>
		<div>
			<h1 class="vh-title"><?php echo $title;?></h1>
		</div> 
	</div>

	<?php 
		echo $message;
		echo $contacthead;
		echo $elem;
	?>

	<div class="website-link">
        <a href="<?php echo $librarylink; ?>" target="_blank"><?php echo $linklabel; ?>
        <i class="fa fa-external-link" aria-hidden="true"></i>
        </a>
    </div>

    <?php 
		echo $hours;
		echo $footer;
	?>

</body>
</html>
