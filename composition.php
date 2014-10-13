<?php

require 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

$config = require '.env.php';

$client = new Client([
    'base_url' => 'https://api.twitter.com',
    'defaults' => [
        'auth' => 'oauth',
    ],
]);

$oauth = new Oauth1([
    'consumer_key'    => $config['TWITTER_CONSUMER_KEY'],
    'consumer_secret' => $config['TWITTER_CONSUMER_SECRET'],
    'token'           => $config['TWITTER_ACCESS_TOKEN'],
    'token_secret'    => $config['TWITTER_ACCESS_TOKEN_SECRET'],
]);
$client->getEmitter()->attach($oauth);

$timeline = $client->get('1.1/statuses/home_timeline.json?count=200')->json();
$stats = [];

foreach ($timeline as $tweet)
{
    $text = $tweet['text'];
    $user = '@' . $tweet['user']['screen_name'];

    if (!isset($stats[$user]))
    {
        $stats[$user] = 0;
    }

    $stats[$user]++;
}

arsort($stats);

$totalNumberOfTweets = sizeof($timeline);
echo "Total number of tweets: $totalNumberOfTweets\n\n";

foreach ($stats as $user => $count)
{  
    $percent = round($count / $totalNumberOfTweets * 100, 2);
    echo "$user: $count ($percent%)\n";
}