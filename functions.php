<?php

  // Display title of each markup samples as a list item
  function listFilesInFolder($dir) {
    $files = scandir($dir);
    sort($files);

    echo '<ul class="sg-nav-group">';
    foreach ($files as $file) {
      if ($file != '.' && $file != '..' && $file != '.DS_Store') {
        $path = $dir.'/'.$file;
        if (is_dir($path)) {
          echo '<li class="sg-subnav-parent">';
          renderTitleFromPath($path, 'h2');
          listComponentFilesInFolder($path);
          echo '</li>';
        }
      }
    }
    echo '</ul>';
  }

	// Display title of each component's subgroup markup samples as a list item
  function listComponentFilesInFolder($dir) {
    $files = scandir($dir);
    sort($files);

    echo '<ul class="sg-nav-group">';
    foreach ($files as $file) {
      if ($file != '.' && $file != '..' && $file != '.DS_Store') {
        $path = $dir.'/'.$file;
        if (is_dir($path)) {
          echo '<li>';
          renderComponentTitleFromPath($path);

					createComponentFile($path);

          //listFilesInFolder($path);
          echo '</li>';
				}
      }
    }
    echo '</ul>';
  }

  // Scan specified directory recursively and render files
  function renderFilesInFolder($dir) {
    $files = scandir($dir);
    sort($files);

    echo '<section class="sg-section-group">';
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $file != '.DS_Store') {
          $path = $dir.'/'.$file;
          if (is_dir($path)) {
            renderTitleFromPath($path, 'h1');
            renderFilesInFolder($path);
          } else {
            renderFile($path);
          }
        }
    }
    echo '</section>';
  }

  function renderTitleFromPath($path, $parent) {
    $unwantedChars = array("/", "-", "_", ".");
    $filename = pathinfo($path, PATHINFO_FILENAME); // filename without extension
    $filename = str_replace($unwantedChars, " ", $filename);
    $id = str_replace($unwantedChars, "-", $path);

    if ($parent) {
      echo '<'.$parent.' id="sg-'.$id.'" class="sg-'.$parent.' sg-title">'.$filename.'</'.$parent.'>';
    } else {
      echo '<a href="#sg-'.$id.'">'.$filename.'</a>';
    }
  }

	function renderComponentTitleFromPath($path, $parent) {
    $unwantedChars = array("/", "-", "_", ".");
    $filename = pathinfo($path, PATHINFO_FILENAME); // filename without extension
    $filename = str_replace($unwantedChars, " ", $filename);
    $id = str_replace($unwantedChars, "-", $path);

    if ($parent) {
      echo '<'.$parent.' id="sg-'.$id.'" class="sg-'.$parent.' sg-title">'.$filename.'</'.$parent.'>';
    } else {
      echo '<a href="'.$id.'.php">'.$filename.'</a>';
    }
  }

  function renderFile($path) {
    $content = file_get_contents($path);
    echo '<div class="sg-section">';
    renderTitleFromPath($path, 'h2');
    renderFileDoc($path);
    renderFileExample($content);
    renderFileSource($content);
    echo '</div>';
  }

  function renderFileDoc($path) {
    $documentation = 'doc'.strstr($path, "/");
    if (file_exists($documentation)) {
      echo '<div class="sg-sub-section sg-doc">';
      echo '<h3 class="sg-h3 sg-title">Usage</h3>';
      include($documentation);
      echo '</div>';
    }
  }

  function renderFileExample($content) {
    if ($content != '') {
      echo '<div class="sg-sub-section sg-example">';
      echo '<h3 class="sg-h3 sg-title">Example</h3>';
      echo $content;
      echo '</div>';
    }
  }

  function renderFileSource($content) {
    if ($content != '') {
      echo '<div class="sg-sub-section">';
      echo '<div class="sg-markup-controls">';
      echo '<button type="button" class="sg-btn sg-btn--source">View Source</button>';
      echo '<a class="sg-btn--top" href="#main">Back to Top</a>';
      echo '</div>';
      echo '<div class="sg-source">';
      echo '<button type="button" class="sg-btn sg-btn--select">Select Code</button>';
      echo '<pre class="line-numbers"><code class="language-markup">';
      echo htmlspecialchars($content);
      echo '</code></pre>';
      echo '</div>';
      echo '</div>';
    }
  }

	// create component files
	function createComponentFile($path) {
		$unwantedChars = array("/", "-", "_", ".");
		$dir = pathinfo($path, PATHINFO_DIRNAME); // filename without extension
		$dir = ucwords(end(explode("/", $dir)));

    $filename = pathinfo($path, PATHINFO_FILENAME); // filename without extension
    $filename = str_replace($unwantedChars, " ", $filename);
		$filename = ucwords($filename);

		$id = str_replace($unwantedChars, "-", $path);


//    if ($parent) {
//      echo '<'.$parent.' id="sg-'.$id.'" class="sg-'.$parent.' sg-title">'.$filename.'</'.$parent.'>';
//    } else {
//      echo '<a href="'.$id.'.php">'.$filename.'</a>';
//    }

		$newfile = "markup-" . strtolower($dir) . "-" . str_replace(" ", "-", strtolower($filename)) . ".php";

		if (!file_exists($newfile)) {
			$file = fopen($newfile, "w") or die("Unable to open file!");
			$txt  = '<?php include_once("sg-page-open.php"); ?>' . PHP_EOL . PHP_EOL;
			$txt .= '<h1 class="sg-h1">'. $dir . '/' . $filename .'</h1>' . PHP_EOL;
			$txt .= '<?php renderFilesInFolder("markup/' . strtolower($dir) . '/' . str_replace(" ", "-", strtolower($filename)) .'"); ?>' . PHP_EOL . PHP_EOL;
			$txt .= '<?php include_once("sg-page-close.php"); ?>' . PHP_EOL . PHP_EOL;
			fwrite($file, $txt);
			fclose($file);
		}
	}
?>
