<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    public function send($to_email, $to_name, $subject, $template, $vars = null)
    {
        // Récupération du template
        $content = file_get_contents(dirname(__DIR__).'/Mail'.$template);

        // Remplacer les variables dans le template
        if ($vars) {
            foreach ($vars as $key => $var) {
                $content = str_replace('{'.$key.'}', $var, $content);
            }
        }

        $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'], $_ENV['MJ_APIKEY_PRIVATE'], true, ['version' => 'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "oks-shop@sowcoder.com",
                        'Name' => "OKS-shop"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TempleteID' => 1233456,
                    'TempleteLangage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content
                    ]
                ]
            ]
        ];

        $mj->post(Resources::$Email, ['body' => $body]);
    }
}
