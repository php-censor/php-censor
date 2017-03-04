<?php

namespace PHPCensor\Helper;

/**
 * Helper class for dealing with SSH keys.
 */
class SshKey
{
    /**
     * Uses ssh-keygen to generate a public/private key pair.
     * @return array
     */
    public function generate()
    {
        $tempPath = sys_get_temp_dir() . '/';
        $keyFile  = $tempPath . md5(microtime(true));

        if (!is_dir($tempPath)) {
            mkdir($tempPath);
        }

        $return = ['private_key' => '', 'public_key' => ''];

        $output = @shell_exec('ssh-keygen -t rsa -b 2048 -f '.$keyFile.' -N "" -C "deploy@php-censor"');

        if (!empty($output)) {
            $pub = file_get_contents($keyFile . '.pub');
            $prv = file_get_contents($keyFile);

            if (!empty($pub)) {
                $return['public_key'] = $pub;
            }

            if (!empty($prv)) {
                $return['private_key'] = $prv;
            }
        }

        return $return;
    }
}
