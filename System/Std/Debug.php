<?php

namespace System\Std;

class Debug {
    
    public static function report(\Exception $e){
        ob_clean();
        
        echo '<h2 style="font-family:tahoma; font-weight:normal; color:orange; border-bottom:1px solid #EEE; padding-bottom:9px;">'.get_class($e).'</h2>';
        echo '<h3 style="color:#990000; font-style:italic; font-weight:normal; font-family:tahoma;">'.$e->getMessage().'</h3>';
        echo '<p style="font-family:tahoma; font-size:12px; color:444;"><b>'.$e->getFile().'</b> on line <b>'.$e->getLine().'</b></p>';
        echo '<div style="border:1px dotted #EEE; font-family:tahoma; font-size:12px; color:444;"><ul style="list-style-type:none; padding:0px; margin:0px;">';
        
        $errLine = $e->getLine();
        $code = file($e->getFile());

        $start = ($errLine - 3) > 0 ? ($errLine - 3) : $errLine;
        $stop = ($errLine + 3) < count($code) ? ($errLine + 3) : $errLine;
        
        for($i=$start; $i<$stop; $i++){
            $selected = ($i == ($errLine-1)) ? 'box-shadow:1px 2px 3px #AAA; margin-bottom:4px; background-color:#E9EFF8; border:1px solid #AAA;' : '';
            echo '<li style="border-bottom:2px solid #FCFCFC; ' . $selected . '">';
            echo '<span style="background-color:#FCFCFC; display:inline-block; width:40px; text-align:center; font-weight:bold; padding:5px; color:#777;">' . ($i+1) .'</span>';
            echo '<pre style="border-left: 2px solid #0F0;  display:inline-block; padding:5px; margin:0px; font-family:tahoma; font-size:12px; color:444;">'.htmlspecialchars($code[$i]).'</pre>';
            echo '</li>';
        }
        
        echo '</ul></div>';
        echo '<p style="font-family:tahoma; font-size:12px; color:444;"><b>Stacktrace:</b></p>';
        echo '<div style="font-family:tahoma; font-size:12px; color:444;">' . str_replace("\n", "<br/>", $e->getTraceAsString()) . '</b></div>';
    }
}

?>