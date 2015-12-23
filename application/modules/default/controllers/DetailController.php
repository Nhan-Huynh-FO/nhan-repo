<?php

/**
 * @author      :   HungNT1
 * @name        :   IndexController
 * @copyright   :   Fpt Online
 * @todo        :   Index Controller
 */
class DetailController extends Zend_Controller_Action
{

    /**
     * @todo - VnE Thethao detail page
     * @author HungNT1
     */
    public function indexAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);
        //Get article id
        $intArticleId = (int) $this->_request->getParam('id', 0);
        //Get page
        $intPage = max(1, $this->_request->getParam('page', 1));
        $viewApp = $this->_request->getParam('view', '');
        if ($intArticleId < 1)
        {
            $this->_redirect('/');
            return;
        }

        //get detail full of article
        $arrArticleDetail = $this->view->objArticle->getArticleFull($intArticleId);

        //init category of article detail
        $intCategoryId = $arrArticleDetail['category_id'];

        //check other
        $arrCateOther = array(SEA_GAMES, CATE_SEA_GAMES_BENLE, CATE_SEA_GAMES_BONGDA, CATE_SEA_GAMES_BINHLUAN, CATE_SEA_GAMES_CACMONKHAC, CATE_SEA_GAMES_THONGTIN);

        //check cate in array other
        if (in_array($intCategoryId, $arrCateOther))
        {
            $link_other = str_replace('http://thethao.vnexpress.net', 'http://seagames.vnexpress.net', $arrArticleDetail['share_url']);
            if ($intPage > 1)
            {
                $link_other = str_replace('.html','-p'.$intPage.'.html', $link_other);
            }

            $this->_redirect($link_other . (in_array($viewApp, array('app', 'app_sport_live')) ? '?view=' . $viewApp : ''), array('code' => 301));
        }

        //check public time and now time
        if (intval($arrArticleDetail['publish_time']) > time())//is schedule article
        {
            $sig = md5($intArticleId . $arrArticleDetail['creation_time']); //gen sig

            if ($sig != $this->_request->getParam('sig'))//secr not match
            {
                $this->_redirect($this->view->configView['linkerror'][DEVICE_ENV], array('code'=>301));
            }
        }

        //Valid data
        if (empty($arrArticleDetail))
        {
            $this->_redirect("/");
        }

        if ($arrArticleDetail['privacy'] & 64)
        {
            $this->_redirect($this->view->configView['url_vne'] . "/phong-van-truc-tuyen/" . Fpt_Filter::setSeoLink($arrArticleDetail['title_format']) . '-' . $arrArticleDetail['article_id'] . '.html');
        }
        //str link
        $strLink = $this->view->configView['url'] . $this->_request->getPathInfo();

        //forward to live action if article type is 5
        if ($arrArticleDetail['article_type'] == 5)
        {
            //check live match is redirect to match
            if (isset($arrArticleDetail['list_object_type']) && !empty($arrArticleDetail['list_object_type']))
            {
                if (isset($arrArticleDetail['list_object_type'][OBJECT_TYPE_MATCH_ARTICLE])
                        && count($arrArticleDetail['list_object_type'][OBJECT_TYPE_MATCH_ARTICLE] == 1))
                {
                    //get match detail
                    $matchId = $arrArticleDetail['list_object_type'][OBJECT_TYPE_MATCH_ARTICLE][0];
                    $this->view->objObject->getMatch()->setId($matchId);
                    $matchDetail = $this->view->objObject->getMatch()->getDetailObjectBasic($matchId);
                    if (!empty($matchDetail))
                    {
                        $link_other = str_replace('/thong-ke/', '/tuong-thuat/', $matchDetail['share_url']) . (in_array($viewApp, array('app', 'app_sport_live')) ? '?view=' . $viewApp : '');
                        $this->_redirect($link_other, array('code' => 301));
					}
                }
            }
            //check page tuong thuat and redirect to tuong thuat or tong thuat
            if ($arrArticleDetail['category_id'] == CATE_ID_TUONGTHUAT)
            {
                //check living or not
                if (($arrArticleDetail['privacy'] & 8) == 8)
                {
                    $strLink = preg_replace('#/tin-tuc/tuong-thuat/#', '/tuong-thuat/', $strLink);
                }
                else
                {
                    //get data page extend
                    $arrDataExtend = $this->view->objArticle->getDetailPageExtend($intArticleId, 2);
                    if (!empty($arrDataExtend) && (!empty($arrDataExtend['content'])))
                    {
                        unset($arrDataExtend['creation_time'],$arrDataExtend['publish_time_format'],$arrDataExtend['publish_time']);
                        //merge page extend to data detail
                        $arrArticleDetail = array_merge($arrArticleDetail, (array) $arrDataExtend);
                        //get link remove -p2
                        $strLink = preg_replace('#/tin-tuc/tuong-thuat/#', '/tong-thuat/', $strLink);
                    }
                    else
                    {
                        $strLink = preg_replace('#/tin-tuc/tuong-thuat/#', '/tuong-thuat/', $strLink);
                    }
                }
                if (strpos($strLink, '/tuong-thuat/') !== FALSE || (strpos($strLink, '/tong-thuat/') !== FALSE)) {
                    $strLink = $strLink . (in_array($viewApp, array('app', 'app_sport_live')) ? '?view=' . $viewApp : '');
                    $this->_redirect($strLink, array('code' => 301));
                }
            }
            return $this->_forward('live');
        }

        //get category on breakcumb (Using when view page > 2)
        $page = 1;
        //check page of detail
        if ($intPage > 1)
        {
            //get data page extend
            $arrDataExtend = $this->view->objArticle->getDetailPageExtend($intArticleId, $intPage);
            if (!empty($arrDataExtend))
            {
                unset($arrDataExtend['creation_time'],$arrDataExtend['publish_time_format'],$arrDataExtend['publish_time']);

                //merge page extend to data detail
                $arrArticleDetail = array_merge($arrArticleDetail, (array) $arrDataExtend);

                //Check page_type popup
                if (2 == $arrDataExtend['page_type'])
                {
                    //Disable layout
                    $this->_helper->layout->disableLayout(true);
                    //Set no render view
                    $this->_helper->viewRenderer->setNoRender(true);
                    $this->view->assign(array(
                        'arrData' => $arrArticleDetail
                    ));
                    echo $this->view->render('detail/popup.phtml');
                    exit;
                }//end if
                //get link remove -p2
                $strLink = preg_replace('#(-p[\d]+\.html)#', '.html', $strLink);
                $page = $intPage;
            }
            else
            {
                $strLink = '';
            }
        }
        //check redirect link
        if ($strLink != $arrArticleDetail['share_url'] && APPLICATION_ENV != 'development')
        {
            $link_more = $arrArticleDetail['share_url'];
            if ($page > 1)
            {
                $link_more = str_replace('.html','-p'.$page.'.html',$arrArticleDetail['share_url']);
            }
            $this->_redirect($link_more . ($viewApp == 'app' ? '?view=app':''), array('code' => 301));
        }

        //check view app
        if (!empty($viewApp) && $viewApp == 'app')
        {
            //Disable layout
            $this->_helper->layout->disableLayout(true);

            $content = $arrArticleDetail['content'];
            $content = preg_replace('/src="(.[^>]*)_500x0.(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
            $content = preg_replace('/src="(.[^>]*).(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
            $content = preg_replace('/src="(.[^>]*)_m_460x0_m_460x0.(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
            $arrArticleDetail['content'] = $content;

            //Trim data
            $arrArticleDetail['title']			= trim($arrArticleDetail['title']);
            $arrArticleDetail['title_format']	= trim($arrArticleDetail['title_format']);
            $arrArticleDetail['lead']			= trim($arrArticleDetail['lead']);

            // Assign to view
            $this->view->assign(array(
                'arrArticleDetail'  => $arrArticleDetail,
                'intCategoryId'        => $intCategoryId,
                'intArticleId'         => $intArticleId,
                'ogType' => 'website',
                'ogTitle' => $arrArticleDetail['title_format'] . ' - VnExpress Thể Thao',
                'ogUrl' => $arrArticleDetail['share_url'],
                'ogImage' => !empty($arrArticleDetail['thumbnail_url']) ? $this->view->ImageurlArticle($arrArticleDetail, 'size2') : 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
                'ogDescription' => trim(strip_tags(html_entity_decode($arrArticleDetail['lead'], ENT_COMPAT, 'UTF-8')))
            ));
            // SEO
            $arrKeyword = array();
            $arrArticleDetail['title'] = html_entity_decode($arrArticleDetail['title'], ENT_QUOTES, 'UTF-8');
            $this->view->headTitle($arrArticleDetail['title'] . ' - VnExpress Thể Thao');
            $this->view->headMeta()->setName('description', trim(strip_tags(html_entity_decode($arrArticleDetail['lead'], ENT_COMPAT, 'UTF-8'))))
                ->setName('keywords', join(',', $arrKeyword));
            echo $this->view->render('detail/app.phtml');
            exit;
        }
        //get topic of article detail
		//set topic show style
        $topic_show_style   = 1;
        //init topic default
        $arrTopic = array();
        $arrTopicDetail = array();
		//set recommend
		$arrRecommend = array(
			'data' => array(),
			'total' => 0
		);
        if (!empty($arrArticleDetail['list_topic_id']))
        {
            //init model topic
            $modelTopic = new Fpt_Data_News_Topic;

            // init topic params
            $arrTopicParams = array(
                'site_id'   => 1000000,
                'topic_id'  => $arrArticleDetail['list_topic_id'],
                'limit'     => LIMIT_TOPIC_DETAIL + 1,
                'offset'    => 0,
                'withscore' => true,
                'article_type' => NULL
            );
            // get list article by topic
            $arrTopic = $modelTopic->getTopicNewsByScore($arrTopicParams);

            // check array topic data
            if (!empty($arrTopic))
            {
                $score = 0;
                $temp_topic_id = 0;

                foreach ($arrArticleDetail['list_topic_id'] as $index => $topic_id)
                {
                    if (isset($arrTopic[$topic_id]['data'][$intArticleId]))
                    {
                        unset($arrTopic[$topic_id]['data'][$intArticleId]);
						$arrTopic[$topic_id]['total'] = $arrTopic[$topic_id]['total'] - 1;
                    }
                    if (empty($arrTopic[$topic_id]['data']))
                    {
                        unset($arrArticleDetail['list_topic_id'][$index]);
						unset($arrTopic[$topic_id]);
                        continue;
                    }

                    // get first article to compare
                    $firstCore = current($arrTopic[$topic_id]['data']);

                    if ($firstCore > $score)
                    {
                        $temp_topic_id = $topic_id;
                        $score = $firstCore;
                    }
                }
                // get detail of topic id list
                $arrTopicDetail = $modelTopic->getDetailTopicByArrId(array($temp_topic_id));

				//shift data
				$arrShift   = isset($arrArticleDetail['list_topic_full'][$temp_topic_id])?$arrArticleDetail['list_topic_full'][$temp_topic_id]:array_shift($arrArticleDetail['list_topic_full']);

				//set topic style topic
				$topic_show_style = $arrShift['show_style'] != 2 ? 1 : $arrShift['show_style'];
				//array slice list article
				$arrTopic   = array_slice(array_keys($arrTopic[$temp_topic_id]['data']), 0, ($topic_show_style==1?LIMIT_TOPIC_DETAIL_STYLE_1:LIMIT_TOPIC_DETAIL_STYLE_2));
            }
        }
		
		//check neu empty data topic + link cheo moi xuat recommend
		if (empty($arrTopic) && empty($arrArticleDetail['list_reference_sites_id']))
		{
			// Recommendation
			$arrRecommendParams = array(
				'article_id' => $intArticleId,
				'limit' => LIMIT_TOPIC_DETAIL_STYLE_1 + 1,
				'offset' => 0
			);
			$arrRecommend = $this->view->objArticle->getRecommendationById($arrRecommendParams);
			//valid data
			if (!empty($arrRecommend['data']))
			{
				$arrRecommend['data'] = array_diff($arrRecommend['data'], array($intArticleId));
				$arrRecommend['data'] = array_slice($arrRecommend['data'], 0, LIMIT_TOPIC_DETAIL_STYLE_1);
				// set to article instance
				$this->view->objArticle->setIdBasic($arrRecommend['data']);
			}
		}
		
        //end get topic of article detail
        //get cate instance
        $modelCategory = Thethao_Model_Category::getInstance();
        // Get parent info
        $arrCateInfo    = $modelCategory->getDetailbyCateId($intCategoryId);
        $intOtherCate   = $intCategoryId;
        //check cate result
        if (!empty($arrCateInfo) && !empty($arrCateInfo['parent_id']))
        {
            //check parent_id
            if (SITE_ID != $arrCateInfo['parent_id'])
            {
                $intOtherCate = $arrCateInfo['parent_id'];
            }
        }

        //get other news of article detail from commendation
        $arrOrtherArticle = array();
        $instanceNews = Thethao_Model_News::getInstance();

        //get list top 15 article with rule 2
        $arrNewsArticle = $instanceNews->getListArticleIdsByRule2(
                array(  'category_id'   => $intOtherCate,
                        'limit'         => LIMIT_OTHER_NEWS + 1,
                        'offset' => 0,
                        'article_type' => NULL
                    )
                );
        if (count($arrNewsArticle['data']) > LIMIT_OTHER_NEWS)
        {
            $arrDiff = array_diff($arrNewsArticle['data'], array($intArticleId));
            $arrNewsArticle['data'] = array_slice($arrDiff, 0, LIMIT_OTHER_NEWS);
        }
        //set flag check exclude
        $flag = false;
        //check is empty list news
        if (is_array($arrNewsArticle) && !empty($arrNewsArticle['data']))
        {
            if (in_array($intArticleId, $arrNewsArticle['data']))
            {
                $flag = true;
                //excluse current article
                $arrOrtherArticle = array_diff($arrNewsArticle['data'], array($intArticleId));
            }
            else
            {
                $arrOrtherArticle = $arrNewsArticle['data'];
            }
            //then slice to get only 14 item
            $arrOrtherArticle = array_slice($arrOrtherArticle, 0, LIMIT_OTHER_NEWS);
        }//end check is empty latest news
        //end get other news of article detail

        //get top view article
        $arrTopViewID = array();
        if (DEVICE_ENV != 1)
        {
            //get top view with rule 4
            $arrTopViewData = $instanceNews->getListArticleIdsByRule4(array(
                                'category_id' => $intOtherCate,
                                'offset' => 0,
                                'limit' => (LIMIT_TOP_VIEW + 1)
                                )
                            );
            if (!empty($arrTopViewData))
            {
                //excluse current article
                $arrTopViewID = array_diff($arrTopViewData, array($intArticleId));
                $arrTopViewID = array_slice($arrTopViewID, 0, LIMIT_TOP_VIEW);
            }
        }
        //end get top view article

        //array merge article id to set id basic
        $arrArticleIds = array_unique(array_merge($arrTopic, $arrOrtherArticle, $arrTopViewID));
        $this->view->objArticle->setIdBasic($arrArticleIds);

        //Set category id for block_cate user in block & menu breakumb
        $this->_request->setParam('block_cate', $intCategoryId);

        //init cate for set menu
        $idCateMenu = $arrCateInfo['parent_id'] == SITE_ID ? $intCategoryId : $arrCateInfo['parent_id'];
        //set cate active
        $this->_request->setParam('cateActiveId', $intCategoryId);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', $idCateMenu);

        //get Instance Block
        $objBlock = Thethao_Plugin_Block::getInstance();
        //set exclude for detail
        $objBlock->setExclude(array($intArticleId));

        //replace image size for mobile
        if (DEVICE_ENV == 1)
        {
            $content = $arrArticleDetail['content'];
            $content = preg_replace('/src="(.[^>]*)_500x0.(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
            $content = preg_replace('/src="(.[^>]*).(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
            $content = preg_replace('/src="(.[^>]*)_m_460x0_m_460x0.(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
            $arrArticleDetail['content'] = $content;
        }
        //Trim data
		$arrArticleDetail['title']			= trim($arrArticleDetail['title']);
		$arrArticleDetail['title_format']	= trim($arrArticleDetail['title_format']);
		$arrArticleDetail['lead']			= trim($arrArticleDetail['lead']);

        // Assign to view
        $this->view->assign(array(
			'arrRecommend' => $arrRecommend,
            'arrNewsArticle'    => $arrNewsArticle,
            'arrOrtherArticle'  => $arrOrtherArticle,
            'flagExc' => $flag,
            'totalPageOtherNews' => intval(ceil($arrNewsArticle['total'] / LIMIT_OTHER_NEWS)),
            'isArticleHotVnE'   => $arrArticleDetail['privacy'] & 512,
            'arrTopViewID'        => $arrTopViewID,
            'parentCategoryID'  => $arrCateInfo['parent_id'],
            'arrArticleDetail'  => $arrArticleDetail,
            'intCategoryId'        => $intCategoryId,
            'intArticleId'         => $intArticleId,
            'intOtherCate'         => $intOtherCate,
            'intViewApp'    => empty($viewApp)? 0 : 1,
            'isAdsFullScreen' => $arrArticleDetail['privacy'] & 2048,
			'topic_show_style'   => $topic_show_style,
            'arrTopic'      => $arrTopic,
            'arrTopicDetail'    => $arrTopicDetail[$temp_topic_id],
            'ogType' => 'website',
            'ogTitle' => $arrArticleDetail['title_format'] . ' - VnExpress Thể Thao',
            'ogUrl' => $arrArticleDetail['share_url'],
            'ogImage' => !empty($arrArticleDetail['thumbnail_url']) ? $this->view->ImageurlArticle($arrArticleDetail, 'size2') : 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription' => trim(strip_tags(html_entity_decode($arrArticleDetail['lead'], ENT_COMPAT, 'UTF-8')))
        ));
        // SEO
        $arrKeyword = array();
        $arrArticleDetail['title'] = html_entity_decode($arrArticleDetail['title'], ENT_QUOTES, 'UTF-8');
        $this->view->headTitle($arrArticleDetail['title'] . ' - VnExpress Thể Thao');
        $this->view->headMeta()->setName('description', trim(strip_tags(html_entity_decode($arrArticleDetail['lead'], ENT_COMPAT, 'UTF-8'))))
            ->setName('keywords', join(',', $arrKeyword));

    }

    /**
     * function get other news in detail article
     * @author HungNT
     */
    public function othernewsAction()
    {
        //Disable layout
        $this->_helper->layout->disableLayout(true);

        //get all param
        $arrParam = $this->_request->getParams();
        // Get article detail
        $instanceNews = Thethao_Model_News::getInstance();
        $limitOtherNews = LIMIT_OTHER_NEWS;
        if (!$arrParam['flagExc'])
        {
            $arrParam['intLimit']++;
        }

        //Get news by rule 2 : folder X + liston folderX   => order by score
        $arrOtherNews = $instanceNews->getListArticleIdsByRule2(
                array(
                    'category_id' => $arrParam['intCate'],
                    'limit' => $arrParam['intLimit'],
                    'offset' => $arrParam['offset'],
                    'article_type' => NULL)
                );


        //set flag check excu
        $flag = false;
        //check data empty
        if (is_array($arrOtherNews) && !empty($arrOtherNews['data']))
        {
            if (in_array($arrParam['article_id'], $arrOtherNews['data']))
            {
                $flag = true;
                //get data diff Exclude and Limit
                $data_diff = array_slice(array_diff($arrOtherNews['data'], array($arrParam['article_id'])), 0, $limitOtherNews);
                $arrOtherNews['data'] = $data_diff;
            }
        }
        //valid data
        if (count($arrOtherNews['data']) > $limitOtherNews)
        {
            //Slice data
            $arrOtherNews['data'] = array_slice($arrOtherNews['data'], 0, $limitOtherNews);
        }
        $totalPage = intval(ceil($arrOtherNews['total'] / $limitOtherNews));
        $offsetNext = $arrParam['offset'] + $limitOtherNews;
        $offsetPrev = $arrParam['offset'] - $limitOtherNews;
        if ($flag)
        {
            $offsetNext++;
        }
        if ($arrParam['flagExc'])
        {
            $offsetPrev--;
        }

        $this->view->objArticle->setIdBasic($arrOtherNews['data']);
        $this->view->assign(array(
            'arrOrtherArticle' => $arrOtherNews['data'],
            'cateId' => $arrParam['intCate'],
            'intPage' => $arrParam['intPage'],
            'article_id' => $arrParam['article_id'],
            'offsetNext' => $offsetNext,
            'offsetPrev' => $offsetPrev,
            'totalPageOtherNews' => $totalPage,
            'flagExc' => $arrParam['flagExc'] ? false : $flag,
        ));
        $response['error'] = 0;
        $response['html'] = $this->view->render('detail/box/othernews.phtml');
        echo Zend_Json::encode($response);
        exit();
    }

    /**
     * @author      : VinhNT
     * @name        : photoAction
     * @copyright   : FPT Online
     * @todo        : photo Action
     */
    public function photoAction()
    {
        // set cache
        $this->_request->setParam('cache_file', 1);
        // get article id
        $id = (int) $this->_request->getParam('id', 0);
        // get view app
		$viewApp = $this->_request->getParam('view', '');
        // get article instance
        $artInstance = $this->view->objArticle;
        // get full detail article
        $arrData = $artInstance->getArticleFull($id);

        $intCategoryId = $arrData['category_id'];
        //check sea games
        $arrCateOther = array(SEA_GAMES, CATE_SEA_GAMES_BENLE, CATE_SEA_GAMES_BONGDA,
            CATE_SEA_GAMES_BINHLUAN, CATE_SEA_GAMES_CACMONKHAC, CATE_SEA_GAMES_THONGTIN, CATE_SEA_GAMES_ANH);

        //check cate in array other
        if (in_array($intCategoryId, $arrCateOther))
        {
            $link_other = str_replace('http://thethao.vnexpress.net', 'http://seagames.vnexpress.net', $arrData['share_url']);
            $this->_redirect($link_other . ($viewApp == 'app' ? '?view=app':''), array('code' => 301));
        }

        // check $arraData
        if (empty($arrData))
        {
            $this->_redirect('/');
        }
        // check publish time security
        // bai hen gio
        if (intval($arrData['publish_time']) > time())
        {
            $sig = md5($id . $arrData['creation_name']);
            if ($sig != $this->_request->getParam('sig'))
            {
                $this->_redirect($this->view->configView['linkerror'][DEVICE_ENV], array('code'=>301));
            }
        }
        // link string
        $strLink = $this->view->configView['url'] . $this->_request->getPathInfo();
        if (APPLICATION_ENV != 'development' && $strLink != $arrData['share_url'])
        {
            $this->_redirect($arrData['share_url'] . ($viewApp == 'app' ? '?view=app':''), array('code'=>301));
        }

        // set category id into cateid param
        $this->_request->setParam('cateid', CATE_ID_PHOTO);
        // set category id into block_cate param
        $this->_request->setParam('block_cate', CATE_ID_PHOTO);

        //Set params
        $arrParams = array(
            'article_id' => $arrData['article_id'],
            'limit' => LIMIT_PHOTO,
            'offset' => 0
        );
        // Get list object reference
        $arrReference = $artInstance->getObjectReferenceByArticleId($arrParams);

        //chia se anh
        $refer      = $this->_request->getParam('refer', 0);
        $selected   = 0;
        $thumbImage = '';
        if ( $refer > 0 )
        {
            $i  = 1;
            foreach ( $arrReference['data'] as $dt )
            {
                if ( $refer == $dt['reference_id'] )
                {
                    $selected   = $i; /// xac dinh vi tri can share
                    $thumbImage = $this->view->Imageurl($dt['url'], 'size2');
                    break;
                }//end if
                $i++;
            }//end foreach
        }//end if

        //check view app
        if (!empty($viewApp) && $viewApp == 'app')
        {
            //Disable layout
            $this->_helper->layout->disableLayout(true);

            //Trim data
            $arrData['title']			= trim($arrData['title']);
            $arrData['title_format']	= trim($arrData['title_format']);
            $arrData['lead']			= trim($arrData['lead']);

            // Assign to view
            $this->view->assign(array(
				'arrArticleDetail'  => $arrData,
				'arrPhoto'          => $arrReference['data'],
                'intCategoryId'        => $intCategoryId,
                'intArticleId'      => $id,
                'ogType' => 'website',
                'ogTitle' => $arrData['title_format'] . ' - VnExpress Thể Thao',
                'ogUrl' => $arrData['share_url'],
                'ogImage' => !empty($arrData['thumbnail_url']) ? $this->view->ImageurlArticle($arrData, 'size2', -1) : 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
                'ogDescription' => trim(strip_tags(html_entity_decode($arrData['lead'], ENT_COMPAT, 'UTF-8')))
            ));
            // SEO
            $arrKeyword = array();
            $arrData['title'] = html_entity_decode($arrData['title'], ENT_QUOTES, 'UTF-8');
            $this->view->headTitle($arrData['title'] . ' - VnExpress Thể Thao');
            $this->view->headMeta()->setName('description', trim(strip_tags(html_entity_decode($arrData['lead'], ENT_COMPAT, 'UTF-8'))))
                ->setName('keywords', join(',', $arrKeyword));
            echo $this->view->render('detail/app-photo.phtml');
            exit;
        }

        //get topic of article detail
        //set topic show style
        $topic_show_style   = 1;
        //init topic default
        $arrTopic = array();
        $arrTopicDetail = array();
		//set recommend
		$arrRecommend = array(
			'data' => array(),
			'total' => 0
		);
        if (!empty($arrData['list_topic_id']))
        {
            //init model topic
            $modelTopic = new Fpt_Data_News_Topic;

            // init topic params
            $arrTopicParams = array(
                'site_id'   => 1000000,
                'topic_id'  => $arrData['list_topic_id'],
                'limit'     => LIMIT_TOPIC_DETAIL + 1,
                'offset'    => 0,
                'withscore' => true,
                'article_type' => NULL
            );
            // get list article by topic
            $arrTopic = $modelTopic->getTopicNewsByScore($arrTopicParams);

            // check array topic data
            if (!empty($arrTopic))
            {
                $score = 0;
                $temp_topic_id = 0;

                foreach ($arrData['list_topic_id'] as $index => $topic_id)
                {
                    if (isset($arrTopic[$topic_id]['data'][$id]))
                    {
                        unset($arrTopic[$topic_id]['data'][$id]);
						$arrTopic[$topic_id]['total'] = $arrTopic[$topic_id]['total'] - 1;
                    }
                    if (empty($arrTopic[$topic_id]['data']))
                    {
                        unset($arrData['list_topic_id'][$index]);
						unset($arrTopic[$topic_id]);
                        continue;
                    }

                    // get first article to compare
                    $firstCore = current($arrTopic[$topic_id]['data']);

                    if ($firstCore > $score)
                    {
                        $temp_topic_id = $topic_id;
                        $score = $firstCore;
                    }
                }
                // get detail of topic id list
                $arrTopicDetail = $modelTopic->getDetailTopicByArrId(array($temp_topic_id));

                //shift data
                $arrShift   = isset($arrData['list_topic_full'][$temp_topic_id])?$arrData['list_topic_full'][$temp_topic_id]:array_shift($arrData['list_topic_full']);

                //set topic style topic
                $topic_show_style = $arrShift['show_style'] != 2 ? 1 : $arrShift['show_style'];
                //array slice list article
                $arrTopic   = array_slice(array_keys($arrTopic[$temp_topic_id]['data']), 0, ($topic_show_style==1?LIMIT_TOPIC_DETAIL_STYLE_1:LIMIT_TOPIC_DETAIL_STYLE_2));
                $artInstance->setIdBasic($arrTopic);
            }
        }
        //end get topic of article detail
		
		//check neu empty data topic + link cheo moi xuat recommend
		if (empty($arrTopic) && empty($arrData['list_reference_sites_id']))
		{
			// Recommendation
			$arrRecommendParams = array(
				'article_id' => $id,
				'limit' => LIMIT_TOPIC_DETAIL_STYLE_1 + 1,
				'offset' => 0
			);
			$arrRecommend = $this->view->objArticle->getRecommendationById($arrRecommendParams);
			//valid data
			if (!empty($arrRecommend['data']))
			{
				$arrRecommend['data'] = array_diff($arrRecommend['data'], array($id));
				$arrRecommend['data'] = array_slice($arrRecommend['data'], 0, LIMIT_TOPIC_DETAIL_STYLE_1);
				// set to article instance
				$this->view->objArticle->setIdBasic($arrRecommend['data']);
			}
		}

        // GET OTHER NEWS
        // get news model
        $newsModel = Thethao_Model_News::getInstance();
        // array param for other news
        $arrOtherNewsParams = array(
            'category_id'   => $arrData['category_id'],
            'offset'        => 0,
            'limit'         => LIMIT_OTHER_NEWS + 1,
            'article_type'  => NULL
        );
        //  get other news
        $arrOtherNews = $newsModel->getListArticleIdsByRule2($arrOtherNewsParams);

        // check data empty
        $intKeyExists = false;
        if (is_array($arrOtherNews) && !empty($arrOtherNews['data']))
        {
            // find id in array other news data
            $intKeyExists = array_search($id, $arrOtherNews['data']);
            // if id exist in other news
            if ($intKeyExists !== false)
            {
                unset($arrOtherNews['data'][$intKeyExists]);
            }
            $arrOtherNews['data'] = array_slice($arrOtherNews['data'], 0, LIMIT_OTHER_NEWS);
        }

        // set id to article instance
        if (!empty($arrOtherNews['data']))
        {
            $artInstance->setIdBasic($arrOtherNews['data']);
        }

        //Trim data
		$arrData['title']			= trim($arrData['title']);
		$arrData['title_format']	= trim($arrData['title_format']);
		$arrData['lead']			= trim($arrData['lead']);

        ///////////////get audio ///////////////////////
        $urlSongMp3 = '';
        if ( !empty($arrData['list_object_type']) && !empty($arrData['list_object_type'][OBJECT_TYPE_SONG]) )
        {
            $fwSong     = $this->view->objObject->getSong();
            $songId     = $arrData['list_object_type'][OBJECT_TYPE_SONG][0]; //OBJECT_SONG=1
            $songDetail = $fwSong->setId($songId)->getDetailObjectBasic($songId);
            if ( !empty($songDetail) )
            {
                $urlSongMp3 = $songDetail['link_url'].'/'.$songDetail['location'];

            }//end if

        }//end if

        $strMetaImage = !empty($arrData['thumbnail_url']) ? $this->view->ImageurlArticle($arrData, 'size2') : 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg';

        // assign params to view
        $this->view->assign(array(
			'arrRecommend' => $arrRecommend,
            'arrArticleDetail'  => $arrData,
            'arrPhoto'          => $arrReference['data'],
            'arrOrtherArticle'  => $arrOtherNews['data'],
            'flagExc'           => $intKeyExists !== false ? 1 : 0,
            'totalPageOtherNews'=> $arrOtherNews['total'],
			'intPage'           => 1,
			'intCategoryId'     => CATE_ID_PHOTO,
            'intOtherCate'      => $arrData['category_id'],
            'intArticleId'      => $id,
            'isArticleHotVnE'   => $arrData['privacy'] & 512,
            'isArticlePhoto'    => true,
            'topic_show_style'  => $topic_show_style,
            'arrTopic'          => $arrTopic,
            'arrTopicDetail'    => $arrTopicDetail[$temp_topic_id],
            'ogType'            => 'website',
            'ogTitle'           => $arrData['title_format'] . ' - VnExpress Thể Thao',
            'ogUrl'             => $arrData['share_url'],
            'ogImage'           => !empty($thumbImage)?$thumbImage:$strMetaImage,
            'ogDescription'     => trim(strip_tags(html_entity_decode($arrData['lead'], ENT_COMPAT, 'UTF-8'))),
            'breakCumbOther'    => array(),
            'imageShare'        => $selected,
            'urlSongMp3'        => $urlSongMp3,
        ));

        if ( DEVICE_ENV != 1 )
        {
            $this->view->headScript()->appendFile($this->view->configView['vnecdn']['js'] . '/utils/slideshow/ImgFullscreen.js');
        }//end if

        // Prepend title
        $this->view->headTitle()->prepend(html_entity_decode($arrData['title'], ENT_COMPAT, 'UTF-8') . ' - VnExpress Thể thao');
        $this->view->headMeta()->setName('description', trim(strip_tags(html_entity_decode($arrData['lead'], ENT_COMPAT, 'UTF-8'))) . ' - VnExpress Thể Thao')
            ->setName('keywords', $arrData['title_format'] . ' - VnExpress Thể Thao');
    }

    /**
     * @author      : VinhNT
     * @name        : ajaxsendmailAction
     * @copyright   : FPT Online
     * @todo        : ajaxsendmail Action
     * @return      : array('error' => 0, 'message' => '', 'body' => '')
     */
    public function ajaxsendmailAction()
    {
        //Disable layout
        $this->_helper->layout->disableLayout(true);
        //Set no render view
        $this->_helper->viewRenderer->setNoRender(true);
        //default return
        $response = array('error' => 0, 'message' => '');
        /* -----------------------------------------------------------------
          Get param sent to job send mail
         */
        try
        {
            $arrParams = $this->_request->getPost();
            //Trim captcha id
            $arrCaptcha['id'] = trim($arrParams['captchaID']);
            $arrCaptcha['input'] = trim($arrParams['txtCode']);
            // Creating a Zend_Captcha_Image instance
            $captcha = new Zend_Captcha_Image();
            // would be key/value array: id => captcha ID, input => captcha value
            if ($captcha->isValid($arrCaptcha))
            {
                // Include config send mail
                include(APPLICATION_PATH . '/configs/mail.php');
                //Check config sender
                if (is_array($configsender) && !empty($configsender))
                {
                    // Get Url to send
                    $url_send = $arrParams['url'];
                    $url_send = addslashes(str_replace(array('=', '(', ')'), '', trim(strip_tags($url_send))));
                    // Get name of the sender
                    $sender_name = $arrParams['sender_name'];
                    $sender_name = addslashes(str_replace(array('=', '(', ')'), '', trim(strip_tags($sender_name))));
                    if (empty($sender_name))
                    {
                        $response['error'] = 1;
                        $response['message'] = 'Xin hãy nhập họ tên người gửi.';
                        echo Zend_Json::encode($response);
                        exit;
                    }
                    // Get user email
                    $email_user = $arrParams['email_user'];
                    $email_user = addslashes(str_replace(array('=', '(', ')'), '', trim(strip_tags($email_user))));
                    //Check email user
                    if (empty($email_user))
                    {
                        $response['error'] = 1;
                        $response['message'] = 'Xin hãy nhập email người gửi.';
                        echo Zend_Json::encode($response);
                        exit;
                    }
                    // Get title of send mail
                    $title_email = urldecode($arrParams['title_email']);
                    $title_email = addslashes(str_replace(array('=', '(', ')'), '', trim(strip_tags($title_email))));
                    //Set parrams email sender
                    $sender_email = $configsender['message']['email'];
                    // Get email of the reciever
                    $reciever_mail = $arrParams['receiver_mail'];
                    $reciever_mail = addslashes(str_replace(array('=', '(', ')'), '', trim(strip_tags($reciever_mail))));
                    //Check reciever mail
                    if (empty($reciever_mail))
                    {
                        $response['error'] = 1;
                        $response['message'] = 'Xin hãy nhập email người nhận.';
                        echo Zend_Json::encode($response);
                        exit;
                    }
                    // Get message mail
                    $message_mail = $arrParams['message_mail'];
                    $message_mail = trim(strip_tags($message_mail));

                    //Get body message
                    $body_message = '<strong>' . $sender_name . '(' . $email_user . ')</strong><br/>Đã gửi cho bạn bài báo tại link: <a href="'.$url_send.'" title="'.$title_email.'">' . $url_send. '</a>';
                    //Check message mail
                    if (!empty($message_mail))
                    {
                        $body_message .= '<br/>Với nội dung: </br>' . $message_mail;
                    }
                    $body_message .= '<hr><br/>Email này được gửi bằng tiện ích "Gửi cho bạn bè" của <b>Thethao.VnExpress.net<b>';
                    //Get array mail
                    $arrEmail = explode(';', $reciever_mail);
                    $arrEmailReciever = array();
                    //Check array email
                    if (!empty($arrEmail))
                    {
                        foreach ($arrEmail as $email)
                        {
                            $email = trim($email);
                            //If email is NOT valid
                            if (@!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+$", ((string) $email))) //if (@!Fpt_Valid::isEmail($email))
                                continue;
                            //Put data to arrReciever
                            $arrEmailReciever[] = array(
                                'email' => $email,
                                'alias' => $email
                            );
                        }
                        if (!empty($arrEmailReciever))
                        {
                            //push job call send mail
                            $jobParams = array(
                                'class' => 'Job_Thethao_JobMessage',
                                'function' => 'sendMail',
                                'args' => array(
                                        'sender' => $sender_email,
                                        'name' => $configsender['message']['name'],
                                        'reciever' => $arrEmailReciever,
                                        'subject' => $title_email,
                                        'body' => $body_message
                                )
                            );
                            //get application config
                            $config = Thethao_Global::getApplicationIni();
                            //To array
                            $jobConfiguration = $config['job']['task']['sport']['send_mail'];
                            //get job send mail instance
                            $jobClient = Thethao_Global::getJobClientInstance('sport');
                            //Register job
                            $result = $jobClient->doBackgroundTask($jobConfiguration, $jobParams);
                            if (!$result)
                            {
                                $response['error'] = 1;
                                $response['message'] = 'Không gửi được email. Xin vui lòng thử lại sau.';
                                echo Zend_Json::encode($response);
                                exit;
                            }
                            else
                            {
                                $response['error'] = 0;
                                $response['message'] = 'Gửi email thành công.';
                                echo Zend_Json::encode($response);
                                exit;
                            }
                        }
                        else
                        {
                            $response['error'] = 1;
                            $response['message'] = 'Email gửi đến không hợp lệ';
                            echo Zend_Json::encode($response);
                            exit;
                        }
                    }
                }
                else
                {
                    $response['error'] = 1;
                    $response['message'] = 'Empty recipient.';
                    echo Zend_Json::encode($response);
                    exit;
                }
            }
            else
            {
                $response['error'] = 2;
                $response['message'] = 'Mã xác nhận không đúng.';
                echo Zend_Json::encode($response);
                exit;
            }
        }
        catch (Exception $ex)
        {
            // Log error
            Thethao_Global::sendLog($ex);
        }
        echo Zend_Json::encode($response);
        exit;
    }

    /**
     * Page Print Detail
     */
    public function printAction()
    {
        //Disable layout
        $this->_helper->layout->disableLayout(true);
        //get id
        $id = $this->_request->getParam('id', 0);
        //Get page
        $intPage = max(1, $this->_request->getParam('page', 1));
        //Get article type
        $intType = $this->_request->getParam('t', TYPE_ARTICLE);

        if (!is_numeric($id) || $id <= 0)
        {
            $this->_redirect('/');
        }

        $articleInstance = $this->view->objArticle;
        //Get full detail article
        if($intPage > 1)
        {
            //get data page extend
            $arrData = $articleInstance->getDetailPageExtend($id, $intPage);
        }
        else
        {
            $arrData = $articleInstance->getArticleFull($id);
            $intType = $arrData['article_type'];
        }

        // check data empty
        if (empty($arrData))
        {
            $this->_redirect($this->view->configView['linkerror'][DEVICE_ENV], array('code'=>301));
        }

        //Check article photo
        $arrReference = array();
        if ($intType == TYPE_PHOTO)
        {
            //Set params
            $arrParams = array(
                'article_id' => $arrData['article_id'],
                'limit' => LIMIT_SLIDESHOW_PHOTO,
                'offset' => 0
            );
            // Get list object reference
            $arrReference = $articleInstance->getObjectReferenceByArticleId($arrParams);
        }

        //assign to view
        $this->view->assign(array(
            'arrData' => $arrData,
            'arrPhoto' => $arrReference['data'],
            'type' => $intType
        ));
    }
    /**
     * infographic
     */
    public function infographicAction()
    {
        $strLink = $this->_request->getPathInfo();
        $strLink = preg_replace('/\/$/', '', $strLink);
        $this->_redirect($this->view->configView['url_vne'] . $strLink, array('code' => 301));
    }

    /**
     * Action article Live
     */
    public function liveAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);

        //get article detail to check link then redirect of false
        $intArticleId = (int)$this->_request->getParam('id');
        $viewApp = $this->_request->getParam('view', '');
        //get detail article
        $arrArticleDetail = $this->view->objArticle->getArticleFull($intArticleId);
        //str link
        $strLink = $this->_request->getPathInfo();

        if (strpos($arrArticleDetail['share_url'], rtrim($strLink, '/')) === FALSE)
        {
            $this->_redirect($arrArticleDetail['share_url'], array('code' => 301));
        }

        //get all block of the article
        //init model live data
        $objLive = Fpt_Data_News_Live::getInstance();
        //init param to get block
        $arrParamBlock = array(
            'article_id' => $intArticleId,
            'order' => ($arrArticleDetail['privacy'] & 16384 ? 0 : 1),
            'from_time' => NULL,
        );
        $arrBlockDataId = $objLive->getListBlockByArticleId($arrParamBlock);

        //get link video streaming
        $strLinkVideo = $objLive->getLinkVideoStreaming($intArticleId);

        //get data extra of article
		$articleExtra = $this->view->objArticle->getArticleExtra($intArticleId);
        $arrArticleExtra = Zend_Json::decode($articleExtra['article_type']);

        //set block id if not null
        if (!empty ($arrBlockDataId['data']))
        {
            $objLive->setBlockId($arrBlockDataId['data']);
        }
        //end get all block of the article

        //Set category id for block_cate user in block & menu breakumb
        $this->_request->setParam('block_cate', $arrArticleDetail['category_id']);

        //set cate active
        $this->_request->setParam('cateActiveId', $arrArticleDetail['category_id']);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', $arrArticleDetail['category_id']);

        //get Instance Block
        $objBlock = Thethao_Plugin_Block::getInstance();
        //set exclude for detail
        $objBlock->setExclude(array($intArticleId));

        //replace image size for mobile
        if (DEVICE_ENV == 1)
        {
            $content = $arrArticleDetail['content'];
            $content = preg_replace('/src="(.[^>]*)_500x0.(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
            $content = preg_replace('/src="(.[^>]*).(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
            $content = preg_replace('/src="(.[^>]*)_m_460x0_m_460x0.(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
            $arrArticleDetail['content'] = $content;
        }
        //Trim data
		$arrArticleDetail['title']			= trim($arrArticleDetail['title']);
		$arrArticleDetail['title_format']	= trim($arrArticleDetail['title_format']);
		$arrArticleDetail['lead']			= trim($arrArticleDetail['lead']);

        // Assign to view
        $this->view->assign(array(
            'arrBlockDataId'    => $arrBlockDataId['data'],
            'objLive'           => $objLive,
            'isArticleHotVnE'   => $arrArticleDetail['privacy'] & 512,
            'arrArticleDetail'  => $arrArticleDetail,
            'intCategoryId'        => $arrArticleDetail['category_id'],
            'intArticleId'         => $intArticleId,
            'timeUpdate'        => $arrBlockDataId['time'],
            'strLinkVideo'      => $strLinkVideo,
            'articleExtra'      => $arrArticleExtra,
            'paramsBlock' => Zend_Json::encode(array(
                'pagecode' => 'default_detail_live_' . $arrArticleDetail['category_id'],
                'exclude' => $intArticleId
            )),
            'ogType' => 'website',
            'ogTitle' => $arrArticleDetail['title_format'] . ' - VnExpress Thể Thao',
            'ogUrl' => $arrArticleDetail['share_url'],
            'ogImage' => !empty($arrArticleDetail['thumbnail_url']) ? $this->view->ImageurlArticle($arrArticleDetail, 'size2', -1) : 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription' => trim(strip_tags(html_entity_decode($arrArticleDetail['lead'], ENT_COMPAT, 'UTF-8')))
        ));

        (DEVICE_ENV == 1) && $this->view->headLink()->appendStylesheet($this->view->configView['vnecdn']['css'] . '/live_detail.css');

        // SEO
        $arrKeyword = array();
        $arrArticleDetail['title'] = html_entity_decode($arrArticleDetail['title'], ENT_QUOTES, 'UTF-8');
        $this->view->headTitle($arrArticleDetail['title'] . ' - VnExpress Thể Thao');
        $this->view->headMeta()->setName('description', trim(strip_tags(html_entity_decode($arrArticleDetail['lead'], ENT_COMPAT, 'UTF-8'))))
            ->setName('keywords', join(',', $arrKeyword));

        //check view app
        if (!empty($viewApp) && $viewApp == 'app')
        {
            //Disable layout
            $this->_helper->layout->disableLayout(true);
            echo $this->view->render('detail/applive.phtml');
            exit;
        }
    }

    /**
     * tuong thuat football match
     * @return void
     */
    public function tuongThuatAction()
	{
		//set cache
        $this->_request->setParam('cache_file', 1);

        //get article detail to check link then redirect of false
        $intArticleId = (int)$this->_request->getParam('id');

        $viewApp = $this->_request->getParam('view', '');

        //get detail article
        $arrArticleDetail = $this->view->objArticle->getArticleFull($intArticleId);

        //check link info
        $strLink = $this->_request->getPathInfo();
        if (strpos($arrArticleDetail['share_url'], rtrim($strLink, '/')) === FALSE)
        {
            $strLink = preg_replace('#/tin-tuc/tuong-thuat/#', '/tuong-thuat/', $arrArticleDetail['share_url']);
            $strLink = $strLink . (in_array($viewApp, array('app', 'app_sport_live')) ? '?view=' . $viewApp : '');
            $this->_redirect($strLink, array('code' => 301));
        }

        //get all block of the article
        //init model live data
        $objLive = Fpt_Data_News_Live::getInstance();
        //init param to get block
        $arrParamBlock = array(
            'article_id' => $intArticleId,
            'order' => ($arrArticleDetail['privacy'] & 16384 ? 0 : 1),
            'from_time' => NULL,
        );
        $arrBlockDataId = $objLive->getListBlockByArticleId($arrParamBlock);
        //set block id if not null
        if (!empty ($arrBlockDataId['data']))
        {
            $objLive->setBlockId($arrBlockDataId['data']);
        }
        //end get all block of the article

        //get data extra of article
		$articleExtra = $this->view->objArticle->getArticleExtra($intArticleId);
        $arrArticleExtra = Zend_Json::decode($articleExtra['article_type']);

        //get topic of article detail
        //set topic show style
        $topic_show_style   = 1;
        //init topic default
        $arrTopic = array();
        $arrTopicDetail = array();
        if (!empty($arrArticleDetail['list_topic_id']))
        {
            //init model topic
            $modelTopic = new Fpt_Data_News_Topic;

            // init topic params
            $arrTopicParams = array(
                'site_id'   => 1000000,
                'topic_id'  => $arrArticleDetail['list_topic_id'],
                'limit'     => LIMIT_TOPIC_DETAIL + 1,
                'offset'    => 0,
                'withscore' => true,
                'article_type' => NULL
            );
            // get list article by topic
            $arrTopic = $modelTopic->getTopicNewsByScore($arrTopicParams);

            // check array topic data
            if (!empty($arrTopic))
            {
                $score = 0;
                $temp_topic_id = 0;

                foreach ($arrArticleDetail['list_topic_id'] as $index => $topic_id)
                {
                    if (isset($arrTopic[$topic_id]['data'][$intArticleId]))
                    {
                        unset($arrTopic[$topic_id]['data'][$intArticleId]);
                    }
                    if (empty($arrTopic[$topic_id]['data']))
                    {
                        unset($arrArticleDetail['list_topic_id'][$index]);
                        continue;
                    }

                    // get first article to compare
                    $firstCore = current($arrTopic[$topic_id]['data']);

                    if ($firstCore > $score)
                    {
                        $temp_topic_id = $topic_id;
                        $score = $firstCore;
                    }
                }
                // get detail of topic id list
                $arrTopicDetail = $modelTopic->getDetailTopicByArrId(array($temp_topic_id));

                //shift data
                $arrShift   = isset($arrArticleDetail['list_topic_full'][$temp_topic_id])?$arrArticleDetail['list_topic_full'][$temp_topic_id]:array_shift($arrArticleDetail['list_topic_full']);

                //set topic style topic
                $topic_show_style = $arrShift['show_style'] != 2 ? 1 : $arrShift['show_style'];
                //array slice list article
                $arrTopic   = array_slice(array_keys($arrTopic[$temp_topic_id]['data']), 0, ($topic_show_style==1?LIMIT_TOPIC_DETAIL_STYLE_1:LIMIT_TOPIC_DETAIL_STYLE_2));
                //set id basic
                $this->view->objArticle->setIdBasic($arrTopic);
            }
        }
        //end get topic of article detail

        //check and get tong thuat if have
        $isReview = false;
        $arrDataExtend = $this->view->objArticle->getDetailPageExtend($intArticleId, 2);
        if (!empty($arrDataExtend) && (!empty($arrDataExtend['content'])))
        {
            unset($arrDataExtend);
            $strLink = preg_replace('#/tin-tuc/tuong-thuat/#', '/tong-thuat/', $arrArticleDetail['share_url']);
            //merge page extend to data detail
            $isReview = true;
        }

        //Set category id for block_cate user in block & menu breakumb
        $this->_request->setParam('block_cate', $arrArticleDetail['category_id']);

        //set cate active
        $this->_request->setParam('cateActiveId', $arrArticleDetail['category_id']);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', $arrArticleDetail['category_id']);

        //get Instance Block
        $objBlock = Thethao_Plugin_Block::getInstance();
        //set exclude for detail
        $objBlock->setExclude(array($intArticleId));

        //replace image size for mobile
        if (DEVICE_ENV == 1)
        {
            $content = $arrArticleDetail['content'];
            $content = preg_replace('/src="(.[^>]*)_500x0.(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
            $content = preg_replace('/src="(.[^>]*).(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
            $content = preg_replace('/src="(.[^>]*)_m_460x0_m_460x0.(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
            $arrArticleDetail['content'] = $content;
        }
        //Trim data
		$arrArticleDetail['title']			= trim($arrArticleDetail['title']);
		$arrArticleDetail['title_format']	= trim($arrArticleDetail['title_format']);
		$arrArticleDetail['lead']			= trim($arrArticleDetail['lead']);

        // Assign to view
        $this->view->assign(array(
            'arrBlockDataId'    => $arrBlockDataId['data'],
            'objLive'           => $objLive,
            'isArticleHotVnE'   => $arrArticleDetail['privacy'] & 512,
            'arrArticleDetail'  => $arrArticleDetail,
            'intCategoryId'     => $arrArticleDetail['category_id'],
            'intArticleId'      => $intArticleId,
            'timeUpdate'        => $arrBlockDataId['time'],
            'isReview'          => $isReview,
            'arrTopic'      => $arrTopic,
            'arrTopicDetail'    => $arrTopicDetail[$temp_topic_id],
            'topic_show_style'  => $topic_show_style,
            'strLinkTongThuat'  => $strLink . (in_array($viewApp, array('app', 'app_sport_live')) ? '?view=' . $viewApp : ''),
            'articleExtra'      => $arrArticleExtra,
            'ogType' => 'website',
            'ogTitle' => $arrArticleDetail['title_format'] . ' - VnExpress Thể Thao',
            'ogUrl' => preg_replace('#/tin-tuc/tuong-thuat/#', '/tuong-thuat/', $arrArticleDetail['share_url']),
            'ogImage' => !empty($arrArticleDetail['thumbnail_url']) ? $this->view->ImageurlArticle($arrArticleDetail, 'size2') : 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription' => trim(strip_tags(html_entity_decode($arrArticleDetail['lead'], ENT_COMPAT, 'UTF-8')))
        ));

        (DEVICE_ENV == 1) && $this->view->headLink()->appendStylesheet($this->view->configView['vnecdn']['css'] . '/live_detail.css');
        // SEO
        $arrKeyword = array();
        $arrArticleDetail['title'] = html_entity_decode($arrArticleDetail['title'], ENT_QUOTES, 'UTF-8');
        $this->view->headTitle($arrArticleDetail['title'] . ' - VnExpress Thể Thao');
        $this->view->headMeta()->setName('description', trim(strip_tags(html_entity_decode($arrArticleDetail['lead'], ENT_COMPAT, 'UTF-8'))))
            ->setName('keywords', join(',', $arrKeyword));

        //check view app
        if (!empty($viewApp) && in_array($viewApp, array('app', 'app_sport_live')))
        {
            $this->view->assign(array(
                'viewApp' => $viewApp
            ));

            //Disable layout
            $this->_helper->layout->disableLayout(true);
            echo $this->view->render('detail/app-tuong-thuat.phtml');
            exit;
        }
	}

    /**
     * tong thuat football match
     * @return void
     */
	public function tongThuatAction()
	{
		//set cache
        $this->_request->setParam('cache_file', 1);

        //get article detail to check link then redirect of false
        $intArticleId = (int)$this->_request->getParam('id');
        $viewApp = $this->_request->getParam('view', '');
        //get detail article
        $arrArticleDetail = $this->view->objArticle->getArticleFull($intArticleId);

        //get topic of article detail
        //set topic show style
        $topic_show_style   = 1;
        //init topic default
        $arrTopic = array();
        $arrTopicDetail = array();
        if (!empty($arrArticleDetail['list_topic_id']))
        {
            //init model topic
            $modelTopic = new Fpt_Data_News_Topic;

            // init topic params
            $arrTopicParams = array(
                'site_id'   => 1000000,
                'topic_id'  => $arrArticleDetail['list_topic_id'],
                'limit'     => LIMIT_TOPIC_DETAIL + 1,
                'offset'    => 0,
                'withscore' => true,
                'article_type' => NULL
            );
            // get list article by topic
            $arrTopic = $modelTopic->getTopicNewsByScore($arrTopicParams);

            // check array topic data
            if (!empty($arrTopic))
            {
                $score = 0;
                $temp_topic_id = 0;

                foreach ($arrArticleDetail['list_topic_id'] as $index => $topic_id)
                {
                    if (isset($arrTopic[$topic_id]['data'][$intArticleId]))
                    {
                        unset($arrTopic[$topic_id]['data'][$intArticleId]);
                    }
                    if (empty($arrTopic[$topic_id]['data']))
                    {
                        unset($arrArticleDetail['list_topic_id'][$index]);
                        continue;
                    }

                    // get first article to compare
                    $firstCore = current($arrTopic[$topic_id]['data']);

                    if ($firstCore > $score)
                    {
                        $temp_topic_id = $topic_id;
                        $score = $firstCore;
                    }
                }
                // get detail of topic id list
                $arrTopicDetail = $modelTopic->getDetailTopicByArrId(array($temp_topic_id));

                //shift data
                $arrShift   = isset($arrArticleDetail['list_topic_full'][$temp_topic_id])?$arrArticleDetail['list_topic_full'][$temp_topic_id]:array_shift($arrArticleDetail['list_topic_full']);

                //set topic style topic
                $topic_show_style = $arrShift['show_style'] != 2 ? 1 : $arrShift['show_style'];
                //array slice list article
                $arrTopic   = array_slice(array_keys($arrTopic[$temp_topic_id]['data']), 0, ($topic_show_style==1?LIMIT_TOPIC_DETAIL_STYLE_1:LIMIT_TOPIC_DETAIL_STYLE_2));
                //set id basic
                $this->view->objArticle->setIdBasic($arrTopic);
            }
        }
        //end get topic of article detail

        //check link info
        $strLink = $this->_request->getParam(2) . '-' . $intArticleId . '.html';
        if (strpos($arrArticleDetail['share_url'], rtrim($strLink, '/')) === FALSE)
        {
            $strLink = preg_replace('#/tin-tuc/tuong-thuat/#', '/tong-thuat/', $arrArticleDetail['share_url']);
            $strLink = $strLink . (in_array($viewApp, array('app', 'app_sport_live')) ? '?view=' . $viewApp : '');
            $this->_redirect($strLink, array('code' => 301));
        }

        //get all block of the article
        //init model live data
        $objLive = Fpt_Data_News_Live::getInstance();
        //init param to get block
        $arrParamBlock = array(
            'article_id' => $intArticleId,
            'order' => 0,
            'from_time' => NULL,
        );
        $arrBlockDataId = $objLive->getListBlockByArticleId($arrParamBlock);
        //set block id if not null
        if (!empty ($arrBlockDataId['data']))
        {
            $objLive->setBlockId($arrBlockDataId['data']);
        }
        //end get all block of the article

        //check and get tong thuat if have
        $isReview = false;
        $arrDataExtend = $this->view->objArticle->getDetailPageExtend($intArticleId, 2);
        if (!empty($arrDataExtend) && (!empty($arrDataExtend['content'])))
        {
            unset($arrDataExtend['creation_time'],$arrDataExtend['publish_time_format'],$arrDataExtend['publish_time']);
            //merge page extend to data detail
            $arrArticleDetail['content_review'] = $arrDataExtend['content'];
            $isReview = true;
        }

        //Set category id for block_cate user in block & menu breakumb
        $this->_request->setParam('block_cate', $arrArticleDetail['category_id']);

        //set cate active
        $this->_request->setParam('cateActiveId', $arrArticleDetail['category_id']);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', $arrArticleDetail['category_id']);

        //get Instance Block
        $objBlock = Thethao_Plugin_Block::getInstance();
        //set exclude for detail
        $objBlock->setExclude(array($intArticleId));

        //replace image size for mobile
        if (DEVICE_ENV == 1)
        {
            $content = $arrArticleDetail['content_review'];
            $content = preg_replace('/src="(.[^>]*)_500x0.(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
            $content = preg_replace('/src="(.[^>]*).(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
            $content = preg_replace('/src="(.[^>]*)_m_460x0_m_460x0.(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
            $arrArticleDetail['content_review'] = $content;
        }
        //Trim data
		$arrArticleDetail['title']			= trim($arrArticleDetail['title']);
		$arrArticleDetail['title_format']	= trim($arrArticleDetail['title_format']);
		$arrArticleDetail['lead']			= trim($arrArticleDetail['lead']);

        // Assign to view
        $this->view->assign(array(
            'arrArticleDetail'  => $arrArticleDetail,
            'intCategoryId'        => $arrArticleDetail['category_id'],
            'intArticleId'         => $intArticleId,
            'isArticleHotVnE'   => $arrArticleDetail['privacy'] & 512,
            'timeUpdate'        => $arrBlockDataId['time'],
            'arrTopic'      => $arrTopic,
            'arrTopicDetail'    => $arrTopicDetail[$temp_topic_id],
            'topic_show_style'  => $topic_show_style,
            'strLinkTuongThuat'  => '/tuong-thuat/' . $strLink . (in_array($viewApp, array('app', 'app_sport_live')) ? '?view=' . $viewApp : ''),
            'ogType' => 'website',
            'ogTitle' => $arrArticleDetail['title_format'] . ' - VnExpress Thể Thao',
            'ogUrl' => preg_replace('#/tin-tuc/tuong-thuat/#', '/tong-thuat/', $arrArticleDetail['share_url']),
            'ogImage' => !empty($arrArticleDetail['thumbnail_url']) ? $this->view->ImageurlArticle($arrArticleDetail, 'size2') : 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription' => trim(strip_tags(html_entity_decode($arrArticleDetail['lead'], ENT_COMPAT, 'UTF-8')))
        ));

        // SEO
        $arrKeyword = array();
        $arrArticleDetail['title'] = html_entity_decode($arrArticleDetail['title'], ENT_QUOTES, 'UTF-8');
        $this->view->headTitle($arrArticleDetail['title'] . ' - VnExpress Thể Thao');
        $this->view->headMeta()->setName('description', trim(strip_tags(html_entity_decode($arrArticleDetail['lead'], ENT_COMPAT, 'UTF-8'))))
            ->setName('keywords', join(',', $arrKeyword));

        //check view app
        if (!empty($viewApp) && in_array($viewApp, array('app', 'app_sport_live')))
        {
            $this->view->assign(array(
                'viewApp' => $viewApp
            ));

            //Disable layout
            $this->_helper->layout->disableLayout(true);
            echo $this->view->render('detail/app-tong-thuat.phtml');
            exit;
        }
	}
}
