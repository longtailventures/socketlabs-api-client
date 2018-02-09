<?php

namespace LongTailVentures;

use stdClass;

class SocketLabsApiClient
{
    const ACCOUNT_ID = '';
    const API_KEY = '';
    const SERVER_ID = '';

    const API_URL = 'https://inject.socketlabs.com/api/v1/email';


    public function __construct($debug = false)
    {
        $this->_debug = $debug;
    }


    public function sendMail(array $mailTo, array $mailFrom, $subject, array $messageTexts, array $attachments = [])
    {
        // re: https://github.com/socketlabs/email-on-demand-examples/blob/master/PHP/Injection%20API/http_injection_merge.php
        $data = new stdClass();
        $data->ServerId = self::SERVER_ID;
        $data->ApiKey = self::API_KEY;

        if ($this->_debug)
        {
            $mailTo = [];
            $mailTo[] = [
                'email' => $this->_debugEmail,
                'name' => 'LotLinx Support'
            ];
        }

        $messagesData = array(
            'Subject' => $subject,
            'TextBody' => $messageTexts['text'],
            'To' => array(array(
                'EmailAddress' => $mailTo[0]['email'],
                'FriendyName' => $mailTo[0]['name']
            )),
            'From' => array(
                'EmailAddress' => $mailFrom['email'],
                'FriendlyName' => $mailFrom['name']
            ),
            'ReplyTo' => array(
                'EmailAddress' => $mailFrom['email'],
                'FriendlyName' => $mailFrom['name']
            )
        );

        if (isset($messageTexts['html']))
            $messagesData['HtmlBody'] = $messageTexts['html'];

        if (count($mailTo) > 1)
        {
            $messagesData['Cc'] = [];

            for ($i = 1; $i < count($mailTo); $i++)
            {
                $messagesData['Cc'][] = [
                    'EmailAddress' => $mailTo[$i]['email'],
                    'FriendlyName' => $mailTo[$i]['name']
                ];
            }
        }

        if (count($attachments) > 0)
            $messagesData['Attachments'] = $attachments;

        $data->Messages = array($messagesData);

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
