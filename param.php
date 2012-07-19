<?php
include $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

$comPath = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/bitrix/';
$comUtil = new CComponentUtil();
$snippetsDir = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/main/include/snippets/';
$snippet = '';
if (is_dir($comPath)) {
    if ($h = opendir($comPath)) {
        while (($file = readdir($h)) !== false) {

        	if ($file == '..' | $file == '.') {
        		continue;
        	}

          if (!file_exists($comPath . $file . '/.parameters.php')) {
            continue;

          }

          $param = $comUtil->GetComponentProps('bitrix:'.$file);
		      $comUtil->PrepareVariables($param);

          if (!is_array($param['PARAMETERS']) | count ($param) == 0) {
              continue;
          }

          $snippetName = 'bitrix-'.$file.'.sublime-snippet';
          $snippet = "<snippet>"."\n".
				  "  <content>"."\n".
				   "    <![CDATA["."\n".
				    '<?\$APPLICATION->IncludeComponent("bitrix:'.$file.'", "${1:.default}", Array('."\n";

        		$count = 2;
        		foreach ($param['PARAMETERS'] as $name => $value) {
              $default = trim($value['DEFAULT']);

              if (substr_count($default, '$') > 0) {
                $default = str_replace('$', '\$', $default);
              }

              if ($default == '') {
                $default = '"${'.$count.':}"';
              } elseif (substr_count($default, '{') > 0) {
                $default = str_replace(array('{','}','='), array('','',''), $default);
                $default = '${'.$count.':'.$default.'}';
              } else {
                $default = '"${'.$count.':'.$default.'}"';
              }


              
              $snippet .= '   "'.$name.'" => '.$default.',	// '.$value['NAME']."\n";
              $count ++;
        		}

        		$snippet .= '   ),'."\n".
        					'  false'."\n".
        				');?>'."\n".
        		    	'  ]]>'."\n".
        		    '</content>'."\n".
        		    '<tabTrigger></tabTrigger>'."\n".
        		    '<scope>text.html</scope>'."\n".
        		    '<description>Bitrix '.$file.'</description>'."\n".
        		'</snippet>';

            if (file_exists($snippetsDir.$snippetName)) {
              unlink($snippetsDir.$snippetName);
            }
            file_put_contents($snippetsDir.$snippetName, $snippet);
            unset($param); $snippet = '';
        }
        closedir($h);
    }
} else {
	echo 'Wrong Components dir "'.$comPath.'"';
}

?>