<?php

$results = array();

//if (!isset($_GET["limit"])) {$_GET["limit"] = 25;}
//if ($_GET["limit"] > 100) {$_GET["limit"] = 100;}
//if (!isset($_GET["from"])) {$_GET["from"] = 0;}
//if (!isset($_GET["search"])) {$_GET["search"] = false;}
//if (!isset($_GET["tag"])) {$_GET["tag"] = "ng";}


if (!isset($_GET["limit"]))
  {$limit = 50;}
else
  {$limit = $_GET["limit"];}
	
if ($limit > 2000)
  {$limit = 2000;}
	
if (!isset($_GET["uri"]))
  {$uri = "https://research.ng-london.org.uk/iiif-projects/json/vol-35.json";}
else
  {$uri = $_GET["uri"];}
  
$arr = getsslJSONfile ($uri, true, false);
$list = array();

if (isset($arr["type"]) and strtolower($arr["type"]) == "collection")
	{$list = collectionToList (false, $arr, $list);}
else if (isset($arr["@type"]) and strtolower($arr["@type"]) == "sc:collection")
	{$list = collectionToList (false, $arr, $list);}
else
	{$list = manifestToList (false, $arr, $list);}
		
$total = count ($list);

if ($total > $limit)
  {$list = array_slice($list, 0, ($limit - 1));
   $total = $limit;}
	 
$list = "\"".implode('", "', $list)."\"";
ob_start();		
	echo <<<END
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Presenting a working example of a Simple IIIF discovery system based on Simple Site." />
		<meta name="keywords" content="The National Gallery, London, National Gallery London, Scientific, Research, Heritage, Culture, JSON, PHP, Javascript, Github Pages, Dissemination, VRE, IIIF, Discovery, Mirador, OpenSeadragon, AHRC, Towards a National Collection, SSHOC" />
    <meta name="author" content="Joseph Padfield| joseph.padfield@ng-london.org.uk |National Gallery | London UK | website@ng-london.org.uk |www.nationalgallery.org.uk" />
    <meta name="image" content="" />
    <link rel="icon" href="/ss-dev/graphics/favicon.ico">
    <title>NG IIIF Simple Discovery - viewer-ng</title>
    
	<link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.3/css/all.min.css" integrity="sha256-2H3fkXt6FEmrReK448mDVGKb3WW2ZZw35gI7vqHOE4Y=" crossorigin="anonymous" rel="stylesheet" type="text/css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.css" integrity="sha256-BNdodQbWHpU3HT8xGhkEusT4ch4HEjvwzcbDcVuHR+E=" crossorigin="anonymous" rel="stylesheet" type="text/css">
	<link href="https://cdn.jsdelivr.net/npm/jquery.json-viewer@1.4.0/json-viewer/jquery.json-viewer.css" integrity="sha256-rXfxviikI1RGZM3px6piq9ZL0YZuO5ETcO8+toY+DDY=" crossorigin="anonymous" rel="stylesheet" type="text/css">
	<link href="https://cdn.jsdelivr.net/npm/highlight.js@11.2.0/styles/github.css" integrity="sha256-Oppd74ucMR5a5Dq96FxjEzGF7tTw2fZ/6ksAqDCM8GY=" crossorigin="anonymous" rel="stylesheet" type="text/css">
	<link href="/ss-iiif/css/main.css" rel="stylesheet" type="text/css">
    <style>
    
		.modal {z-index: 1112;}
		.fixed-top {z-index:1111;}
	
    </style>
  </head>

<body onload="onLoad();">
	<div id="wrap" class="container-fluid h-100">
		
		
		
			<div class="h-100 w-100" style="display:block;" id="iiifviewerO">
			</div>
			<div class="h-100" style="min-height:400px;display:none;" id="iiifviewerM">
			</div>
		
	

    
	</div><!--/.container-->
    
	
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/tether@2.0.0/dist/js/tether.min.js" integrity="sha256-cExSEm1VrovuDNOSgLk0xLue2IXxIvbKV1gXuCqKPLE=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha256-d+FygkWgwt59CFkWPuCB4RE6p1/WiUYCy16w1+c5vKk=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/jquery.json-viewer@1.4.0/json-viewer/jquery.json-viewer.js" integrity="sha256-klSHtWPkZv4zG4darvDEpAQ9hJFDqNbQrM+xDChm8Fo=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.2.0/build/highlight.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/mirador@3.2.0/dist/mirador.min.js" integrity="sha256-e11UQD1U7ifc8OK9X0rVMshTXSKl7MafRxi3PTwXDHs=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/openseadragon@2.4.2/build/openseadragon/openseadragon.min.js" integrity="sha256-NMxPj6Qf1CWCzNQfKoFU8Jx18ToY4OWgnUO1cJWTWuw=" crossorigin="anonymous"></script>
	<script src="https://cdn.rawgit.com/Pin0/openseadragon-justified-collection/1.0.2/dist/openseadragon-justified-collection.min.js"></script>
	<script>
		

	
			
	var loadedViewer;
	var myMViewer;
	var myOSDViewer;
	
	/*function setViewer (viewer)
		{
		if (viewer == "m")
			{
			$(toggleO).css("display", "block");
			$(listButton).css("display", "none");					 
			loadedViewer = "m";		
			}
		else
			{
			$(toggleM).css("display", "block");			 
			loadedViewer = "osd";		
			}
		}
		
	function toggleViewer ()
		{		
		if (loadedViewer == "m")
			{
			$(listButton).css("display", "block");
			$(iiifviewerO).css("display", "block");
			$(toggleM).css("display", "block");
			 
			$(iiifviewerM).css("display", "none");
			$(toggleO).css("display", "none");			 
			loadedViewer = "osd";
			}
		else
			{
			$(listButton).css("display", "none");
			$(iiifviewerO).css("display", "none");
			$(toggleM).css("display", "none");
			 
			$(iiifviewerM).css("display", "block");
			$(toggleO).css("display", "block");			 
			loadedViewer = "m";
			}
		}*/
		
	function displayMirador() {
		myMViewer = Mirador.viewer({
			id: "iiifviewerM",
			"workspace": {
				"isWorkspaceAddVisible": true},     
				
			"catalog": [
				{"manifestId": "https://data.ng-london.org.uk/0M2M-0001-0000-0000.iiif"}
				]
				});  
			}
				
	function displayOpenSeadragon() {
		var w = $(iiifviewerO).width();
		var h = $(iiifviewerO).height();
		myOSDViewer = OpenSeadragon({
			id: "iiifviewerO",
			prefixUrl: "https://openseadragon.github.io/openseadragon/images/",
			imageLoaderLimit: 100,
				
			collectionMode:       true,
			collectionRows:       1, 
			
			tileSources:   [ 
			$list
] 
			});
			
		var cls = Math.round(Math.sqrt((w/h) * total));
			
		if (w > h)
			{myOSDViewer.collectionColumns = cls;}
		else
			{myOSDViewer.collectionColumns = cls - 1;}
		
		myOSDViewer.addHandler('open', function() {
			myOSDViewer.world.arrange();
			myOSDViewer.viewport.goHome(true);
			});
		}
				 
		
	var total = $total;
				 
		
		function onLoad() {
			hljs.highlightAll();			
	
		
	 //setViewer ("osd");
	 displayOpenSeadragon();
	 displayMirador();	 
	 $(function() {
  // ------------------------------------------------------- //
  // Multi Level dropdowns
  // ------------------------------------------------------ //
  $("ul.dropdown-menu [data-toggle='dropdown']").on("click", function(event) {
    event.preventDefault();
    event.stopPropagation();

    $(this).siblings().toggleClass("show");


    if (!$(this).next().hasClass('show')) {
      $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
    }
    $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
      $('.dropdown-submenu .show').removeClass("show");
    });

  });
});
			}
	</script>
</body>

</html>
END;
	$html = ob_get_contents();
	ob_end_clean();

echo $html;
exit;

if ($_GET["search"])
	{
	$esResults = getObjectIIIF ($_GET["search"], intval($_GET["limit"]), intval($_GET["from"]));

	$out = array(		
		"limit" => $esResults[1],
		"from" => $esResults[2], 
		"limited" => $esResults[3],
		"total" => $esResults[4],
		"search" => $_GET["search"],
		"results" => $esResults[0],
		"comment" => $esResults[5],
		"altIDs" => $esResults[6]
		);
	}
else
	{
	$out = array(		
		"limit" => 25,
		"from" => 0, 
		"limited" => false,
		"total" => false,
		"search" => "required-search-term",
		"results" => array("info"=>array(), "manifests"=>array()),
		"comment" => "This API has been setup to return lists of IIIF manifests or info.json URLs based on a simple keyword search passed via the URL. Available variable include: \"search\" (the keyword of interest), \"limit\" (a simple limit on the total number of manifest to be returned, up to a maximum of 100, default 25), and \"from\" (an offset value to facilitate pagination of results in conjunction with a defined \"limit\" value, default = 0).",
		"altIDs" => array()
		);
	}

$json = json_encode($out);
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
echo $json;
exit;	


function manifestToList ($uri=false, $dets=false, $list=array())
	{
	global $limit;
	
	$no = count ($list);
  
  if ($no >= $limit)
		{return ($list);}
	else {
				
  if ($uri)
		{$dets = getsslJSONfile ($uri, true, false);}
  
  if(isset($dets["@context"])) {
		
		if ($dets["@context"] == "http://iiif.io/api/presentation/2/context.json")
			{
			if (isset($dets["sequences"])) {
				foreach ($dets["sequences"] as $k2 => $s) {
					foreach ($s["canvases"] as $k3 => $c) {
						foreach ($c["images"] as $k4 => $i) {
							
							if (isset($i["resource"]["service"]))
								{$list[] = $i["resource"]["service"]["@id"]."/info.json";
								 $no++;}
							else if (preg_match('/^(.+).full.full.0.[a-z]+.jpg$/', $i["resource"]["@id"], $m))
								{$list[] = "$m[1]/info.json";
								 $no++;}
							
							if ($no >= $limit) {return ($list);}
							}}}}
			}
		else
			{
			if (isset($dets["items"])) {
				foreach ($dets["items"] as $k2 => $i1) {
					foreach ($i1["items"] as $k3 => $i2) {
						foreach ($i2["items"] as $k3 => $c) {
							foreach ($c["body"] as $k4 => $i) {
								if (preg_match('/^(.+).full.[0-9,ful]+.0.[a-z]+.jpg$/', $i["id"], $m))
									{$list[] = "$m[1]/info.json";
									 $no++;
									 if ($no >= $limit) {return ($list);}}}}}}}
			}
		}
	}
	return ($list);
	}
	
function collectionToList ($uri=false, $dets=false, $list=array())
	{		
	global $limit;
	
	$no = count ($list);
  
  if ($no >= $limit) {return ($list);}
		
	if ($uri)
		{$dets = getsslJSONfile ($uri, true, false);}	

  // Parse $dets
  if(isset($dets["@context"])) 
    {
		
    // Presentation Version 2
		if ($dets["@context"] == "http://iiif.io/api/presentation/2/context.json")
			{				
      // Case One //////////////////////////////////////////////////////
			if (isset($dets["manifests"])) {
				
				foreach ($dets["manifests"] as $mk => $m) {
					if (strtolower($m["@type"]) == "sc:manifest")
						{$list = manifestToList ($m["@id"], false, $list);}
					else
						{$list = collectionToList ($m["@id"], false, $list);}
					}
				
				}
      //////////////////////////////////////////////////////////////////
				
      //Case 2:
			if (isset($dets["collections"])) 
        {
				foreach ($dets["collections"] as $ck => $c) 
          {
					if (isset($c["members"])) {
						foreach ($c["members"] as $k3 => $cm) {
							if (strtolower($cm["@type"]) == "sc:manifest")
								{$list = manifestToList ($cm["@id"], false, $list);}
							else
								{$list = collectionToList ($cm["@id"], false, $list);}
							}
						}
					else if (isset($c["@type"])) {
						if (strtolower($c["@type"]) == "sc:manifest")
							{$list = manifestToList ($c["@id"], false, $list);}
						else
							{$list = collectionToList ($c["@id"], false, $list);}
						}
					}
				}
      //////////////////////////////////////////////////////////////////
				
			
      // Case 3
			if (isset($dets["members"])) 
        {				
				foreach ($dets["members"] as $ck => $c) {
					
					if (isset($c["members"])) {
						foreach ($c["members"] as $k3 => $cm) {
							if (strtolower($cm["@type"]) == "sc:manifest")
								{$list = manifestToList ($cm["@id"], false, $list);}
							else
								{$list = collectionToList ($cm["@id"], false, $list);}
							}
						}
					else if (isset($c["@type"])) {
						if (strtolower($c["@type"]) == "sc:manifest")
							{$list = manifestToList ($c["@id"], false, $list);}
						else
							{$list = collectionToList ($c["@id"], false, $list);}
						}
					}
				}
      //////////////////////////////////////////////////////////////////
      }
    else
      {
      if (isset($dets["items"])) 
        {
        foreach ($dets["items"] as $k2 => $s) 
          {
          if (strtolower($s["type"]) == "manifest")
            {$list = manifestToList ($s["id"], false, $list);}
          else
            {$list = collectionToList ($s["id"], false, $list);}
          }
        }
    	}
    }
    
	return ($list);
	}  
  
function getObjectIIIF ($str, $limit=25, $start=0)
	{
	global $config;					
					
	$limited = false;	
	$missed = 0;
	$out = array("info" => array(), "manifests" => array());
	$altIDs = array();
	$str = urlencode($str);
	
	if (isset($config["page"]) and $config["page"])
		{$start = floor($start/$limit) + 1;}
		
	if (isset($config["api"]) and $config["api"])
		{
		$uri = $config["api"][0].$start.$config["api"][1].$limit.$config["api"][2].$str;
		$arr = getsslJSONfile($uri, true, true);

		$total = getNestedValue ($arr, $config["total"]);
		if (!$total) {$total = 0;}
		if ($total > $limit) {$limited = true;}
	
		$results = getResults ($arr, $config["results"]);
	
		$mdets = formatResults ($results, $config["manifests"]);
		$out["manifests"] = array_merge($out["manifests"], $mdets[0]);
		$altIDs = array_merge($altIDs, $mdets[1]);

		$idets = formatResults ($results, $config["info"]);
		$out["info"] = array_merge($out["info"], $idets[0]);
		$altIDs = array_merge($altIDs, $idets[1]);
		}
		
	if (
		isset($config["info"]["api"]) and isset($config["info"]["results"]) and 
		isset($config["info"]["total"]) and $config["info"]["api"] and 
		$config["info"]["results"] and $config["info"]["total"])
		{		
		$uri = $config["info"]["api"][0].$start.$config["info"]["api"][1].$limit.$config["info"]["api"][2].$str;
		$arr = getsslJSONfile($uri);

		$total = getNestedValue ($arr, $config["info"]["total"]);
		if (!$total) {$total = 0;}
		if ($total > $limit) {$limited = true;}
	
		$results = getResults ($arr, $config["info"]["results"]);
		$idets = formatResults ($results, $config["info"]);
		$out["info"] = array_merge($out["info"], $idets[0]);
		$altIDs = array_merge($altIDs, $idets[1]);	
		}

	if (
		isset($config["manifests"]["api"]) and isset($config["manifests"]["results"]) and 
		isset($config["manifests"]["total"]) and $config["manifests"]["api"] and 
		$config["manifests"]["results"] and $config["manifests"]["total"])
		{
		$uri = $config["manifests"]["api"][0].$start.$config["manifests"]["api"][1].$limit.$config["manifests"]["api"][2].$str;
		$arr = getsslJSONfile($uri);
		
		$mtotal = getNestedValue ($arr, $config["manifests"]["total"]);
		if (!$mtotal) {$mtotal = 0;}
		// Need to extend system to cope with different totals.
		if ($mtotal > $total) {$total = $mtotal;}
		if ($total > $limit)
			{$limited = true;}

		$results = getResults ($arr, $config["manifests"]["results"]);				
		$mdets = formatResults ($results, $config["manifests"]);
		$out["manifests"] = array_merge($out["manifests"], $mdets[0]);
		$altIDs = array_merge($altIDs, $mdets[1]);
		}

	if (isset($config["page"]) and $config["page"])
		{$start = intval(($start - 1) * $limit);	}
		
	$comment = "IIIF resources returned from a full-text object search, for <b>$str</b> of the $config[str].";
	
	return (array($out, $limit, $start, $limited, $total, $comment, $altIDs));
	}
				
function formatResults ($arr, $config)
	{
	$list = array();
	$alts = array();

	foreach ($arr as $j => $b)
		{
		$skip = false;
		if (isset($config["valueConditionField"]) and $config["valueConditionField"] and
		    isset($config["valueConditionValue"]) and $config["valueConditionValue"])
			{$vc = getNestedValue ($b, $config["valueConditionField"]);
			 if ($vc != $config["valueConditionValue"])
				{$skip = true;}}
		if (!$skip)
			{$v = getNestedValue ($b, $config["value"]);}
		else
			{$v = false;}
				
		if ($v) {
			
			if (!is_array($v)) {$v = array($v);}
				
			foreach ($v as $ii => $iv)
				{
				if (isset($config["url"]) and $config["url"])
					{$iv = urlencode($iv);}				 
				if (isset($config["regex"]) and $config["regex"])
					{if (preg_match ("/^".$config["regex"]."$/", $iv, $m)) 
						{$str = "";
						foreach ($config["regexNo"] as $i => $c)
							{$str .= $m[$c];}
						$iv = $str;}}
				$list[] = $config["url"].$iv.$config["suffix"];
				if (isset($config["altID"]) and $config["altID"])
					{$alts[$iv] = getNestedValue ($b, $config["altID"]);}
				}
			}
		}
		
	return (array($list, $alts));
	}

function getConfig ($tag)
	{	
	global $configPath;
	
	$de = glob($configPath."*.json");
	$configs = array();

	foreach($de as $file){
		$dets = getsslJSONfile ($file, true);	
		if (!isset($dets["skip"]) or !$dets["skip"])
			{$configs[$dets["tag"]] = $dets;}
		} 
	
	if (!isset($configs[$tag]))
		{$tag = "ng";}
		
	return ($configs[$tag]);
	}
	
function getResultsLoop ($data, $resultsFields)
	{
	$newdata = array();
	$current = array_shift($resultsFields);
	
	foreach ($data as $k => $a)
		{$newdata = array_merge($newdata, getNestedValue ($a, $current));}
			
	if ($resultsFields)
		{$tmp = getResultsLoop ($newdata, $resultsFields);
		 $newdata = $tmp[0];
		 $resultsFields = $tmp[1];}
				
	return (array($newdata, $resultsFields));
	}
				
function getResults ($data, $resultsFields)
	{	
	$current = array_shift($resultsFields);

	//get me the array of values
	$results = getNestedValue ($data, $current);
			
	if (!$resultsFields)
		{return ($results);}
	else
		{$newResults = getResultsLoop ($results, $resultsFields);
		 return($newResults[0]);}
	}
			
function getsslJSONfile ($uri, $decode=true, $debug=false)
	{
	$arrContextOptions=array(
    "ssl"=>array(
       "verify_peer"=>false,
       "verify_peer_name"=>false,),);  

	$response = file_get_contents($uri, false, stream_context_create($arrContextOptions));
	
	if ($debug)
	 {

	}
	
	if ($decode)
		{return (json_decode($response, true));}
	else
		{return ($response);}
	}

function getNestedValue ($arr, $keys)
	{
	$temp = $arr;	
	foreach ($keys as $k => $v)
		{if (isset($temp[$v]))
			{$temp = $temp[$v];}
		 else
			{$temp = array();
			 break;}}	
	return ($temp);
	}
				
function prg($exit=false, $out=array(), $noecho=false)
	{	
	ob_start();
	echo "<pre class=\"wrap\">";
	if (is_object($out))
		{var_dump($out);}
	else
		{print_r ($out);}
	echo "</pre>";
	$out = ob_get_contents();
  ob_end_clean(); // Don't send output to client
  
	if (!$noecho) {echo $out;}
		
	if ($exit) {exit;}
	else {return ($out);}
	}

?>
