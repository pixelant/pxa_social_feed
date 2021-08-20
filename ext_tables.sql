#
# Table structure for table 'tx_pxasocialfeed_domain_model_feed'
#
CREATE TABLE tx_pxasocialfeed_domain_model_feed
(

    uid                 int(11)                         NOT NULL auto_increment,
    pid                 int(11)             DEFAULT '0' NOT NULL,

    post_date           int(11)             DEFAULT NULL,
    post_url            varchar(255)        DEFAULT ''  NOT NULL,
    message             text                            NOT NULL,
    image               text                            NOT NULL,
    small_image               text                      NOT NULL,
    title               varchar(255)        DEFAULT ''  NOT NULL,
    likes               int(11) unsigned    DEFAULT '0',
    external_identifier varchar(255)        DEFAULT ''  NOT NULL,
    update_date         int(11)             DEFAULT NULL,
    configuration       int(11) unsigned    DEFAULT '0',
    media_type          int(11) unsigned    DEFAULT '1',

    type                varchar(100)                    NOT NULL DEFAULT '0',
    tstamp              int(11) unsigned    DEFAULT '0' NOT NULL,
    crdate              int(11) unsigned    DEFAULT '0' NOT NULL,
    cruser_id           int(11) unsigned    DEFAULT '0' NOT NULL,
    deleted             tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden              tinyint(4) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)

);

#
# Table structure for table 'tx_pxasocialfeed_domain_model_token'
#
CREATE TABLE tx_pxasocialfeed_domain_model_token
(

    uid                 int(11)                         NOT NULL auto_increment,
    pid                 int(11)             DEFAULT '0' NOT NULL,

    name                varchar(55)         DEFAULT ''  NOT NULL,
    type                int(11)             DEFAULT '1' NOT NULL,
    be_group            varchar(255)        DEFAULT '0' NOT NULL,

    # Facebok & Instagram
    app_id              varchar(55)         DEFAULT ''  NOT NULL,
    app_secret          varchar(255)        DEFAULT ''  NOT NULL,
    access_token        varchar(255)        DEFAULT ''  NOT NULL,

    # Twitter, access_token already exist
    api_key             varchar(255)        DEFAULT ''  NOT NULL,
    api_secret_key      varchar(255)        DEFAULT ''  NOT NULL,
    access_token_secret varchar(255)        DEFAULT ''  NOT NULL,

    tstamp              int(11) unsigned    DEFAULT '0' NOT NULL,
    crdate              int(11) unsigned    DEFAULT '0' NOT NULL,
    cruser_id           int(11) unsigned    DEFAULT '0' NOT NULL,
    deleted             tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden              tinyint(4) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)

);

#
# Table structure for table 'tx_pxasocialfeed_domain_model_config'
#
CREATE TABLE tx_pxasocialfeed_domain_model_configuration
(

    uid       int(11)                         NOT NULL auto_increment,
    pid       int(11)             DEFAULT '0' NOT NULL,

    name      varchar(255)        DEFAULT ''  NOT NULL,
    image_size varchar(255)       DEFAULT 'normal_images'  NOT NULL,
    social_id varchar(255)        DEFAULT ''  NOT NULL,
    token     int(11) unsigned    DEFAULT '0',
    max_items int(11) unsigned    DEFAULT '0',
    storage   int(11)             DEFAULT '0' NOT NULL,
    be_group  varchar(255)        DEFAULT '0' NOT NULL,

    tstamp    int(11) unsigned    DEFAULT '0' NOT NULL,
    crdate    int(11) unsigned    DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned    DEFAULT '0' NOT NULL,
    deleted   tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden    tinyint(4) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)

);
