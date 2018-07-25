<?php
	date_default_timezone_set("America/Toronto");
	include "GIFEncoder.class.php";
	include "hex2rgb.php";
	
	// Query string variables
	$time = filter_input(INPUT_GET, "time");
	$hexcolor = filter_input(INPUT_GET, "color") ? filter_input(INPUT_GET, "color") : "000000";
	$hexbg = filter_input(INPUT_GET, "bg") ? filter_input(INPUT_GET, "bg") : "ffffff";
	$fontsize = filter_input(INPUT_GET, "fontsize") ? filter_input(INPUT_GET, "fontsize") : "30";
	$fontfile = filter_input(INPUT_GET, "fontname") ? filter_input(INPUT_GET, "fontname") : "arial.ttf";
	
	$options=array('options'=>array('default'=>60, 'min_range'=>60, 'max_range'=>120));
	$framecount=filter_input(INPUT_GET, 'frames', FILTER_VALIDATE_INT, $options);
	
	$fontname = __DIR__ . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . $fontfile;

	$color = hex2RGB($hexcolor, true, ",");
	$bg = hex2RGB($hexbg, true, ",");
	
	list($red, $green, $blue) = explode(",", $color);
	list($bgred, $bggreen, $bgblue) = explode(",", $bg);

	$future_date = new DateTime(date("r",strtotime($time)));
	$time_now = time();
	$now = new DateTime(date("r", $time_now));

	$frames = array();	
	$delays = array();

	$interval = date_diff($future_date, $now);

	if($future_date < $now){
            $text = $interval->format("00     00     00     00");
	} else {
            $text = $interval->format("%a     %H     %I     %S");
            if(preg_match("/^[0-9]\s/", $text)){
		$text = "0".$text;
            }
	}
	
	// Create our bounding box for the text
	$box = imagettfbbox($fontsize, 0, $fontname, $text);
	
	// Measure the text
	$textwidth = abs($box[4] - $box[0]) + 5;
	$textheight = abs($box[5] - $box[1]);
	
	$delay = 100;// milliseconds
	
	$image = imagecreatetruecolor($textwidth, $textheight);
	$textcolor = imagecolorallocate($image, $red, $green, $blue);
	
	$font = [
		"size" => $fontsize, // Font size, in pts usually.
		"angle" => 0, // Angle of the text
		"x-offset" => 0, // Usually 0 to align to the left.
		"y-offset" => $textheight, // The vertical alignment, usually the same as the font size.
		"file" => $fontname, // Font path
		"color" => $textcolor, // RGB Colour of the text
		"textwidth" => $textwidth,
		"textheight" => $textheight,
	];

	imagedestroy($image);
	
	for($i = 0; $i <= $framecount; $i++){
		
		$interval = date_diff($future_date, $now);
		
		if($future_date < $now){
			// Create the first source image and add the text.
			$image = imagecreatetruecolor($font["textwidth"], $font["textheight"]);
			$background = imagecolorallocate($image, $bgred, $bggreen, $bgblue);
			imagefill($image, 0, 0, $background);

			$text = $interval->format("00     00     00     00");

			imagettftext($image, $font["size"], $font["angle"], $font["x-offset"], $font["y-offset"], $font["color"], $font["file"], $text);
			
			ob_start();
			imagecolortransparent($image, $background);
			imagegif($image);
			$frames[]=ob_get_contents();
			$delays[]=$delay;
			$loops = 1;
			ob_end_clean();
			break;
		} else {
			// Create the first source image and add the text.
			$image = imagecreatetruecolor($font["textwidth"], $font["textheight"]);
			$background = imagecolorallocate($image, $bgred, $bggreen, $bgblue);
			imagefill($image, 0, 0, $background);

			$text = $interval->format("%a     %H     %I     %S");
			// %a is weird in that it doesn't give you a two digit number
			// check if it starts with a single digit 0-9
			// and prepend a 0 if it does
			if(preg_match("/^[0-9]\s/", $text)){
				$text = "0".$text;
			}

			imagettftext($image, $font["size"], $font["angle"], $font["x-offset"], $font["y-offset"], $font["color"], $font["file"], $text);

			ob_start();
			imagecolortransparent($image, $background);
			imagegif($image);
			$frames[]=ob_get_contents();
			$delays[]=$delay;
			$loops = 0;
			ob_end_clean();
		}

		$now->modify("+1 second");
	}

	//expire this image instantly
	header( "Expires: Sat, 26 Jul 1997 05:00:00 GMT" );
	header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
	header( "Cache-Control: no-store, no-cache, must-revalidate" );
	header( "Cache-Control: post-check=0, pre-check=0", false );
	header( "Pragma: no-cache" );
	$gif = new AnimatedGif($frames,$delays,$loops);
	$gif->display();
