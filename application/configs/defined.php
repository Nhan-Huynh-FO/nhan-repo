<?php

define('SITE_ID', 1002565); //The thao
define('VOTE_ID_1', 22); // Vote id
define('VOTE_ID_2', 23); // Vote id
define('BLOCK_ALBUM_ANH', 1002586); //Anh


// Name for gearman
define('CATE_ID_PHOTO', 1002586);
define('CATE_ID_VIDEO', 1002566);
define('CATE_ID_BONGDA', 1002567);
define('CATE_ID_NGOAIHANGANH', 1002580);
define('CATE_ID_CHAMPIONSLEAGUE', 1002575);
define('CATE_ID_LALIGA', 1002582);  //1002582);
define('CATE_ID_SERIA', 1002581);   //1002581);
define('CATE_ID_CACGIAIKHAC', 1002584); //1002584);
define('CATE_ID_CACMONKHAC', 1002623);  //1002584);
define('CATE_ID_TENNIS', 1002619);
define('CATE_ID_AFFCUP', 1003030);
define('CATE_ID_BONGDATRONGNUOC', 1002568);
define('CATE_ID_CHESS', 1003158);   /*Cate Co vua*/

define('CATE_ID_BUNDESLIGA', 1002583);
define('CATE_ID_FIXTURE', 1002571); /*Cate Lich thi dau*/
define('CATE_ID_BEHINDS_SCENES', 1002570);  /*Cate Hau truong*/
define('CATE_ID_PORTRAIT', 1002572);        /*Cate Chan dung*/
define('CATE_ID_INTERVIEW', 1003141);       /*Cate Phong van truc tuyen*/
define('CATE_ID_GOLF', 1003163);            /* Cate Golf */
define('CATE_ID_RACING', 1003495);            /* Cate đua xe */
define('CATE_ID_TAITRO', 1003455);
define('CATE_ID_TUONGTHUAT', 1003044);

define('CATE_ID_PHOTO_WORLDCUP', 1003579);
//End check ENV to define

//Define cate SEA Games
define('SEA_GAMES', 1003355);
define('CATE_SEA_GAMES_BONGDA', 1003759);
define('CATE_SEA_GAMES_CACMONKHAC', 1003762);
define('CATE_SEA_GAMES_BINHLUAN', 1003763);
define('CATE_SEA_GAMES_BENLE', 1003764);
define('CATE_SEA_GAMES_THONGTIN', 1003765);

//Define prefix key
define('CACHING_PREFIX', 'vnett_new:');
//Define article type
define('ARTICLE',1);//1: article
define('VIDEO',2);//2: video
define('ALBUM',3);//3: album
defined('DEVICE_ENV') || define('DEVICE_ENV', 4);

define('LEAGUE_ID', 16); //league_id worldcup
define('SEASON_ID', 153); //season_id worldcup
switch (DEVICE_ENV)
{
    case 1: //MOBILE
        //Define limit topic detail
        define('LIMIT_TOPIC_DETAIL', 5);
        define('LIMIT_TOPIC_DETAIL_STYLE_1', 5);
        define('LIMIT_TOPIC_DETAIL_STYLE_2', 2);
        //Define limit 4 tin top home
        define('LIMIT_TOP', 3);
        //Define limit 15 tin/ 1 trang
        define('LIMIT_LIST', 15);
        define('LIMIT_LIST_MORE', 10);

        //Define limit 14 tin / detail (other news)
        define('LIMIT_OTHER_NEWS', 10);
        //Define limit 6 tin top view in detail
        define('LIMIT_TOP_VIEW', 5);
        //Define limit 3 video relate in detail article
        define('LIMIT_RELATED_VIDEO', 3);
        //Define limit slideshow photo
        define('LIMIT_SLIDESHOW_PHOTO', 100);
        //Define caption photo
        define('LIMIT_CAPTION_PHOTO', 50);
        define('LIMIT_COUNT_CAPTION_PHOTO', 65);
        //Define limit match in detail team football
        define('LIMIT_MATCH_IN_TEAM', 1);
        //Define limit photo & show more
        define('LIMIT_PHOTO', 24);
        define('LIMIT_PHOTO_MORE', 20);
        break;
    default: //PC
        //Limit topic detail 5 tin
        define('LIMIT_TOPIC_DETAIL', 5);
        define('LIMIT_TOPIC_DETAIL_STYLE_1', 5);
        define('LIMIT_TOPIC_DETAIL_STYLE_2', 3);
        //Define limit 4 tin top home
        define('LIMIT_TOP', 4);
        //Define limit 29 tin/ 1 trang
        define('LIMIT_LIST', 29);
        define('LIMIT_LIST_MORE', 29);
        //Define limit 14 tin / detail (other news)
        define('LIMIT_OTHER_NEWS', 14);
        //Define limit 6 tin top view in detail
        define('LIMIT_TOP_VIEW', 5);
        //Define limit 3 video relate in detail article
        define('LIMIT_RELATED_VIDEO', 3);
        //Define limit slideshow photo
        define('LIMIT_SLIDESHOW_PHOTO', 100);
        //Define caption photo
        define('LIMIT_CAPTION_PHOTO', 20);
        define('LIMIT_COUNT_CAPTION_PHOTO', 45);
        //Define limit match in detail team football
        define('LIMIT_MATCH_IN_TEAM', 3);
        //Define limit photo & show more
        define('LIMIT_PHOTO', 24);
        define('LIMIT_PHOTO_MORE', 20);
        break;
}//end switch

//Define tin lien quan
define('RELATE_NEWS', 2);

define('COMMENT_TYPE', 2);// Che do duyet comment (1: hậu kiểm; 2: tiền kiểm)

//Link quảng cáo trên video
define('ADS_LINK', 'http://180.148.142.153/Cpx.aspx?s=35&r=0&c=2&p=604&n=22806581467&f=0&fm=1');

define('TYPE_ALL', 0);
define('TYPE_PHOTO', 3);
define('TYPE_VIDEO', 2);
define('TYPE_ARTICLE', 1);

//define object type
define('OBJECT_TYPE_SONG', 1);
define('OBJECT_TYPE_MVIDEO', 3);
define('OBJECT_TYPE_ARTICLE', 7);
define('OBJECT_TYPE_TEAM',12);
define('OBJECT_TYPE_MATCH',11);
define('OBJECT_TYPE_PLAYER',13);
define('OBJECT_TYPE_TENNIS',14);
define('OBJECT_TYPE_MATCH_ARTICLE', 28);
define('OBJECT_TYPE_DOVUI', 7);
define('AWARD_DAY_DOVUI', 1);
define('AWARD_DOVUI', 2);

//define Limit User Post question Interview, enable Captcha
define('LIMIT_POST_PVTT_CAPTCHA', 3);

define('GROUP_FOOTBAL_VN', 2001);
define('GROUP_FOOTBAL_QT', 1001);
//define domain vnex
define('DOMAIN_VNE', 'vnexpressdev.net');
//define comment sig key
define('COMMENT_SIGN_ID', '7fe8e168fc1cb29fa07be');
//define video sugess
define('LIMIT_VIDEO_MORE', 9);
//Limit top 5 tin lien quan object
define('LIMIT_OBJECT_NEW', 5);