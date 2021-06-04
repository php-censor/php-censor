<?php

declare(strict_types = 1);

namespace PHPCensor\Store;

use DateTime;
use PHPCensor\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Model\BuildError;
use PHPCensor\StoreRegistry;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildErrorWriter
{
    /**
     * @var int
     */
    private int $buildId;

    /**
     * @var int
     */
    private int $projectId;

    /**
     * @var array
     */
    private array $errors = [];

    private DatabaseManager $databaseManager;

    private StoreRegistry $storeRegistry;

    /**
     * @see https://stackoverflow.com/questions/40361164/pdoexception-sqlstatehy000-general-error-7-number-of-parameters-must-be-bet
     */
    private int $bufferSize;

    public function __construct(
        ConfigurationInterface $configuration,
        DatabaseManager $databaseManager,
        StoreRegistry $storeRegistry,
        int $projectId,
        int $buildId
    ) {
        $this->bufferSize = (int)$configuration->get('php-censor.build.writer_buffer_size', 500);

        $this->projectId = $projectId;
        $this->buildId   = $buildId;

        $this->databaseManager = $databaseManager;
        $this->storeRegistry   = $storeRegistry;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->flush();
    }

    /**
     * Write error
     *
     * @param string    $plugin
     * @param string    $message
     * @param int   $severity
     * @param string    $file
     * @param int   $lineStart
     * @param int   $lineEnd
     * @param DateTime $createdDate
     */
    public function write(
        $plugin,
        $message,
        $severity,
        $file = null,
        $lineStart = null,
        $lineEnd = null,
        $createdDate = null
    ) {
        if (is_null($createdDate)) {
            $createdDate = new DateTime();
        }

        /** @var BuildErrorStore $errorStore */
        $errorStore = $this->storeRegistry->get('BuildError');
        $hash       = BuildError::generateHash($plugin, $file, $lineStart, $lineEnd, $severity, $message);

        $this->errors[] = [
            'plugin'      => (string)$plugin,
            'message'     => (string)$message,
            'severity'    => (int)$severity,
            'file'        => !is_null($file) ? (string)$file : null,
            'line_start'  => !is_null($lineStart) ? (int)$lineStart : null,
            'line_end'    => !is_null($lineEnd) ? (int)$lineEnd : null,
            'create_date' => $createdDate->format('Y-m-d H:i:s'),
            'hash'        => $hash,
            'is_new'      => $errorStore->getIsNewError($this->projectId, $hash) ? 1 : 0,
        ];

        if (count($this->errors) >= $this->bufferSize) {
            $this->flush();
        }
    }

    /**
     * Flush buffer
     */
    public function flush()
    {
        if (empty($this->errors)) {
            return;
        }

        $insertValuesPlaceholders = [];
        $insertValuesData         = [];
        foreach ($this->errors as $i => $error) {
            $insertValuesPlaceholders[] = '(
                :build_id' . $i . ',
                :plugin' . $i . ',
                :file' . $i . ',
                :line_start' . $i . ',
                :line_end' . $i . ',
                :severity' . $i . ',
                :message' . $i . ',
                :create_date' . $i . ',
                :hash' . $i . ',
                :is_new' . $i . '
            )';
            $insertValuesData['build_id' . $i]    = $this->buildId;
            $insertValuesData['plugin' . $i]      = $error['plugin'];
            $insertValuesData['file' . $i]        = $error['file'];
            $insertValuesData['line_start' . $i]  = $error['line_start'];
            $insertValuesData['line_end' . $i]    = $error['line_end'];
            $insertValuesData['severity' . $i]    = $error['severity'];
            $insertValuesData['message' . $i]     = $error['message'];
            $insertValuesData['create_date' . $i] = $error['create_date'];
            $insertValuesData['hash' . $i]        = $error['hash'];
            $insertValuesData['is_new' . $i]      = $error['is_new'];
        }
        $query = '
            INSERT INTO {{build_errors}} (
                {{build_id}},
                {{plugin}},
                {{file}},
                {{line_start}},
                {{line_end}},
                {{severity}},
                {{message}},
                {{create_date}},
                {{hash}},
                {{is_new}}
            )
            VALUES ' . join(', ', $insertValuesPlaceholders) . '
        ';
        $stmt = $this->databaseManager->getConnection('write')->prepare($query);
        $stmt->execute($insertValuesData);

        $this->errors = [];
    }
}
