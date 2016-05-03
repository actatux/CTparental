<?php
// on détecte la langue du système
$LANG = getenv('LANG');
//$LANG = "fr_FR.UTF-8";

if (isset($LANG))
{
    $tab    = explode(".", getenv('LANG'));
    //$domain = substr($tab[0], 0, 2);
    $domain="ctparental";

    // set the locale into the instance of gettext 
    setlocale(LC_ALL, $LANG); // change by language, directory name fr_FR, not fr_FR.UTF-8 

    // Spécifie la localisation des tables de traduction
    // ce qui donne pour une variable « $LANG = 'fr_FR.UTF-8' » une répertoir ci dessous.
    // ./locale/fr_FR/LC_MESSAGES/
    bindtextdomain($domain, "/usr/share/locale");

    // Choisit le domaine
    // ce qui nous donne un nom de fichier pour « $LANG = 'fr_FR.UTF-8' » de fr.mo
    textdomain($domain);
    // La traduction se trouve dans ./locale/fr_FR/LC_MESSAGES/fr.mo
}

function form_filter ($form_content)
{
    // réencodage iso + format unix + rc fin de ligne (ouf...)
    $list = str_replace("\r\n", "\n", utf8_decode($form_content));
    
    if (strlen($list) != 0)
    {
        if ($list[strlen($list)-1] != "\n")
        {
            $list[strlen($list)] = "\n";
        }
    }
    
    return $list;
}

$week = array
(
    gettext("monday"),
    gettext("tuesday"),
    gettext("wednesday"),
    gettext("thursday"),
    gettext("friday"),
    gettext("saturday"),
    gettext("sunday")
);

$weeknum               = array(0,1,2,3,4,5,6);
$dirconf               = "/etc/CTparental/";
$bl_categories         = $dirconf."bl-categories-available";
$bl_categories_enabled = $dirconf."categories-enabled.conf";
$conf_file             = $dirconf."CTparental.conf";
$conf_ctoff_file       = $dirconf."GCToff.conf";
$hconf_file            = $dirconf."CThours.conf";
$wl_domains            = $dirconf."domaine-rehabiliter.conf";
$bl_domains            = $dirconf."blacklist-local.conf";

$cmdCT = "sudo -h localhost -u root /usr/bin/CTparental ";


if (isset($_GET['dgfile']))
{
    $dg_confswitch=$_GET['dgfile'];
} 
else
{
    if ($DNSMASQ <> "OFF")
    {
        $dg_confswitch = 'Blacklist filtering';
    }
    else
    {
        $dg_confswitch = 'Hours of allowed connections';
    }
}
        
switch ($dg_confswitch)
{
    case 'extensions has filtered' :
        $dg_file_edit="/etc/dansguardian/lists/bannedextensionlist";
        break;
    case 'mimetype has filtered' :
        $dg_file_edit="/etc/dansguardian/lists/bannedmimetypelist";
        break;
    case '*ip **ips ...' :
        $dg_file_edit="/etc/dansguardian/lists/bannedsitelist";
        break;
    case 'WhiteList Filtering' :
        $bl_categories=$dirconf."wl-categories-available";
        break;
    case 'Blacklist filtering' :
        $bl_categories=$dirconf."bl-categories-available";
        break;
    case 'safesearch Enebeled' :
        $dg_file_edit=$dirconf."CTsafe.conf";
        break;
}

# traitement du formulaire
if (isset($_POST['choix'])){ $choix=$_POST['choix']; } else { $choix=""; }
switch ($choix)
{
	
case 'change_file1' :
	$tab=file($dg_file_edit);
	if ($tab)
		{
		$pointeur=fopen($dg_file_edit,"w+");
		$numline=1;
		foreach ($tab as $ligne)
			{
			$line=$ligne ;
			if (trim($ligne) != '') # the line isn't empty
			{
				$ext_lignes=explode(" ", $line);
				
				if ($_POST['chk-'.$numline] == "on" )
				{
					if(preg_match('/^#/',$ligne)) {
						$line=substr($ligne,1);
					}
				}
				else { 				
						if(!preg_match('/^#/',$ligne)) {
							$line="#".$ligne;			
						}
				}
				//echo $line."<br>";
				fwrite($pointeur,$line);
		    }	
		    $numline=$numline+1;			
			}
		fclose($pointeur);
		}
	exec ($cmdCT."-dgreload");
	break;
case 'change_safesearch' :
	$tab=file($dg_file_edit);
	if ($tab)
		{
		$pointeur=fopen($dg_file_edit,"w+");
		$numline=1;
		foreach ($tab as $ligne)
			{
			$line=$ligne ;
			if (trim($ligne) != '') # the line isn't empty
			{
				$ext_lignes=explode(" ", $line);
				
				if ($_POST['chk-'.$numline] == "on" )
				{
					if(preg_match('/^#/',$ligne)) {
						$line=substr($ligne,1);
					}
				}
				else { 				
						if(!preg_match('/^#/',$ligne)) {
							$line="#".$ligne;			
						}
				}
				//echo $line."<br>";
				fwrite($pointeur,$line);
		    }	
		    $numline=$numline+1;			
			}
		fclose($pointeur);
		}
	exec ($cmdCT."-ubl");
	break;
case 'gct_Off' :
	exec ($cmdCT."-gctoff");
	break;
case 'gct_On' :
	exec ($cmdCT."-gcton");
	break;
case 'LogOFF' :
	header('HTTP/1.0 401 Unauthorized');
	header('WWW-Authenticate: Digest realm="interface admin"');
	exit;
	break;
case 'BL_On' :
	exec ($cmdCT."-on");
	break;
case 'BL_Off' :
	exec ($cmdCT."-off");
	break;
case 'H_On' :
	exec ($cmdCT."-trf");
	break;
case 'H_Off' :
	exec ($cmdCT."-tlu");
	break;
case 'AUP_On' :
	exec ($cmdCT."-aupon");
	break;
case 'AUP_Off' :
	exec ($cmdCT."-aupoff");
	break;
case 'INIT_BL' :
	exec ($cmdCT."-dble");
	break;
case 'Download_bl' :
	exec ($cmdCT."-dl");
	break;
case 'ProxyDF_On' :
	exec ($cmdCT."-pfon");
	break;
case 'ProxyDF_Off' :
	exec ($cmdCT."-pfoff");
	break;
case 'MAJ_cat' :
	$tab=file($bl_categories_enabled);	
	if ($tab)
		{
		$pointeur=fopen($bl_categories_enabled, "w+");
		foreach ($_POST as $key => $value)
			{
                        if (strstr($key,'chk-'))
				{	
				$line=str_replace('chk-','',$key)."\n";
				fwrite($pointeur,$line);
				}
			}
		fclose($pointeur);
		}
	else {echo gettext('Error opening the file')." ".$bl_categories_enabled;}
	$fichier=fopen($bl_domains,"w+");
	fputs($fichier, form_filter($_POST['OSSI_bl_domains']));
	fclose($fichier);
	unset($_POST['OSSI_bl_domains']);
	$fichier=fopen($wl_domains,"w+");
	fputs($fichier, form_filter($_POST['OSSI_wl_domains']));
	fclose($fichier);
	unset($_POST['OSSI_wl_domains']);
	exec ($cmdCT."-ubl");
	break;
case 'MAJ_H' :
	$formatheuresok=1;
	if (isset($_POST['selectuser'])){ $selectuser=$_POST['selectuser']; }
	#echo "$selectuser";
	$tab=file($hconf_file);	
	if ($tab)
	{
		$pointeur=fopen($hconf_file, "w+");	
		foreach ($tab as $line)
		{
			if (strstr($line,$selectuser) == false)
			{
				fwrite($pointeur,$line); # on reécrit toutes les lignes ne correspondant pas à l'utilisateur sélectionné
			}
	
		}
	}
	else {echo gettext('Error opening the file')." $hconf_file";}
	if (isset($_POST["isadmin"])){fwrite($pointeur,"$selectuser=admin="."\n"); } 
	else 
	{
		if (isset($_POST["tmax"])){
			if ( ( preg_match( "/^[1-9]$|^[1-9][0-9]$|^[1-9][0-9][0-9]$|^1[0-3][0-9][0-9]$|^14[0-3][0-9]$|^1440$/", $_POST["tmax"] ) and preg_match( "/^[1-9]$|^[1-9][0-9]$|^[1-9][0-9][0-9]$|^1[0-3][0-9][0-9]$|^14[0-3][0-9]$|^1440$/", $_POST["tmax2"] )) == 1  )
			{
				if ( $_POST["tmax2"] <= $_POST["tmax"] ) 
				{
				fwrite($pointeur,"$selectuser=user=".$_POST["tmax"]."=".$_POST["tmax2"]."\n");
				}
				else
				{
					fwrite($pointeur,"$selectuser=user=".$_POST["tmax"]."=".$_POST["tmax"]."\n");
					echo "<H3>".gettext('Time surf between 1 to')." ".$_POST["tmax"]."</H3>";
					
				}
			}
			else {fwrite($pointeur,"$selectuser=user=1440=1440"."\n"); 
				  echo "<H3>".gettext('You must enter a value between 1 and 1440 minutes.')."</H3>";}
		}
		else {fwrite($pointeur,"$selectuser=user=1440=1440"."\n"); }
		foreach ($weeknum as $numday)
		{
			$formatheuresok=1;
			if (isset($_POST["h1$numday"])){ $h1[$numday]=$_POST["h1$numday"]; } else { $h1[$numday]="00h00"; }
			if (isset($_POST["h2$numday"])){ $h2[$numday]=$_POST["h2$numday"]; } else { $h2[$numday]="23h59"; }
			if (isset($_POST["h3$numday"])){ $h3[$numday]=$_POST["h3$numday"]; } else { $h3[$numday]=""; }
			if (isset($_POST["h4$numday"])){ $h4[$numday]=$_POST["h4$numday"]; } else { $h4[$numday]=""; }
			if (preg_match("/^[0-1][0-9]h[0-5][0-9]$|^2[0-3]h[0-5][0-9]$/",$h1[$numday])!=1){$formatheuresok=0;}
			if (preg_match("/^[0-1][0-9]h[0-5][0-9]$|^2[0-3]h[0-5][0-9]$/",$h2[$numday])!=1){$formatheuresok=0;}
			if ($h3[$numday]=="")
			{	
	
				if ($formatheuresok == 1)
				{
					$t1=explode("h", $h1[$numday]);
					$t2=explode("h", $h2[$numday]);
					$v1="$t1[0]$t1[1]";
					$v2="$t2[0]$t2[1]";
					if ( $v1 < $v2)
					{
						fwrite($pointeur,"$selectuser=$numday=$h1[$numday]:$h2[$numday]"."\n");
					}
					else
					{
						fwrite($pointeur,"$selectuser=$numday=00h00:23h59"."\n");
						echo "<H3>$week[$numday] : ".gettext('time inconsistency: ')." $h1[$numday]>=$h2[$numday]</H3>";
					}
				}
				else 
				{
					fwrite($pointeur,"$selectuser=$numday=00h00:23h59"."\n");
					echo "<H3>$week[$numday] : ".gettext('A bad time format has been found: 8h30 instance must be written 08h30')."</H3>";
				}
			}
			else 
			{
				if (preg_match("/^[0-1][0-9]h[0-5][0-9]$|^2[0-3]h[0-5][0-9]$/",$h3[$numday])!=1){$formatheuresok=0;}
				if (preg_match("/^[0-1][0-9]h[0-5][0-9]$|^2[0-3]h[0-5][0-9]$/",$h4[$numday])!=1){$formatheuresok=0;}
				if ($formatheuresok == 1)
				{
					$t1=explode("h", $h1[$numday]);
					$t2=explode("h", $h2[$numday]);
					$t3=explode("h", $h3[$numday]);
					$t4=explode("h", $h4[$numday]);
					$v1="$t1[0]$t1[1]";
					$v2="$t2[0]$t2[1]";
					$v3="$t3[0]$t3[1]";
					$v4="$t4[0]$t4[1]";
					if ( $v1 < $v2 && $v2 < $v3 && $v3 < $v4)
					{
					fwrite($pointeur,"$selectuser=$numday=$h1[$numday]:$h2[$numday]:$h3[$numday]:$h4[$numday]"."\n");
					}
					else
					{
						fwrite($pointeur,"$selectuser=$numday=00h00:23h59"."\n");
						echo "<H3>$week[$numday] : ".gettext('time inconsistency: ')." $h1[$numday]>=$h2[$numday]>=$h3[$numday]>=$h4[$numday]</H3>";
					}
				}
				else 
				{
					fwrite($pointeur,"$selectuser=$numday=00h00:23h59"."\n");
					echo "<H3>$week[$numday] : ".gettext('A bad time format has been found: 8h30 instance must be written 08h30')."</H3>";
					
				}
			}

		}
	}
	
	fclose($pointeur);
	exec ($cmdCT."-trf");
	break;
	
case 'change_user' :
	$tab=file($conf_ctoff_file);
	if ($tab)
		{
		$pointeur=fopen($conf_ctoff_file,"w+");
		$numline=1;
		foreach ($tab as $ligne)
			{
			$line=$ligne ;
			if (trim($ligne) != '') # the line isn't empty
			{
				$ext_lignes=explode(" ", $line);
				
				if ($_POST['chk-'.$numline] == "on" )
				{
					if(preg_match('/^#/',$ligne)) {
						$line=substr($ligne,1);
					}

				}
				else { 				
						if(!preg_match('/^#/',$ligne)) {
							$line="#".$ligne;			
						}
						if(preg_match('/^\+/',$ligne)) {
						$line=$ligne;
						}
				}
				//echo $line."<br>";
				fwrite($pointeur,$line);
		    }	
		    $numline=$numline+1;			
			}
		fclose($pointeur);
		}
	exec ($cmdCT."-gctalist");
	break;
}


if (is_file ($conf_file))
{
    $tab = file($conf_file);
    
    if ($tab)
    {
        foreach ($tab as $line)
        {
            $field = explode("=", $line);
            
            if ($field[0] == "LASTUPDATE")      { $LASTUPDATE   = trim($field[2]); }
            if ($field[0] == "DNSMASQ")         { $DNSMASQ      = trim($field[1]); }
            if ($field[0] == "AUTOUPDATE")      { $AUTOUPDATE   = trim($field[1]); }
            if ($field[0] == "HOURSCONNECT")    { $HOURSCONNECT = trim($field[1]); }
            if ($field[0] == "GCTOFF")          { $GCTOFF       = trim($field[1]); }
            if ($field[0] == "PRIVOXYDF")   	{ $PRIVOXYDF 	= trim($field[1]); }            
        }
    }
}
else
{
    echo gettext('Error opening the file')." ".$conf_file;
}

echo "<nav class='navbar navbar-inverse navbar-fixed-top'>";
echo "<div class='container-fluid'>";
echo "<div class='navbar-header'>";
echo "<a class='navbar-brand' href='#'>".gettext('web filtering')."</a>";
echo "</div>";

if ($DNSMASQ <> "OFF")
{
    echo "<p class='navbar-text'>";
    echo gettext('Actually, the Domain name filter is on');
    echo "&nbsp;<span class='glyphicon glyphicon-ok' style='color: green;' aria-hidden='true'></span>";
    echo "</p>";
}
else
{
    echo "<p class='navbar-text'>";
    echo gettext('Actually, the Domain name filter is off');
    echo "&nbsp;<span class='glyphicon glyphicon-remove' style='color: red;' aria-hidden='true'></span>";
    echo "</p>";
}

echo "<form class='navbar-form navbar-right' role='logout' method='post'>";
echo "<input type='hidden' name='choix' value='LogOFF'>";
echo "<button class='btn btn-default'>";
echo "<span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>&nbsp;";
echo gettext('Logout');
echo "</button>";
echo "</form>";

echo "<form class='navbar-form navbar-right' action='".$_SERVER["PHP_SELF"]."?dgfile=Hours of allowed connections' method='post' role='Switch the Filter'>";
echo "<input type='hidden' name='choix' value='";

if ($DNSMASQ <> "OFF")
{
    echo "BL_Off";
}
else
{
    echo "BL_On";
}

echo "'>";

if ($DNSMASQ <> "OFF")
{
    echo "<button class='btn btn-danger'>";
    echo "<span class='glyphicon glyphicon-refresh' aria-hidden='true'></span>&nbsp;";
    echo gettext('Switch the Filter off');
    echo "</button>";
}
else
{
    echo "<button class='btn btn-success'>";
    echo "<span class='glyphicon glyphicon-refresh' aria-hidden='true'></span>&nbsp;";
    echo gettext('Switch the Filter on');
    echo "</button>";
}

echo "</form>";
echo "</div>";
echo "</nav>";

echo "<div class='col-sm-3 col-md-2 sidebar'>";
echo "<ul class='nav nav-sidebar'>";
    
if ($DNSMASQ <> "OFF")
{
    $temp = array
    (
        'Blacklist filtering',
        'WhiteList Filtering',
        'extensions has filtered',
        'mimetype has filtered',
        '*ip **ips ...',
        'privileged group',
        'safesearch Enebeled'
    );
    
    foreach ($temp as $item)
    {
        echo "<li role='".gettext($item)."'".(($dg_confswitch == $item) ? " class='active'" : "").">";
        echo "<a href='".$_SERVER["PHP_SELF"]."?dgfile=".$item."'>".gettext($item)."</a>";
        echo "</li>";
    }
}

$item = 'Hours of allowed connections';
echo "<li role='".gettext($item)."'".(($dg_confswitch == $item) ? " class='active'" : "").">";
echo "<a href='".$_SERVER["PHP_SELF"]."?dgfile=".$item."'>".gettext($item)."</a>";
echo "</li>";
    
echo "</ul>";
echo "</div>";

echo "<div class='col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main'>";

# Lecture du formulaire
switch ($dg_confswitch)
{
    case 'Blacklist filtering':
        include 'bl_dns.php';
        break;
    case 'WhiteList Filtering':
        include 'wl_dns.php';
        break;
    case 'extensions has filtered':
        include 'dg_extensions.php';
        break;
    case 'mimetype has filtered':
        include 'dg_mimetype.php';
        break;
    case '*ip **ips ...':
        include 'dg_sitelist.php';
        break;
    case 'privileged group':
        include 'gctoff.php';
        break;
    case 'safesearch Enebeled':
        include 'safesearch.php';
        break;  
    case 'Hours of allowed connections':
        include 'hours.php';
        break;
}

echo "</div>";
