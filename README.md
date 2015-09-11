### Pixelant Social Media Feed Extension Manual ###

Extension is importing feeds from social network and shows mixed feed. For now Facebook and Instagram are supported.

To install extension:
1. copy (or git clone) it to /typo3conf/ext/ folder.
2. enable it in extension manager.
3. include 'Social Media Feed Reader (pxa_social_feed)' to template (Template -> Info/Modify -> Edit the whole template record -> Includes).

There are 3 important constants in extension's configuration (plugin.tx_pxasocialfeed):
facebookID -- facebook page ID.
instagramID -- instagram user ID.
limit -- amount of records to show in feed.

facebook page ID can be found by page name using service like http://findmyfbid.com/
instagram user ID can be found by username using service like http://jelled.com/instagram/lookup-user-id

To run import in backend click on 'Social Import' link in tab 'ADMIN TOOLS'.

To add frontend plugin create new content element Plugins -> General Plugin -> Show Social Feed.