<?php

function load_klaskodes( $parm="" ) {
    $resultsth = Klas::getInstance()->get_klas();
    
    while($row = $resultsth->fetch()) {
        echo "KK1=" . htmlentities($row["KK1"]) . "<br />";
        echo "KK2=" . htmlentities($row["KK2"]) . "<br />";
        echo "KK3=" . htmlentities($row["KK3"]) . "<br />";
    }
    
    $resultsth->closeCursor();
}

function load_activiteiten( $parm="" ) {
    $resultsth = Activiteit::getInstance()->get_activiteit();
    
    echo "Activiteiten: <br/>";
    
    while($row = $resultsth->fetch()) {
        echo htmlentities($row["ActiviteitNaam"]) . "<br />";
    }
    
    $resultsth->closeCursor();
}

function load_fiches( $parm="" ) {
    $resultsth = Fiche::getInstance()->get_fiche();
    
    echo "Fiches: <br/>";
    
    while($row = $resultsth->fetch()) {
        echo "FicheCode=" . htmlentities($row["FicheCode"]);
        echo " - FicheNaam=" . htmlentities($row["FicheNaam"]) . "<br />";
    }
    
    $resultsth->closeCursor();
}







function strip_zeros_from_date( $marked_string="" ) {
  // first remove the marked zeros
  $no_zeros = str_replace('*0', '', $marked_string);
  // then remove any remaining marks
  $cleaned_string = str_replace('*', '', $no_zeros);
  return $cleaned_string;
}

function redirect_to( $location = NULL ) {
  if ($location != NULL) {
    header("Location: {$location}");
    exit;
  }
}

function output_message($message="") {
  if (!empty($message)) { 
    return "<p class=\"message\">{$message}</p>";
  } else {
    return "";
  }
}

function __autoload($class_name) {
	$class_name = strtolower($class_name);
  $path = LIB_PATH.DS."{$class_name}.php";
  if(file_exists($path)) {
    require_once($path);
  } else {
		die("The file {$class_name}.php could not be found.");
	}
}

function include_layout_template($template="") {
	include(SITE_ROOT.DS.'public'.DS.'layouts'.DS.$template);
}

function log_action($action, $message="") {
	$logfile = SITE_ROOT.DS.'logs'.DS.'log.txt';
	$new = file_exists($logfile) ? false : true;
  if($handle = fopen($logfile, 'a')) { // append
    $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
		$content = "{$timestamp} | {$action}: {$message}\n";
    fwrite($handle, $content);
    fclose($handle);
    if($new) { chmod($logfile, 0755); }
  } else {
    echo "Could not open log file for writing.";
  }
}

function datetime_to_text($datetime="") {
  $unixdatetime = strtotime($datetime);
  return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}

?>