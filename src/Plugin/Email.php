<?php

namespace PHPCensor\Plugin;

use PHPCensor\View;
use PHPCensor\Helper\Email as EmailHelper;
use Psr\Log\LogLevel;
use PHPCensor\Plugin;

/**
 * Email Plugin - Provides simple email capability.
 *
 * @author Steve Brazier <meadsteve@gmail.com>
 */
class Email extends Plugin
{
    /**
     * @return string
     */
    public static function pluginName()
    {
        return 'email';
    }

    /**
     * Send a notification mail.
     *
     * @return boolean
     */
    public function execute()
    {
        $addresses = $this->getEmailAddresses();

        // Without some email addresses in the yml file then we
        // can't do anything.
        if (count($addresses) == 0) {
            return false;
        }

        $buildStatus  = $this->build->isSuccessful() ? "Passing Build" : "Failing Build";
        $projectName  = $this->build->getProject()->getTitle();

        try {
            $view = $this->getMailTemplate();
        } catch (\RuntimeException $e) {
            $this->builder->log(
                sprintf('Unknown mail template "%s", falling back to default.', $this->options['template']),
                LogLevel::WARNING
            );
            $view = $this->getDefaultMailTemplate();
        }

        $view->build   = $this->build;
        $view->project = $this->build->getProject();

        $layout          = new View('Email/layout');
        $layout->build   = $this->build;
        $layout->project = $this->build->getProject();
        $layout->content = $view->render();
        $body            = $layout->render();

        $sendFailures = $this->sendSeparateEmails(
            $addresses,
            sprintf("PHP Censor - %s - %s", $projectName, $buildStatus),
            $body
        );

        // This is a success if we've not failed to send anything.
        $this->builder->log(sprintf('%d emails sent.', (count($addresses) - $sendFailures)));
        $this->builder->log(sprintf('%d emails failed to send.', $sendFailures));

        return ($sendFailures === 0);
    }

    /**
     * @param string $toAddress Single address to send to
     * @param string[] $ccList
     * @param string $subject Email subject
     * @param string $body Email body
     *
     * @return integer
     */
    protected function sendEmail($toAddress, $ccList, $subject, $body)
    {
        $email = new EmailHelper();

        $email->setEmailTo($toAddress, $toAddress);
        $email->setSubject($subject);
        $email->setBody($body);
        $email->setHtml(true);

        if (is_array($ccList) && count($ccList)) {
            foreach ($ccList as $address) {
                $email->addCc($address, $address);
            }
        }

        return $email->send($this->builder);
    }

    /**
     * Send an email to a list of specified subjects.
     *
     * @param array $toAddresses
     *   List of destination addresses for message.
     * @param string $subject
     *   Mail subject
     * @param string $body
     *   Mail body
     *
     * @return int number of failed messages
     */
    public function sendSeparateEmails(array $toAddresses, $subject, $body)
    {
        $failures = 0;
        $ccList   = $this->getCcAddresses();

        foreach ($toAddresses as $address) {
            if (!$this->sendEmail($address, $ccList, $subject, $body)) {
                $failures++;
            }
        }

        return $failures;
    }

    /**
     * Get the list of email addresses to send to.
     * @return array
     */
    protected function getEmailAddresses()
    {
        $addresses = [];
        $committer = $this->build->getCommitterEmail();

        $this->builder->logDebug(sprintf("Committer email: '%s'", $committer));
        $this->builder->logDebug(sprintf(
            "Committer option: '%s'",
            (!empty($this->options['committer']) && $this->options['committer']) ? 'true' : 'false'
        ));

        if (!empty($this->options['committer']) && $this->options['committer']) {
            if ($committer) {
                $addresses[] = $committer;
            }
        }

        $this->builder->logDebug(sprintf(
            "Addresses option: '%s'",
            (!empty($this->options['addresses']) && is_array($this->options['addresses'])) ? implode(', ', $this->options['addresses']) : 'false'
        ));

        if (!empty($this->options['addresses']) && is_array($this->options['addresses'])) {
            foreach ($this->options['addresses'] as $address) {
                $addresses[] = $address;
            }
        }

        $this->builder->logDebug(sprintf(
            "Default mailTo option: '%s'",
            !empty($this->options['default_mailto_address']) ? $this->options['default_mailto_address'] : 'false'
        ));

        if (empty($addresses) && !empty($this->options['default_mailto_address'])) {
            $addresses[] = $this->options['default_mailto_address'];
        }

        return array_unique($addresses);
    }

    /**
     * Get the list of email addresses to CC.
     *
     * @return array
     */
    protected function getCcAddresses()
    {
        $ccAddresses = [];

        if (isset($this->options['cc'])) {
            foreach ($this->options['cc'] as $address) {
                $ccAddresses[] = $address;
            }
        }

        return $ccAddresses;
    }

    /**
     * Get the mail template used to sent the mail.
     *
     * @return View
     */
    protected function getMailTemplate()
    {
        if (isset($this->options['template'])) {
            return new View('Email/' . $this->options['template']);
        }

        return $this->getDefaultMailTemplate();
    }

    /**
     * Get the default mail template.
     *
     * @return View
     */
    protected function getDefaultMailTemplate()
    {
        $template = $this->build->isSuccessful() ? 'short' : 'long';

        return new View('Email/' . $template);
    }
}
