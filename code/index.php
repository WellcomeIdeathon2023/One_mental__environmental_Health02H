<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>One-Health-02H</title>
	<link rel="shortcut icon" type="image/x-icon" href="images/uol.ico" />
	<!--
		CREDITS:
		https://getbootstrap.com/docs/4.1/getting-started/download/
		https://colorbrewer2.org/
		https://github.com/Leaflet/Leaflet.markercluster
		https://leafletjs.com/
-->
    <link rel="stylesheet" href="css/leaflet.css"/>
	<link rel="stylesheet" href="css/app.css"/>
	<link rel="stylesheet" href="css/MarkerCluster.css"/>
	<link rel="stylesheet" href="css/MarkerCluster.Default.css"/>
	
    <script src="js/leaflet.js" ></script>	
	<script src="js/leaflet.markercluster.js" ></script>
</head>
<body>
<?php
// Check if the user is login
	session_start();
	$user_login=false;
	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
		$user_login=true;
	}

	//ini_set('display_errors', 1); 
	//ini_set('display_startup_errors', 1); 
	//error_reporting(E_ALL);

	require_once "data_conn.php";
	
	// get the data from the database using SELECT
	// consider the user selection from form as a filter
	$metric = "";
	
	//==========================================================
	// Get the year, pollutant and health filters post via form
	//===========================================================
	// 1. Which start and end year options were selected
	if(isset($_POST["start_year"]) && isset($_POST["end_year"]))
	{
		$start_year = $_POST["start_year"];
		$end_year = $_POST["end_year"];
	}
	else
	{
		$start_year = "Select Start Year";
		$end_year = "Select End Year";
	}
	
	// 2. Which pollutant option was selected
	if(isset($_POST["pollutant"]))
	{
		$pollutant = $_POST["pollutant"];
	}
	else
	{
		$pollutant = "methane";
	}
	
	// 3. Which health option was selected
	$health_option = "";	
	if (isset($_POST["health_code"]))
	{
		$health_option = $_POST["health_code"];
	}

	 // ================================================
	// Generate daily alerts based on assumed daily feed
	// =================================================
	// Alert Markers
	if(!(isset($_POST["start_year"]) && isset($_POST["end_year"])) || $_POST["start_year"]=="Select Start Year" || $_POST["start_year"]=="Select End Year")
	{
		// This is when no filter options are selected
		// The display should be based on the daily alerts
		include_once("daily_pollutants.php");
		include_once("daily_alerts.php");
		// TODO: healths markers from user uploaded data
		include_once("user_health_markers.php");
	}
	else{
		// this script is used when filter values are selected
		include_once("pollutants.php");
		include_once("health_markers.php");

	}
	
	// Populate the health codes
	include_once("health_codes.php");	
	
?>
<div class="display-table">
	<div id='map'></div>

	<?php
		include_once("filter_navigation.php");
	?>
</div>


<script type="text/javascript">

	//The GeoJSON data prep by php
	var statesData = <?php echo $geojsonData; ?>

	// register the icons
	const greenIcon = L.icon({
		iconUrl: "images/green_icon.png",
		iconSize: [16, 24]
	});
	
	const blueIcon = L.icon({
		iconUrl: "images/blue_icon.png",
		iconSize: [16, 24]
	});

	const redIcon = L.icon({
		iconUrl: "images/red_alert_icon.gif",
		iconSize: [32, 32]
	});

	// Health Markers
	var markers = L.markerClusterGroup();
	<?php echo $health_markers; ?>
    const health = L.layerGroup([markers]);

    // Alert Marks
	<?php //$alert_markers = "L.marker([32.8067, -86.7911], {icon: redIcon}).bindPopup('This is Crown Hill Park.')" .","."L.marker([39.75, -105.09], {icon: redIcon}).bindPopup('This is Crown Hill Park.'),"?>
	//const alert1 = L.marker([32.8067, -86.7911], {icon: redIcon}).bindPopup('This is Crown Hill Park.');
    //const alert2 = L.marker([39.75, -105.09], {icon: redIcon}).bindPopup('This is Crown Hill Park.');
    const alerts = L.layerGroup([<?php echo $alert_markers; ?>]);

    // Map tile
    const osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    });

    // Map
    const map = L.map('map', {
        center: [39.73, -104.99],
        zoom: 4,
        layers: [osm, health, alerts]
    });



	// Show the state information on hover
	const info = L.control();
	info.onAdd = function (map) {
		this._div = L.DomUtil.create('div', 'info');
		this.update();
		return this._div;
	};
	
	//Write the information into the.info div and add it to the map.
	info.update = function (props) {
		const contents = props ? `<b>State:</b> ${props.name}</b><br /><b>Pollutant:</b> <?php echo ucwords($pollutant); ?> - ${props.weather_value} <?php echo $metric; ?>` : 'Hover over a state';
		this._div.innerHTML = `<h4>Pollution and Health</h4>${contents}`;
	};

	info.addTo(map);

	// HTML colour codes were generated using ColorBrewer(https://colorbrewer2.org/)
	// the colour depending on the decile (UV/NOx/Methane). Then Overlay the health cases..
	function getColor(d) {
		<?php echo $color_gen; ?>
	}

	function style(feature) {
		return {
			weight: 2,
			opacity: 1,
			color: 'white',
			dashArray: '3',
			fillOpacity: 0.7,
			fillColor: getColor(feature.properties.weather_value) // feature.properties.colour
		};
	}

	function highlightFeature(e) {
		const layer = e.target;

		layer.setStyle({
			weight: 5,
			color: '#666',
			dashArray: '',
			fillOpacity: 0.7
		});

		layer.bringToFront();

		info.update(layer.feature.properties);
	}

	/* global statesData */
	const geojson = L.geoJson(statesData, {
		style,
		onEachFeature
	}).addTo(map);

	function resetHighlight(e) {
		geojson.resetStyle(e.target);
		info.update();
	}

	function zoomToFeature(e) {
		map.fitBounds(e.target.getBounds());
	}

	function onEachFeature(feature, layer) {
		layer.on({
			mouseover: highlightFeature,
			mouseout: resetHighlight,
			click: zoomToFeature
		});
	}

	map.attributionControl.addAttribution('Pollution Data &copy; <a href="https://data.carbonmapper.org/">Carbon Mapper</a>, One-Health-02H, University of Lincoln.');

	const legend = L.control({position: 'bottomleft'});

	// to correct scale for UVI remove this function in to individual pollutants and daily pollutants php files 
	// and echo results here*****************
	legend.onAdd = function (map) {

		const div = L.DomUtil.create('div', 'info legend');
		const grades = [<?php echo $deciles[0] ?>, <?php echo $deciles[1] ?>, 
		<?php echo $deciles[2] ?>, <?php echo $deciles[3] ?>, <?php echo $deciles[4] ?>, 
		<?php echo $deciles[5] ?>, <?php echo $deciles[6] ?>, <?php echo $deciles[7] ?>,
		<?php echo $deciles[8] ?>, <?php echo $deciles[9] ?>];
		const labels = [];
		let from, to;
		labels.push(`<h4><?php echo $pollutant." ("; ?><?php echo $metric.")"; ?></h4>`);
		
		for (let i = 0; i < grades.length; i++) {
			from = grades[i];
			to = grades[i + 1];

			labels.push(`<i style="background:${getColor(from)}"></i> ${from}${to ? `&ndash;${to}` : '+'}`);
		}

		

		div.innerHTML = labels.join('<br>');
		return div;
	};

	legend.addTo(map);
	
	
	const baseLayers = {
        'OpenStreetMap': osm
    };

    const overlays = {
        'Health': health,
        'Alerts': alerts
    };

    const layerControl = L.control.layers(baseLayers, overlays, {collapsed:false}).addTo(map);
	

	
</script>

<?php
	$conn->close();	
?>


</body>
</html>
