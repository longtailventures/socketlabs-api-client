<?php

namespace LongTailVentures;

use stdClass;

class SocketLabsApiClient
{
    private $_apiKey, $_serverId;
    private $_messageData;

    const API_URL = 'https://inject.socketlabs.com/api/v1/email';


    /**
     * constructor
     *
     * @param string $socketLabsApiKey
     * @param int $socketLabsServerId
     * @param string $debug
     */
    public function __construct($socketLabsApiKey, $socketLabsServerId)
    {
        $this->_apiKey = $socketLabsApiKey;
        $this->_serverId = $socketLabsServerId;

        $this->_messageData = [
            'Subject' => '',
            'TextBody' => '',
            'HtmlBody' => '',
            'To' => '',
            'From' => '',
            'ReplyTo' => '',
        ];
    }


    /**
     * set subject of email
     */
    public function setSubject($subject)
    {
        $this->_messagesData['Subject'] = $subject;
    }


    /**
     * set Html body of email message
     *
     * @param string $body
     */
    public function setHtmlBody($body)
    {
        $this->_messageData['HtmlBody'] = $body;
    }


    /**
     * set Text body of email message
     *
     * @param string $body
     */
    public function setTextBody($body)
    {
        $this->_messageData['TextBody'] = $body;
    }


    /**
     * set from address for email
     *
     * @param string $name
     * @param string $email
     */
    public function setFromAddress($name, $email)
    {
        $this->_messageData['From'] = [
            'EmailAddress' => $email,
            'FriendlyName' => $name
        ];

        // set replyto to incoming as default
        $this->_messageData['ReplyTo'] = $this->_messageData['From'];
    }


    /**
     * set reply to address for email
     *
     * @param string $name
     * @param string $email
     */
    public function setReplyToAddress($name, $email)
    {
        $this->_messageData['ReplyTo'] = [
            'EmailAddress' => $email,
            'FriendlyName' => $name
        ];

    }


    /**
     * set to address for email
     *
     * @param string $name
     * @param string $email
     */
    public function setToAddress($name, $email)
    {
        $this->_messageData['To'] = [
            'EmailAddress' => $email,
            'FriendlyName' => $name
        ];
    }


    /**
     * add cc address for email
     *
     * @param string $name
     * @param string $email
     */
    public function addCcAddress($name, $email)
    {
        $this->_messageData['Cc'] = [
            'EmailAddress' => $email,
            'FriendlyName' => $name
        ];
    }


    /**
     * add attachment to email
     *
     * @param string $attachmentFileName
     * @param string $fileName
     */
    public function addAttachment($attachmentFileName, $fileName)
    {
        if (!isset($this->_messageData['Attachments']))
            $this->_messageData['Attachments'] = [];
        $this->_messageData['Attachments'] = $fileName;
    }


    /**
     * send email
     *
     * @return boolean $isSent
     * true if successful, false otherwise
     */
    public function send()
    {
        // re: https://github.com/socketlabs/email-on-demand-examples/blob/master/PHP/Injection%20API/http_injection_merge.php
        $data = new stdClass();
        $data->ServerId = $this->_serverId;
        $data->ApiKey = $this->_apiKey;
        $data->Messages = array($this->_messageData);

        $emailData = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $emailData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);

        return $result->ErrorCode === 'Success';
    }
}
