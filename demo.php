<?php

namespace Solution;

define('PATH_TO_ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('PATH_TO_ASSETS', PATH_TO_ROOT . 'assets' . DIRECTORY_SEPARATOR);

spl_autoload_register(function ($className) {
    $parts = explode('\\', $className);
    include end($parts) . '.php';
});

$customerRawSilenceData = file(PATH_TO_ASSETS . 'customer-channel.txt');
$userRawSilenceData     = file(PATH_TO_ASSETS . 'user-channel.txt');

$customerAudioParser = new AudioParser($customerRawSilenceData);
$userAudioParser     = new AudioParser($userRawSilenceData);

$conversationParser = new ConversationParser();
$conversationParser->addAudioChannel('customer', $customerAudioParser);
$conversationParser->addAudioChannel('user', $userAudioParser);

$output = [
    'longest_user_monologue'     => number_format($conversationParser->getChannelLongestUninterruptedMonologue('user'), 2),
    'longest_customer_monologue' => number_format($conversationParser->getChannelLongestUninterruptedMonologue('customer'), 2),
    'user_talk_percentage'       => number_format($conversationParser->getChannelTalkPercentage('user'), 2),
    'user'                       => $conversationParser->getChannelAudioPoints('user'),
    'customer'                   => $conversationParser->getChannelAudioPoints('customer'),
];  

print_r(json_encode($output, JSON_PRETTY_PRINT));
