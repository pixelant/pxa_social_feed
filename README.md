# Pixelant #

### Pxa Social Feed ###

Allow to add facebook, instagram and twitter feeds on site.

### How to setup ? ###

There are 4 main steps how to make it work:

* Configure access tokens and feeds configuration for facebook, instagram and twitter in BE module.
* Add FE plugin on page.
* Include extension TypoScript in TS template of site
* Configure scheduler task

#### Create tokens using BE module. ####
In "Admin tools" find "Social Feed" module. First create new access tokens to be able to fetch feeds.
Access tokens types:

* Facebook. Create new app to get credentials. [developers page](https://developers.facebook.com/apps)

    1. App Id 
    2. App Secret

* Instagram OAuth. Register a new client to get credentials [developers page](https://www.instagram.com/developer/). 

    1. Client Id
    2. Client Secret

    After Client Id and Client Secret are set **press button "Generate Access Token".** 

    **IMPORTANT !!!** To get Access Token - Valid redirect URI should be "http://yoursiteurl.com/typo3/index.php"

* Twitter. [Obtaining access tokens](https://dev.twitter.com/oauth/overview)

    1. Consumer Key
    2. Consumer Secret
    3. Access Token
    4. Access Token Secret


#### Create configurations using BE module. ####
In configuration there are next fields:

* Configuration name - *just a custom name*
* Social ID - *ID of account where to fetch feed (for example, "pixelant.net" - Pixelant ID on a facebook)*
* Limit - *minimum number of records to keep and fetch from feed at once*
* Token - *choose access token*

#### Add plugin on a page. ####
Create new content element and on "**Plugins**" tab find "**Social Feed**".

#### Plugin Options ####

* Amount of feeds on page - *limit of feeds to show at once*
* Load likes count - *show like count for posts or no*
* Choose configuration for plugin - *configurations to show on page*

#### Include TS template ####
Include static (from extensions): "Pxa Social Feed (pxa_social_feed)"

### Scheduler tasks ###
There are two scheduler tasks:

* Social Feed Import - *Import feeds from social network*
* Clean up obsolete entries - *Remove obsolete social feed entries*

#### Social Feed Import ####
Need only to set Frequency and choose configurations.

#### Clean up obsolete entries ####
Remove obsolete records for given number of days. It won't remove records if amount is less than limit in configuration.