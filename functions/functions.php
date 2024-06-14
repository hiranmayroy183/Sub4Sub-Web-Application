<?php
function getYoutubeChannelName($channelLink) {
    if (strpos($channelLink, 'youtube.com/channel/') !== false) {
        $channelID = substr($channelLink, strpos($channelLink, 'youtube.com/channel/') + strlen('youtube.com/channel/'));
        if (strpos($channelID, '&') !== false) {
            $channelID = substr($channelID, 0, strpos($channelID, '&'));
        }
        $channelInfoURL = 'https://www.youtube.com/channel/' . $channelID;
        $channelPage = file_get_contents($channelInfoURL);
        $startTag = '<title>';
        $endTag = ' - YouTube</title>';
        $startPos = strpos($channelPage, $startTag) + strlen($startTag);
        $endPos = strpos($channelPage, $endTag, $startPos);
        $channelName = substr($channelPage, $startPos, $endPos - $startPos);
        return $channelName;
    } else {
        return "Not a valid YouTube channel link";
    }
}
?>
