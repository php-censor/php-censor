<?php

use Phinx\Migration\AbstractMigration;
use PHPCensor\Store\Factory;
use PHPCensor\Store\BuildStore;
use PHPCensor\Model\Build;

class AddedRequestBranchToBuild extends AbstractMigration
{
    public function up()
    {
        /** @var BuildStore $buildStore */
        $buildStore = Factory::getStore('Build');

        $count  = 100;
        $offset = 0;
        while ($count >= 100) {
            $builds    =  $buildStore->getBuilds(100, $offset);
            $offset    += 100;
            $count     =  count($builds);

            /** @var Build $build */
            foreach ($builds as &$build) {
                $extra = $build->getExtra();
                if (isset($extra['build_type'])) {
                    unset($extra['build_type']);

                    $build->setSource(Build::SOURCE_WEBHOOK_PULL_REQUEST_CREATED);
                }

                if (!empty($extra['remote_url'])) {
                    preg_match(
                        '/[\/:]([a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-]+)/',
                        $extra['remote_url'],
                        $matches
                    );
                    $remoteReference = $matches[1];

                    if ($remoteReference && empty($extra['remote_reference'])) {
                        $extra['remote_reference'] = $remoteReference;
                    }
                }
                unset($extra['build_type']);
                unset($extra['pull_request_id']);

                $build->setExtra($extra);
                $buildStore->save($build);
            }
            unset($build);
        }
    }

    public function down()
    {
    }
}
