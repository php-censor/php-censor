<?php

namespace PHPCensor\Helper;

use PHPCensor\ConfigurationInterface;

/**
 * Helper class for dealing with SSH keys.
 */
class SshKey
{
    protected ConfigurationInterface $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Uses ssh-keygen to generate a public/private key pair.
     *
     * @return array
     */
    public function generate()
    {
        $tempPath = \sys_get_temp_dir() . '/';
        $keyFile  = $tempPath . \md5(\microtime(true));

        if (!\is_dir($tempPath)) {
            \mkdir($tempPath);
        }

        $return = [
            'ssh_private_key' => '',
            'ssh_public_key'  => ''
        ];

        $sshStrength = $this->configuration->get('php-censor.ssh.strength', 2048);
        $sshComment  = $this->configuration->get('php-censor.ssh.comment', 'admin@php-censor');

        $output = @\shell_exec(
            \sprintf(
                'ssh-keygen -t rsa -b %s -f %s -N "" -C "%s"',
                $sshStrength,
                $keyFile,
                $sshComment
            )
        );

        if (!empty($output)) {
            $publicKey  = \file_get_contents($keyFile . '.pub');
            $privateKey = \file_get_contents($keyFile);

            if (!empty($publicKey)) {
                $return['ssh_public_key'] = $publicKey;
            }

            if (!empty($privateKey)) {
                $return['ssh_private_key'] = $privateKey;
            }
        }

        return $return;
    }
}
