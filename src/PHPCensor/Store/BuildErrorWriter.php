<?php

namespace PHPCensor\Store;

use b8\Database;

/**
 * Class BuildErrorWriter
 */
class BuildErrorWriter
{
    /** @var int */
    protected $build_id;

    /** @var array */
    protected $errors = [];

    /** @var int */
    protected $buffer_size;

    /**
     * BuildErrorWriter constructor.
     *
     * @param int $build_id
     * @param int $buffer_size
     */
    public function __construct($build_id, $buffer_size = 10000)
    {
        $this->build_id = $build_id;
        $this->buffer_size = max((int) $buffer_size, 1);
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
     * @param int       $severity
     * @param string    $file
     * @param int       $line_start
     * @param int       $line_end
     * @param \DateTime $created_date
     */
    public function write($plugin, $message, $severity, $file = null, $line_start = null, $line_end = null, $created_date = null)
    {
        if (is_null($created_date)) {
            $created_date = new \DateTime();
        }
        $this->errors[] = array(
            'plugin' => (string)$plugin,
            'message' => (string)$message,
            'severity' => (int)$severity,
            'file' => !is_null($file) ? (string)$file : null,
            'line_start' => !is_null($line_start) ? (int)$line_start : null,
            'line_end' => !is_null($line_end) ? (int)$line_end : null,
            'created_date' => $created_date->format('Y-m-d H:i:s'),
        );
        if (count($this->errors) >= $this->buffer_size) {
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

        $insert_values_placeholders = [];
        $insert_values_data = [];
        foreach ($this->errors as $i => $error) {
            $insert_values_placeholders[] = '(
                :build_id' . $i . ',
                :plugin' . $i . ',
                :file' . $i . ',
                :line_start' . $i . ',
                :line_end' . $i . ',
                :severity' . $i . ',
                :message' . $i . ',
                :created_date' . $i . '
            )';
            $insert_values_data['build_id' . $i] = $this->build_id;
            $insert_values_data['plugin' . $i] = $error['plugin'];
            $insert_values_data['file' . $i] = $error['file'];
            $insert_values_data['line_start' . $i] = $error['line_start'];
            $insert_values_data['line_end' . $i] = $error['line_end'];
            $insert_values_data['severity' . $i] = $error['severity'];
            $insert_values_data['message' . $i] = $error['message'];
            $insert_values_data['created_date' . $i] = $error['created_date'];
        }
        $query = '
            INSERT INTO {{build_error}} (
                {{build_id}},
                {{plugin}},
                {{file}},
                {{line_start}},
                {{line_end}},
                {{severity}},
                {{message}},
                {{created_date}}
            )
            VALUES ' . join(', ', $insert_values_placeholders) . '
        ';
        $stmt = Database::getConnection('write')->prepareCommon($query);
        $stmt->execute($insert_values_data);
        $this->errors = [];
    }
}
