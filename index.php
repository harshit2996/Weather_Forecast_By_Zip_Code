
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="weather.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.4.0/leaflet.css">
	<link rel="icon" href="icons/w-icon.gif" type="image/gif" sizes="32x32">
	<title>Weather Forecast</title>
</head>
<body>
	
	<!-- form Section for User to Enter the Zip Code or Post Code -->
	<div id="zip"><form action="" method="POST" >
		<label><strong>Post Code:</strong></label><input type="text" name="zip" required="true"  placeholder="Enter Zip Code">
		<button type="submit">Submit</button></form></div>
		
	<?php

		// function to check if url is present, i.e., to check if the api returns the request or not
		function _checkFileExists($url){
			$headers = @get_headers($url);
			if($headers[0] == 'HTTP/1.1 404 Not Found') {
				return false;
			}else{
				return true;
			}
		} 
		// function to check if connected to internet or not as the apis are used to return data
		function check_internet_connection() {
			$sCheckHost="www.google.com";
			return (bool) @fsockopen($sCheckHost, 80, $iErrno, $sErrStr, 5);
		}
		
		// testing if connected to the internet or not
		$test = check_internet_connection(); 
		
		// main code for weather forecast
		if ($test){
			
			if (isset($_POST['zip'])){
				$zip_code=$_POST['zip']; 
				// using openweathermap api to get the weather information please use your own api key
				$w_jsonurl="https://api.openweathermap.org/data/2.5/forecast/daily/?q=".$zip_code."&units=metric&APPID='Your AppId / API Key goes here'";
				
				if(_checkFileExists($w_jsonurl)){
					$w_json = file_get_contents($w_jsonurl);
					$json_obj = json_decode($w_json);
					$req=[$json_obj->list[0],$json_obj->list[1],$json_obj->list[2]];
					
					//using getziptastic api to get location information for map and to get area name for zip code				
					$region_info_url="https://zip.getziptastic.com/v2/".$json_obj->city->country."/".$zip_code;
					$r_json=file_get_contents($region_info_url);
					$r_json_obj = json_decode($r_json);

					echo '<div id="location"><h1>'.$r_json_obj->state.',  '.$r_json_obj->county.', '.$r_json_obj->city.'<h1></div>
						<div><p><strong>3-day forecast</strong></p></div>
						<div class="row">
					';
					// displaying weather forecast for the three days in card elements 
					foreach ($req as $day) {
						echo'
							<div id="w" class="card" >';
								
								$icon = $day->weather[0]->icon;
								$label= $day->weather[0]->main;
								$date= $day->dt;
								$max_temp= $day->temp->max;
								$min_temp= $day->temp->min;
								$src="icons/".$icon.".svg";
								echo '<div><img src='.$src.'></div>
								<div id="w-details">
									<div id="date">
										<p>'.date('Y-m-d',$date).'</p>
										<p>'.date('D',$date).'</p>
									</div>
									<h1>'. $label.'</h1>
									<div id="date">
										<p>Max: '.$max_temp.'&deg;C</p>
										<p>Min: '.$min_temp.'&deg;C</p>
									</div>
								</div>

							</div>
						';
					}

					//Map Section, (Photos associated with the Zip Code), Scripts Sections 
					echo '
						</div><div class="row">	
							
							<div id="fs">
								<div id="snaps">
									<div id="map"></div>
								</div>
								<div id="pic_sec"><strong>Location of post code '.$zip_code.' on Map</strong></div>
							</div>
							
							<div id="fs">';
								$query="https://pixabay.com/api/?key='Your Api Key'&q=".$r_json_obj->state."&image_type=photo";
								$json=file_get_contents($query);			
								echo '<script>console.log('.$json.')</script>';
								if($json){
									$json=json_decode($json);
									$hits=$json->hits;
									echo '<div id="snaps">'; 
									if(!empty($hits)){
										$x=0;
										while((!empty($hits[$x]))&&($x<3)) {
											$image_src=$hits[$x]->largeImageURL;
											echo'<div id="pic"><a href='.$image_src.' target="_blank"><img src='.$image_src.'></a></div>';
											$x=$x+1;
										} 	
									}
									else{
										echo "<div><strong> No images to show</strong></div>";
									}
									echo '</div>';
								}	
								else{
										echo "<div>No images to show</div>";
								}	
								echo '<div id="pic_sec"><strong>Snaps of '.$r_json_obj->state.' - '.$zip_code.' </strong></div>
							</div>

						</div>
						<script type="text/javascript">
							
							var lat = '.$json_obj->city->coord->lat.';
							var lon = '.$json_obj->city->coord->lon.';
							var place = "'.$r_json_obj->city.'";
							
						</script>
						<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
						<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.4.0/leaflet.js"> </script>
						<script type="text/javascript">var lat, lon,place;</script>
						<script src="maps.js"></script>
					';
				}
				else {
					echo '<div id="error"><strong>No City Found !</strong></div>';
				}
			}
		}

		else{
			echo '<div id="error"><strong>Please Check your Internet Connection and Refresh the Page</strong></div>';
		}

	?>
	
</body>
</html>


