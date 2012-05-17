<?php

$path = dirname(dirname(__FILE__)) . '/4411/epi/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

include_once 'Epi.php';
Epi::setPath('base', 'epi');
Epi::setPath('config', dirname(__FILE__));
Epi::init('route');
Epi::init('database');
EpiDatabase::employ('mysql', 'parking', 'localhost', 'parking', 'password');



getRoute()->get('/GetMeter/([-+]?[0-9]*\.?[0-9]+)/([-+]?[0-9]*\.?[0-9]+)', 'getMeter');
getRoute()->get('/GetMeterId/(.*)', 'getMeterId');
getRoute()->get('/GetZoneId/(.*)', 'getZoneId');
getRoute()->get('/Login/(.*)/(.*)', 'login');
getRoute()->get('/RunningSessions/(.*)/(.*)', 'runningSessions');
getRoute()->get('/StartParking/(.*)/(.*)/(.*)/(.*)/(.*)', 'startParking');
getRoute()->get('/StopParking/(.*)/(.*)/(.*)/(.*)', 'stopParking');
getRoute()->get('/GetAPIServer', 'apiServer');

getRoute()->get('/ImportMeters', 'importMeters');
getRoute()->get('/ImportMeterIds', 'importMeterIds');
getRoute()->get('/ImportZones', 'importZones');
getRoute()->get('/', 'about');
getRoute()->run();

function about() {
?>
<h1>Parking 4411 API</h1>
<p>Deze pagina beschrijft de beschikbare RESTful API's om te parkeren met het 4411 systeem.  De API laat ook toe om de dichtsbijzijnde meter op te vragen gegeven een GPS locatie (maximum afstand van de meter tot de GPS locatie is 500m).</p>
<p>Deze API bevindt zich nog in alpha-stage.  Commentaar meer dan welkom!</p>
<p>De API komt - zodra stabiel - eveneens op Github.</p>
<h2>Privacy/Security</h2>
<p>Achterliggend verbindt de API met 4411 via een SSL verbinding.  De credentials worden nergens op de NetwalkApps site opgeslagen, maar worden enkel gebruikt om in te loggen op https://4411.be.</p>
<h2>API</h2>
<h3>GetMeter</h3>
<p>De GetMeter API verwacht 2 argumenten: latitude en longitude, waarbij het 'komma'-gedeelte gescheiden wordt door een punt (.).</br>
Voorbeeld: https://api.netwalkapps.com/4411/GetMeter/51.005695/3.8819</p>
<ul>
<li>Latitude - bvb 51.005695</li>
<li>Longitude - bvb 3.8819</li>
</ul>


<h3>GetAPIServer</h3>
<p>De GetAPIServer geeft de URL terug die kan gebruikt worden om onderstaande API's uit te voeren.  In geval Mobile-For een IP blockt, kan een andere server opgezet worden.</br>
Voorbeeld: https://api.netwalkapps.com/4411/GetAPIServer</p>

<h3>GetMeterId</h3>
<p>De GetMeterId API verwacht 1 argument: het meter nummer zoals getoond op de fysieke meter.</br>
Voorbeeld: https://api.netwalkapps.com/4411/GetMeterId/WET12</p>
<ul>
<li>Meter Nummer - Meter nummer zoals getoond op de fysieke meter.  Bvb WET12</li>
</ul>

<h3>GetZoneId</h3>
<p>De GetZoneId API verwacht 1 argument: de zone nummer/string van de zone. Dit is meestal van de vorm LET123.</br>
Voorbeeld: https://api.netwalkapps.com/4411/GetZoneId/WET1</p>
<ul>
<li>Zone Nummer - Bvb WET1</li>
</ul>

<h3>Login</h3>
<p>Login op het 4411 systeem.  Indien de login lukt, wordt een lijst van telefoons en nummerplaten teruggegeven.  De API verwacht 2 argumenten, gebruikersnaam en paswoord</br>
<ul>
<li>Gebruikersnaam - Telefoonnummer, bvb 32497403832</li>
<li>Wachtwoord - Zoals geconfigureerd op de 4411 website</li>
</ul>
Voorbeeld: https://api.netwalkapps.com/4411/Login/Username/Password</p>

<h3>RunningSessions</h3>
<p>Vraag de actieve parkeersessie op.  De API verwacht 2 argumenten, gebruikersnaam en paswoord.  Geeft een dictionary terug, waarbij de <code>parking</code> key 0 teruggeeft indien geen actieve parkeersessie, 1 indien wel actief.
Indien een actieve parkeersessie gevonden is, wordt eveneens <code>sessionId</code> en <code>phoneId</code> teruggegeven.</br>
<ul>
<li>Gebruikersnaam - Telefoonnummer, bvb 32497403832</li>
<li>Wachtwoord - Zoals geconfigureerd op de 4411 website</li>
</ul>
Voorbeeld: https://api.netwalkapps.com/4411/RunningSessions/Username/Password</p>

<h3>StartParking</h3>
<p>Start een parkeersessie.  De API verwacht 5 argumenten, gebruikersnaam, paswoord, meter (of zone) id, telefoon id en nummerplaat id.  Geeft een dictionary terug, waarbij de <code>status</code> key 0 teruggeeft indien parkeren mislukt, 1 indien gelukt.</br>
<ul>
<li>Gebruikersnaam - Telefoonnummer, bvb 32497403832</li>
<li>Wachtwoord - Zoals geconfigureerd op de 4411 website</li>
<li>Meter Id - Zoals teruggegeven door GetMeter</li>
<li>Telefoon Id - Zoals teruggegeven door Login</li>
<li>Nummerplaat Id - Zoals teruggegeven door Login</li>
</ul>
Voorbeeld: https://api.netwalkapps.com/4411/StartParking/Username/Password/MeterId/PhoneId/PlateId</p>

<h3>StopParking</h3>
<p>Stopt een bestaande parkeersessie.  De API verwacht 4 argumenten, gebruikersnaam, paswoord, sessie id en telefoon id.  Geeft een dictionary terug, waarbij de <code>status</code> key 0 teruggeeft indien stop parkeren is mislukt, 1 indien gelukt.</br>
<ul>
<li>Gebruikersnaam - Telefoonnummer, bvb 32497403832</li>
<li>Wachtwoord - Zoals geconfigureerd op de 4411 website</li>
<li>Session Id - Zoals teruggegeven door RunningSessions</li>
<li>Telefoon Id - Zoals teruggegeven door RunningSessions of Login</li>
</ul>
Voorbeeld: https://api.netwalkapps.com/4411/StopParking/Username/Password/SessionId/PhoneId</p>

<?php
}

function apiServer() {
	print "https://api.netwalkapps.com/4411";
}

//
// get nearest meter
//
function getMeter($lat, $long) {
	header('Content-Type:application/json');
	$sql = "SELECT X(location) AS latitude, Y(location) AS longitude, street, meter, meterId, zone, DISTANCE(LOCATION, GEOMFROMTEXT('POINT($lat $long)')) AS dist FROM meter HAVING dist < 0.5 ORDER BY dist";
	$ret = getDatabase()->one($sql);
	print json_encode($ret);
}


//
// get meter id
//
function getMeterId($meter) {
	header('Content-Type:application/json');
	$sql = "SELECT X(location) AS latitude, Y(location) AS longitude, street, meter, meterId, zone FROM meter WHERE meter = '$meter'";
	$ret = getDatabase()->one($sql);
	print json_encode($ret);
}


//
// get zone id
//
function getZoneId($zone) {
	header('Content-Type:application/json');
	$sql = "SELECT zone, zoneId, city FROM zone WHERE zone = '$zone'";
	$ret = getDatabase()->one($sql);
	print json_encode($ret);
}

function startParking($username, $password, $meterOrZoneId, $phoneId, $plateId) {
	header('Content-Type:application/json');
	$cookie = '/dev/null/';
	{
		$ch = curl_init();

		// first get csrf
		curl_setopt($ch, CURLOPT_URL, 'https://www.4411.be/nl');
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, "https://www.4411.be/nl"); 
		$output = curl_exec($ch);
		$startPos = stripos($output, "value=\"", stripos($output, "\"csrf\""));
		$endPos = stripos($output, "\"", $startPos + 7);
		$csrf = substr($output, $startPos + 7, $endPos - $startPos - 7);

		$postData = array('username' => $username,  'password' => $password, 'csrf' => $csrf);
		foreach ( $postData as $key => $value) {
			$post_items[] = $key . '=' . $value;
		}
		
		$post_string = implode ('&', $post_items);
		curl_setopt($ch, CURLOPT_URL, 'https://www.4411.be/nl/aanmelden/');
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, "https://www.4411.be/nl"); 
		$output = curl_exec($ch);
	
		if (preg_match("/nl\/mijnpagina\/[0-9]*\/webparking/", $output, $matches))
		{
			$postData = array('parkingLocationId' => $meterOrZoneId,  'phoneId' => $phoneId, 'licensePlate' => $plateId, 'confirmationSms' => 0);
			foreach ( $postData as $key => $value)
				$post_items[] = $key . '=' . $value;
			$post_string = implode ('&', $post_items);
			curl_setopt($ch, CURLOPT_URL, "https://www.4411.be/" . $matches[0] . "/start-session/");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Requested-With:XMLHttpRequest'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			print curl_exec($ch);
			return;
		}
	}
}


function stopParking($username, $password, $parkingSessionId, $phoneId) {
	header('Content-Type:application/json');
	$cookie = '/dev/null/';
	{
		$ch = curl_init();

		// first get csrf
		curl_setopt($ch, CURLOPT_URL, 'https://www.4411.be/nl');
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, "https://www.4411.be/nl"); 
		$output = curl_exec($ch);
		$startPos = stripos($output, "value=\"", stripos($output, "\"csrf\""));
		$endPos = stripos($output, "\"", $startPos + 7);
		$csrf = substr($output, $startPos + 7, $endPos - $startPos - 7);

		$postData = array('username' => $username,  'password' => $password, 'csrf' => $csrf);
		foreach ( $postData as $key => $value) {
			$post_items[] = $key . '=' . $value;
		}
		
		$post_string = implode ('&', $post_items);
		curl_setopt($ch, CURLOPT_URL, 'https://www.4411.be/nl/aanmelden/');
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, "https://www.4411.be/nl"); 
		$output = curl_exec($ch);
	
		if (preg_match("/nl\/mijnpagina\/[0-9]*\/webparking/", $output, $matches))
		{
			$postData = array('parkingSessionId' => $parkingSessionId,  'phoneId' => $phoneId, 'confirmationSms' => 0);
			foreach ( $postData as $key => $value)
				$post_items[] = $key . '=' . $value;
			$post_string = implode ('&', $post_items);
			curl_setopt($ch, CURLOPT_URL, "https://www.4411.be/" . $matches[0] . "/stop-session/");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Requested-With:XMLHttpRequest'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			print curl_exec($ch);
			return;
		}
	}
}

function runningSessions($username, $password) {
	header('Content-Type:application/json');
	$cookie = '/dev/null/';
	{
		$ch = curl_init();

		// first get csrf
		curl_setopt($ch, CURLOPT_URL, 'https://www.4411.be/nl');
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, "https://www.4411.be/nl"); 
		$output = curl_exec($ch);
		$startPos = stripos($output, "value=\"", stripos($output, "\"csrf\""));
		$endPos = stripos($output, "\"", $startPos + 7);
		$csrf = substr($output, $startPos + 7, $endPos - $startPos - 7);

		$postData = array('username' => $username,  'password' => $password, 'csrf' => $csrf);
		foreach ( $postData as $key => $value) {
			$post_items[] = $key . '=' . $value;
		}
		
		$post_string = implode ('&', $post_items);
		curl_setopt($ch, CURLOPT_URL, 'https://www.4411.be/nl/aanmelden/');
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, "https://www.4411.be/nl"); 
		$output = curl_exec($ch);
	
		if (preg_match("/nl\/mijnpagina\/[0-9]*\/webparking/", $output, $matches))
		{
			curl_setopt($ch, CURLOPT_URL, "https://www.4411.be/" . $matches[0] . "/running-sessions");
			curl_setopt($ch, CURLOPT_HTTPGET, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Requested-With:XMLHttpRequest'));
			$output = curl_exec($ch);
			//print $output;
			
			$parked = FALSE;
			if (preg_match("/session_[0-9]*/", $output, $match))
			{
				$parked = TRUE;
				$sessionId = substr($match[0], 8);
				
				if (preg_match("/phone_[0-9]*/", $output, $match))
				{
					$phoneId = substr($match[0], 6);
					print json_encode(array('sessionId' => $sessionId, 'phoneId' => $phoneId, 'parked' => 1));
					return;
				}
			}
		}
	}
	print json_encode(array('parked' => 0));
}

//
// login, required info is saved in PHP session
//
function login($username, $password) {
	header('Content-Type:application/json');
	$cookie = '/dev/null/';
	{
		$ch = curl_init();

		// first get csrf
		curl_setopt($ch, CURLOPT_URL, 'https://www.4411.be/nl');
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, "https://www.4411.be/nl"); 
		$output = curl_exec($ch);
		$startPos = stripos($output, "value=\"", stripos($output, "\"csrf\""));
		$endPos = stripos($output, "\"", $startPos + 7);
		$csrf = substr($output, $startPos + 7, $endPos - $startPos - 7);

		$postData = array('username' => $username,  'password' => $password, 'csrf' => $csrf);
		foreach ( $postData as $key => $value) {
			$post_items[] = $key . '=' . $value;
		}
		
		$post_string = implode ('&', $post_items);
		curl_setopt($ch, CURLOPT_URL, 'https://www.4411.be/nl/aanmelden/');
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, "https://www.4411.be/nl"); 
		$output = curl_exec($ch);
	
		if (preg_match("/nl\/mijnpagina\/[0-9]*\/webparking/", $output, $matches))
		{
			curl_setopt($ch, CURLOPT_URL, "https://www.4411.be/" . $matches[0]);
			curl_setopt($ch, CURLOPT_HTTPGET, 1);
			$output = curl_exec($ch);

			// find license plates
			$plates = array();
			$phones = array();
			$startPos = stripos($output, "id=\"licenseplate\"");
			$endPos = stripos($output, '</select>', $startPos);
			$str = substr($output, $startPos, $endPos - $startPos);
			if (preg_match_all("/option value=\"[0-9]*/", $str, $matches, PREG_OFFSET_CAPTURE))
			{
				foreach($matches[0] as $match)
				{
					$plateId = substr($match[0], 14);
					$start = stripos($str, ">", $match[1]);
					$end = stripos($str, "</option>", $start);
					$plateName = substr($str, $start + 1, $end - $start - 1);
					$plates[$plateName] = $plateId;
				}
			}
						
			// find phone numbers
			$startPos = stripos($output, "id=\"phone_id\"");
			$endPos = stripos($output, '</select>', $startPos);
			$str = substr($output, $startPos, $endPos - $startPos);
			if (preg_match_all("/option value=\"[0-9]*/", $str, $matches, PREG_OFFSET_CAPTURE))
			{
				foreach($matches[0] as $match)
				{
					$phoneId = substr($match[0], 14);
					$start = stripos($str, ">", $match[1]);
					$end = stripos($str, "</option>", $start);
					$phoneNr = substr($str, $start + 1, $end - $start - 1);
					$phones[$phoneNr] = $phoneId;
				}
			}
			
			print json_encode(array('plates' => $plates,  'phones' => $phones, 'success' => 1));
			curl_close($ch);
			return;
		}
	}
	
	print json_encode(array('success' => 0));
}


// internal - import all meters
function importMetersFromFile($handle) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
    	$row++;

		getDatabase()->execute("INSERT INTO meter(location, street, meter, zone) VALUES(GEOMFROMTEXT('POINT(" . $data[0] . " " . $data[1] . ")'), '" . $data[2] . "', '" . $data[3] ."', '')");

    }
}

// internal - import all meters
function importMeters() {

	if ($handle = opendir('import')) {
		while (false !== ($entry = readdir($handle))) {
			if (($fileHandle = fopen('import/' . $entry, "r")) !== FALSE) 
			{
				importMetersFromFile($fileHandle);
			    fclose($fileHandle);
			}
 	   	}		
	}

}


function importMeterIds() {

	if (($fileHandle = fopen('import/meterids.txt', "r")) !== FALSE) 
	{
	    while (($data = fgetcsv($fileHandle, 1000, "-")) !== FALSE) {
    	    $num = count($data);
    		$row++;

			getDatabase()->execute("UPDATE meter SET meterId = '" . $data[0] . "' WHERE meter = '" . $data[1] . "'");

    }
	    fclose($fileHandle);
	}

}

function importZones() {

	if (($fileHandle = fopen('import/zones.txt', "r")) !== FALSE) 
	{
	    while (($data = fgetcsv($fileHandle, 1000, "-")) !== FALSE) {
    	    $num = count($data);
    		$row++;

			getDatabase()->execute("INSERT INTO zone(zoneId, zone, city) VALUES('" . $data[0] . "','" . $data[1] . "','" . $data[2] . "')");

    }
	    fclose($fileHandle);
	}

}

?>