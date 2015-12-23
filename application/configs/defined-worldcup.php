<?php

define('SITE_ID', 1002565); //The thao
define('WORLD_CUP', 1003570); // Wordcup 2014
define('CATE_ID_TINTUC', 1003598);
define('CATE_ID_BINHLUAN', 1003599);
define('CATE_ID_BENLE', 1003600);
define('CATE_ID_PHOTO', 1003579);
define('CATE_ID_VIDEO', 1003580);
define('CATE_ID_LICHSU', 1003581);
switch (APPLICATION_ENV)
{
    case 'development': //DEV
        define('LEAGUE_ID', 16);
        define('SEASON_ID', 155);

        define('SITE_KINHDOANH', 1003159); //KINH DOANH
        define('SITE_DULICH', 1003280); //DU LỊCH
        define('SITE_DOISONG', 1002699); //THE THAO
        define('SITE_GIAITRI', 1002589); //GIAI TRI
        define('SITE_SOHOA', 1002592); //SO HOA
        define('SITE_VNE', 1000000); //VNE
        define('SITE_IONE', 1002722); //IONE
        define('SITE_NGOISAO', 1002835); //NGOI SAO

        break;
    case 'qc': //qc
        break;
    case 'production': //PRODUCTION
    default:
        break;
}
/* $arrGroup = array(
  'group-a' => array(),
  'group-b' => array(),
  'group-c' => array(),
  'group-d' => array(),
  'group-e' => array(),
  'group-f' => array(),
  'group-g' => array(),
  'group-h' => array()
  ); */
//Define prefix key
define('CACHING_PREFIX', 'vnett_new:');
//define DEVICE_ENV
defined('DEVICE_ENV') || define('DEVICE_ENV', 4);
switch (DEVICE_ENV)
{
    case 1: //MOBILE
        //Define limit 13 tin/ 1 trang
        define('LIMIT_LIST', 13);
        //Define load more
        define('LIMIT_LIST_MORE', 10);
        //Define limit 16 tin/ 1 trang folder
        define('LIMIT_LIST_CATE', 16);
        //Define limit 4 tin top home
        define('LIMIT_TOP', 3);
        //Define limit 20 photo
        define('LIMIT_LIST_PHOTO', 12);
        //Define limit 12 video
		define('LIMIT_LIST_VIDEO', 3);
        //Define limit 10 video
        define('LIMIT_LIST_VIDEO_MORE', 3);
        //Define limit 4 video relate in detail article
        define('LIMIT_RELATED_VIDEO', 4);
        define('LIMIT_PHOTO', 5);
        define('LIMIT_VIDEO', 6);
        //Define limit 10 tin / detail (other news)
        define('LIMIT_OTHER_NEWS', 10);
        //Define limit 2 tin relate player / detail
        define('LIMIT_NEWS_PLAYER', 2);
		//Define limit match in detail team football
        define('LIMIT_MATCH_IN_TEAM', 1);
		//Define limit photo & show more
        define('LIMIT_PHOTO_DETAIL', 24);
        define('LIMIT_PHOTO_MORE', 10);
        //Define limit slideshow photo
        define('LIMIT_SLIDESHOW_PHOTO', 100);
		//Define limit topic detail
        define('LIMIT_TOPIC_DETAIL', 2);
		//Define limit 5 tin relate  player
        define('LIMIT_RELATED_PLAYER', 5);
        //Define caption photo
        define('LIMIT_CAPTION_PHOTO', 50);
        define('LIMIT_COUNT_CAPTION_PHOTO', 65);
        //Define limit item box fixture
        define('LIMIT_FIXTURE', 2);
        define('LIMIT_TOP_SCORER', 20);
        break;
    case 2:
        define('LIMIT_FIXTURE', 3);
        break;
    default: //PC
        //Define limit 16 tin/ 1 trang
        define('LIMIT_LIST', 16);
        //Define limit 19 tin/ 1 trang folder
        define('LIMIT_LIST_CATE', 19);
        //Define limit 4 tin top home
        define('LIMIT_TOP', 4);
        //Define limit 20 photo
        define('LIMIT_LIST_PHOTO', 12);
        //Define limit 12 video
        define('LIMIT_LIST_VIDEO', 3);
        //Define limit 4 video relate in detail article
        define('LIMIT_RELATED_VIDEO', 4);
        define('LIMIT_PHOTO', 6);
        define('LIMIT_VIDEO', 8);
        //Define limit 14 tin / detail (other news)
        define('LIMIT_OTHER_NEWS', 14);
        //Define limit 2 tin relate player / detail
        define('LIMIT_NEWS_PLAYER', 2);
		//Define limit 5 tin relate  player
        define('LIMIT_RELATED_PLAYER', 5);
		//Define limit match in detail team football
        define('LIMIT_MATCH_IN_TEAM', 3);
		//Define limit photo & show more
        define('LIMIT_PHOTO_DETAIL', 24);
        define('LIMIT_PHOTO_MORE', 20);
        //Define limit slideshow photo
        define('LIMIT_SLIDESHOW_PHOTO', 100);
		//Define limit topic detail
        define('LIMIT_TOPIC_DETAIL', 2);
        //Define limit item box fixture
        define('LIMIT_FIXTURE', 4);
		define('LIMIT_TOP_SCORER',20);
        break;
}//end switch

define('COMMENT_TYPE', 2); // Che do duyet comment (1: hậu kiểm; 2: tiền kiểm)
//Link quảng cáo trên video
define('ADS_LINK', 'http://180.148.142.153/Cpx.aspx?s=35&r=0&c=2&p=604&n=22806581467&f=0&fm=1');
//Link quang cao trang chi tiet tin tuc
define('ADS_LINK_DETAIL', 'http://180.148.142.153/Cpx.aspx?s=39&r=0&c=6&p=604&n=22806581467&f=0&fm=1');

define('ARTICLE', 1); //1: article
define('VIDEO', 2); //2: video
define('ALBUM', 3); //3: album
define('TYPE_ALL', 0);
define('TYPE_ARTICLE', 1);
define('TYPE_VIDEO', 2);
define('TYPE_PHOTO', 3);



//define object type
define('OBJECT_TYPE_SONG', 1);
define('OBJECT_TYPE_MVIDEO', 3);
define('OBJECT_TYPE_ARTICLE', 7);
define('OBJECT_TYPE_TEAM', 12);
define('OBJECT_TYPE_MATCH', 11);
define('OBJECT_TYPE_PLAYER', 13);
define('OBJECT_TYPE_TENNIS', 14);
define('OBJECT_TYPE_MATCH_ARTICLE', 28);
define('OBJECT_TYPE_DOVUI', 7);
define('AWARD_DAY_DOVUI', 1);
define('AWARD_DOVUI', 2);

//define domain vnex
define('DOMAIN_VNE', 'vnexpressdev.net');
//define comment sig key
define('COMMENT_SIGN_ID', '7fe8e168fc1cb29fa07be');
define('ACTIVE_CAPCHA', 1); // 1-TRUE, 0-FALSE

//define dovui
define('DOVUI_SDATE', '1402333200');//2014-06-10
define('DOVUI_EDATE', '1405960560');//2014-07-21
define('DOVUI_EDATE_NORMAL', '2014-07-21 08:00:00');//2014-07-21