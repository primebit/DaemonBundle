<?php
/**
 * Created by PhpStorm.
 * User: prime
 * Date: 17.09.13
 * Time: 16:51
 */

namespace Hq\DaemonsBundle\Daemon;


class SystemWrapper {

    public static function findActiveProcesses($processName)
    {
        $sOutput = '';
        $iCount  = 0;

        ob_start();
        system(
            'ps xw | grep '. escapeshellarg($processName) .' | grep -v '. escapeshellarg('grep') . ' | grep -v ' . escapeshellarg('new mail')
        );

        $sOutput = ob_get_clean();

        if (empty($sOutput)) {
            return 0;
        }

        str_replace("\n", '', $sOutput, $iCount);

        return $iCount;
    }

    public static function myExec($sCmd, $bAndRedirect = true) {
        if ($bAndRedirect) {
            $sTemp = tempnam(sys_get_temp_dir(), "out");

            shell_exec($sCmd . " > $sTemp 2>&1");
            $sRealOutput = file_get_contents($sTemp);
            unlink($sTemp);

            return $sRealOutput;
        } else {
            shell_exec($sCmd);
            return "";
        }
    }

    public static function printMemoryUsage($sHeader = "") {
        echo $sHeader . ": ";
        $sUsage = ceil(memory_get_usage() / 1024);
        $sRealUsage = ceil(memory_get_usage(true) / 1024);
        echo $sUsage . "Kb used, " . $sRealUsage . "Kb really used\n";
    }

    public static function setMemoryLimit($iLimitInMegabytes = 100) {
        ini_set("memory_limit", $iLimitInMegabytes * 1024 * 1024);
    }

    public static function getMemoryLimit() {
        return ini_get('memory_limit');
    }

} 