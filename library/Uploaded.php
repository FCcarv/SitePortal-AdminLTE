<?php/** * Created by PhpStorm. * User: franc * Date: 11/05/18 * Time: 13:08 */class Uploaded{ public  function permissaoFile() {     $command = "chmod 0777 -R /opt/lampp/htdocs/Fatiando-AdminLTE/uploads/*";     $output = shell_exec($command);     self::$BaseDir = ( (string) $BaseDir ? $BaseDir : $output);     $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathname));     foreach($iterator as $item) {         chmod($item, $filemode);     } }}