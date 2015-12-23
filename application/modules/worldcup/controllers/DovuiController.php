<?php

/**
 * @author      :   CUONGNM 17
 * @name        :   dovuiController
 * @copyright   :   Fpt Online
 * @todo        :   dovui Controller
 */
class DovuiController extends Zend_Controller_Action
{
    public function indexAction()
    {
        //set cache
        $this->view->headScript()->appendFile($this->view->configView['vnecdn']['js'] . '/interactions/quiz-game.js');
        $this->_request->setParam('cache_file', 1);
        $strCateCode = $this->_request->getParam('controller');      
        
        //get Unix Current DateTime
        $currentDate = new DateTime();
        $currentDate = $currentDate->format('Y-m-d');
        $currentDate = strtotime($currentDate);  

        $objNews = Thethao_Model_News::getInstance();

        $date = new DateTime();
        
        
        $dateNow = strtotime($date->format('Y-m-d H:i:s'));
        $date8Am = strtotime($date->format('Y-m-d 08:00:00'));
        
        $minDate = date('Y-m-d', DOVUI_SDATE);
        $minDate =  date('Y-m-d 08:00:00', strtotime($minDate. ' + 1 days'));
        $minDate = strtotime($minDate);
        
        $maxDate = date('Y-m-d', DOVUI_EDATE);
        $maxDate =  date('Y-m-d 08:00:00', strtotime($maxDate. ' + 1 days'));
        $maxDate = strtotime($maxDate);
                
        //var_dump($date->format('Y-m-d H:i:s'),'---',$minDate);die();
        //case 1: Nếu ngày hiện tại lớn hơn SDate + 1 --> Ẩn View danh sách trúng thưởng
        if($dateNow < $minDate)
        {
            
            
        }
        //case 2: Nếu Sdate + 1 < currentDate < EDate + 1 --> Get DS trúng thưởng hàng ngày
        else if($minDate <= $dateNow && $dateNow <= $maxDate)
        {
            
            //Nếu thời gian hiện tại lớn hơn 8h sáng thì lấy danh sách trúng thưởng từ 00h đến 23h59 ngày hôm trước (datenow - 1 ngày) 
            if($dateNow >= $date8Am)
            {
                
                $date->sub(new DateInterval('P1D')); 
                $fromDate = strtotime($date->format('Y-m-d 00:00:00'));
                $toDate = strtotime($date->format('Y-m-d 23:59:59'));  
                
                //set thời gian truyền vào: fromDate = 00h00p00s & toDate = 23h59p59s của ngày hiện tại - 1
            }
            //Nếu thời gian hiện tại nhỏ hơn 8h sáng thì lấy danh sách trúng thưởng từ 00h đến 23h59 ngày hôm trước nữa (datenow - 2 ngày)
            else
            {
                $date->sub(new DateInterval('P2D')); 
                $fromDate = strtotime($date->format('Y-m-d 00:00:00'));
                $toDate = strtotime($date->format('Y-m-d 23:59:59'));   
                //set thời gian truyền vào: fromDate = 00h00p00s & toDate = 23h59p59s của ngày hiện tại - 2
            }
        }
         //case 3: Nếu currentDate >= EDate + 1 --> Get Ds chung cuộc
        else
        {
            //die('456');
            //Nếu thời gian hiện tại lớn hơn 8h sáng thì lấy danh sách trúng thưởng chung cuộc
            if($dateNow >= $date8Am)
            {
                //$date->sub(new DateInterval('P0D')); 
//                $fromDate = 0;
//                $toDate = 0;
                  $date = date('Y-m-d H:i:s', DOVUI_EDATE);
                  $date = new DateTime($date);
                  $date->format('Y-m-d H:i:s');
                
                  $fromDate = strtotime(date('Y-m-d 00:00:00', DOVUI_EDATE));
                  $toDate = strtotime(date('Y-m-d 23:59:59', DOVUI_EDATE));  
                //set thời gian truyền vào : fromDate = 0 & toDate = 0 (ket qua chung cuộc)
            }
            //Nếu thời gian hiện tại nhỏ hơn 8h sáng thì lấy danh sách trúng thưởng của ngày kết thúc
            else
            {
                $date = date('Y-m-d H:i:s', DOVUI_EDATE);
                $date = new DateTime($date);
                $date->format('Y-m-d H:i:s');
                
                $fromDate = strtotime(date('Y-m-d 00:00:00', DOVUI_EDATE));
                $toDate = strtotime(date('Y-m-d 23:59:59', DOVUI_EDATE));   
                //set thời gian truyền vào : fromDate = 00h00p00s & toDate = 23h59p59s của EDATE_DOVUI
            }
        }
        
            $arrAward = $objNews->getListEndAward(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DOVUI,
            'fromdate'  => $fromDate,
            'todate'  => $toDate,                                    
            ));
            //Nếu danh sách trả về rỗng
            if(empty($arrAward))
            {    
                for($i = 0; $i < 5; $i++)
                {
                    if(empty($arrAward))
                    {
                        $date->sub(new DateInterval('P1D'));
                        $fromDate = strtotime($date->format('Y-m-d 00:00:00')); 
                        $toDate = strtotime($date->format('Y-m-d 23:59:59'));
                       
                        $arrAward = $objNews->getListEndAward(array(            
                            'isGearman' => false,
                            'object_type'  => OBJECT_TYPE_DOVUI,
                            'award'  => AWARD_DOVUI,
                            'fromdate'  => $fromDate,
                            'todate'  => $toDate,                                    
                        ));
                    }
                    else
                    {
                        break;
                    }
                }
            }
                 
        $dateAward = $date; 
        
        
        
        //---question
        $timestamp = $this->_request->getParam('day', '');
        //get Date Param URL & Check Int Param
        //begin        
        //nếu param truyền vào không hợp lệ set time = giờ hiện tại
        $timestampParam = $this->_request->getParam('day', '');
             
        if (!is_numeric($timestampParam) || $timestampParam < 1 || $timestampParam != round($timestampParam)) 
        {
            //Nếu thoi gian hien tại nhỏ hơn hoặc bằng EDATE
            if($currentDate <= DOVUI_EDATE)
                $timestampParam = $currentDate;
            else
            {
                //Nếu thoi gian hien tại nhỏ hơn 8h sáng
                if($date8Am > $currentDate)
                    $timestampParam = $currentDate;
                else
                    $timestampParam = DOVUI_EDATE;
            }
        }
        else
        {
            //Nếu ?day không nằm trong khoảng thời gian thi set time = hiện tại
            if($timestampParam < DOVUI_SDATE || $timestampParam > DOVUI_EDATE)
            {
                if($timestampParam > DOVUI_EDATE)
                {
                    //Nếu thoi gian hien tại nhỏ hơn 8h sáng
                    if($date8Am > $currentDate)
                        $timestampParam = $currentDate;
                    else
                        $timestampParam = DOVUI_EDATE;
                }
            }
            else
            {
                //Nếu ?day lớn hơn thời gian hiện tại set time = hiện tại
                if($timestampParam > $currentDate)
                    $timestampParam = $currentDate;
            }
        }  
        //end        
        
         
        $check = (is_int($timestamp) OR is_float($timestamp))?$timestamp:(string)(int)$timestamp;
         
        $isTimestamp=($check === $timestamp) AND ( (int) $timestamp <= PHP_INT_MAX) AND ( (int) $timestamp >= ~PHP_INT_MAX);
        
        if($isTimestamp != 1)
        {
			
            //Nếu thoi gian hien tại nhỏ hơn hoặc bằng EDATE
            if($currentDate <= DOVUI_EDATE)
            {
				
                $date=new DateTime(); //this returns the current date time
                $fromDate = strtotime($date->format('Y-m-d 08:00:00'));
                $date->add(new DateInterval('P1D'));
                $toDate = strtotime($date->format('Y-m-d 07:59:00')); 
            }
            else
            {
				
                //Nếu thoi gian hien tại nhỏ hơn 8h sáng
                if($date8Am > $currentDate)
                {
					//die('221121111');
                    $date=new DateTime('2014-07-21'); //this returns the current date time
                    $fromDate = strtotime($date->format('Y-m-d 08:00:00'));
                    $date->add(new DateInterval('P1D'));
                    $toDate = strtotime($date->format('Y-m-d 07:59:00'));  
                }
                else
                {
                    $date=new DateTime('2014-07-21'); //this returns the current date time
                    $fromDate = strtotime($date->format('Y-m-d 08:00:00'));
                    $date->add(new DateInterval('P1D'));
                    $toDate = strtotime($date->format('Y-m-d 07:59:00')); 
                }
                    
            }
            
            
        }
        else
        {
            if($timestamp > $currentDate)
                $timestamp = $currentDate;
                
            if($timestamp > DOVUI_EDATE||$timestamp < DOVUI_SDATE )
            {
                if($currentDate >= DOVUI_EDATE)
                    $timestamp = DOVUI_EDATE;
                else
                    $timestamp = $currentDate;
            }    
            
            $date = date('Y-m-d', $timestamp);
            $date = new DateTime($date);
            $fromDate = strtotime($date->format('Y-m-d 08:00:00'));
            $date->add(new DateInterval('P1D'));
            $toDate = strtotime($date->format('Y-m-d 07:59:00')); 
        }
        
        $arrListQuestion = $objNews->getListQuestion(array(            
                'isGearman' => false,                
                'fromdate'  => $fromDate,
                'todate'  => $toDate,                                    
            )); 
        //var_dump($fromDate,$toDate); die();
        
        //get list award
        
        $arrAward1 = $objNews->getListAwardByWeek(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DAY_DOVUI,
            'fromdate'  => 0,
            'todate'  => 0,                                    
            ),self::getweek(1,1,0,0,0,0,0,0));
        $arrAward2 = $objNews->getListAwardByWeek(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DAY_DOVUI,
            'fromdate'  => 0,
            'todate'  => 0,                                    
            ),self::getweek(2,1,0,0,0,0,0,0));
        $arrAward3 = $objNews->getListAwardByWeek(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DAY_DOVUI,
            'fromdate'  => 0,
            'todate'  => 0,                                    
            ),self::getweek(3,1,0,0,0,0,0,0));
        $arrAward4 = $objNews->getListAwardByWeek(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DAY_DOVUI,
            'fromdate'  => 0,
            'todate'  => 0,                                    
            ),self::getweek(4,1,0,0,0,0,0,0));
        $arrAward5 = $objNews->getListAwardByWeek(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DAY_DOVUI,
            'fromdate'  => 0,
            'todate'  => 0,                                    
            ),self::getweek(5,1,0,0,0,0,0,0));
        $arrAward6 = $objNews->getListAwardByWeek(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DAY_DOVUI,
            'fromdate'  => 0,
            'todate'  => 0,                                    
            ),self::getweek(6,1,0,0,0,0,0,0));
        $arrAwardBlock = $objNews->getListAwardByWeek(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DAY_DOVUI,
            'fromdate'  => 0,
            'todate'  => 0,                                    
            ),self::getweek(1,0,0,0,0,0,0,0));
        
        
        $week = self::getweek(1,0,$arrAward1,$arrAward2,$arrAward3,$arrAward4,$arrAward5,$arrAward6);
        
        
        $this->view->assign(array(            
            'strCateCode' => $strCateCode,
            'arrAward'  => $arrAward,
            'dateAward' =>$dateAward,
            'arrData' => $arrListQuestion,
            'dateParam' => $timestampParam,
            'timestamp' =>$timestamp,
            'currentDate' => $currentDate,
            'arrData1'  => $arrAward1,
            'arrData2'  => $arrAward2,
            'arrData3'  => $arrAward3,
            'arrData4'  => $arrAward4,
            'arrData5'  => $arrAward5,
            'arrData6'  => $arrAward6,
            'arrAwardBlock' =>$arrAwardBlock,
            'week'  => $week
        ));
         //var_dump($dateAward); 
        //gan meta data
        
       
        $titleMeta = 'Ðố vui World Cup 2014 - VnExpress';
        $keywords = 'World cup 2014, tin tuc , bong da';
        $description = '';
        //Set param
        $this->_request->setParam('block_cate', WORLD_CUP);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', WORLD_CUP);
        //Add script
        $this->view->headTitle($titleMeta);
        $this->view->headMeta()->setName('description', $description);
        $this->view->headMeta()->setName('keywords', $keywords);
        
        
    }    
    public function trungthuongAction()
    {
        //set cache
        $objNews = Thethao_Model_News::getInstance();
        $this->view->headScript()->appendFile($this->view->configView['vnecdn']['js'] . '/interactions/quiz-game.js');
        $this->_request->setParam('cache_file', 1);
        $strCateCode = $this->_request->getParam('controller');     
        
        
        $currentDate = new DateTime();
        $currentDate = $currentDate->format('Y-m-d');
        $currentDate = strtotime($currentDate);  

        $objNews = Thethao_Model_News::getInstance();

        $date = new DateTime();
        
        
        $dateNow = strtotime($date->format('Y-m-d H:i:s'));
        $date8Am = strtotime($date->format('Y-m-d 08:00:00'));
        
        $minDate = date('Y-m-d', DOVUI_SDATE);
        $minDate =  date('Y-m-d 08:00:00', strtotime($minDate. ' + 1 days'));
        $minDate = strtotime($minDate);
        
        $maxDate = date('Y-m-d', DOVUI_EDATE);
        $maxDate =  date('Y-m-d 08:00:00', strtotime($maxDate. ' + 1 days'));
        $maxDate = strtotime($maxDate);
        
        
                
        //var_dump($date->format('Y-m-d H:i:s'),'---',$minDate);die();
        //case 1: Nếu ngày hiện tại lớn hơn SDate + 1 --> Ẩn View danh sách trúng thưởng
        if($dateNow < $minDate)
        {
            
            
        }
        //case 2: Nếu Sdate + 1 < currentDate < EDate + 1 --> Get DS trúng thưởng hàng ngày
        else if($minDate <= $dateNow && $dateNow <= $maxDate)
        {
            
            //Nếu thời gian hiện tại lớn hơn 8h sáng thì lấy danh sách trúng thưởng từ 00h đến 23h59 ngày hôm trước (datenow - 1 ngày) 
            if($dateNow >= $date8Am)
            {
                
                $date->sub(new DateInterval('P1D')); 
                $fromDate = strtotime($date->format('Y-m-d 00:00:00'));
                $toDate = strtotime($date->format('Y-m-d 23:59:59'));  
                
                //set thời gian truyền vào: fromDate = 00h00p00s & toDate = 23h59p59s của ngày hiện tại - 1
            }
            //Nếu thời gian hiện tại nhỏ hơn 8h sáng thì lấy danh sách trúng thưởng từ 00h đến 23h59 ngày hôm trước nữa (datenow - 2 ngày)
            else
            {
                $date->sub(new DateInterval('P2D')); 
                $fromDate = strtotime($date->format('Y-m-d 00:00:00'));
                $toDate = strtotime($date->format('Y-m-d 23:59:59'));   
                //set thời gian truyền vào: fromDate = 00h00p00s & toDate = 23h59p59s của ngày hiện tại - 2
            }
        }
         //case 3: Nếu currentDate >= EDate + 1 --> Get Ds chung cuộc
        else
        {
            //die('456');
            //Nếu thời gian hiện tại lớn hơn 8h sáng thì lấy danh sách trúng thưởng chung cuộc
            if($dateNow >= $date8Am)
            {
                //$date->sub(new DateInterval('P0D')); 
//                $fromDate = 0;
//                $toDate = 0;
                  $date = date('Y-m-d H:i:s', DOVUI_EDATE);
                  $date = new DateTime($date);
                  $date->format('Y-m-d H:i:s');
                
                  $fromDate = strtotime(date('Y-m-d 00:00:00', DOVUI_EDATE));
                  $toDate = strtotime(date('Y-m-d 23:59:59', DOVUI_EDATE));  
                //set thời gian truyền vào : fromDate = 0 & toDate = 0 (ket qua chung cuộc)
            }
            //Nếu thời gian hiện tại nhỏ hơn 8h sáng thì lấy danh sách trúng thưởng của ngày kết thúc
            else
            {
                $date = date('Y-m-d H:i:s', DOVUI_EDATE);
                $date = new DateTime($date);
                $date->format('Y-m-d H:i:s');
                
                $fromDate = strtotime(date('Y-m-d 00:00:00', DOVUI_EDATE));
                $toDate = strtotime(date('Y-m-d 23:59:59', DOVUI_EDATE));   
                //set thời gian truyền vào : fromDate = 00h00p00s & toDate = 23h59p59s của EDATE_DOVUI
            }
        }
        
            $arrAward = $objNews->getListEndAward(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DOVUI,
            'fromdate'  => $fromDate,
            'todate'  => $toDate,                                    
            ));
            //Nếu danh sách trả về rỗng
            if(empty($arrAward))
            {    
                for($i = 0; $i < 5; $i++)
                {
                    if(empty($arrAward))
                    {
                        $date->sub(new DateInterval('P1D'));
                        $fromDate = strtotime($date->format('Y-m-d 00:00:00')); 
                        $toDate = strtotime($date->format('Y-m-d 23:59:59'));
                       
                        $arrAward = $objNews->getListEndAward(array(            
                            'isGearman' => false,
                            'object_type'  => OBJECT_TYPE_DOVUI,
                            'award'  => AWARD_DOVUI,
                            'fromdate'  => $fromDate,
                            'todate'  => $toDate,                                    
                        ));
                    }
                    else
                    {
                        break;
                    }
                }
            }
                 
        $dateAward = $date; 
        
        
        
        
        $arrAward1 = $objNews->getListAwardByWeek(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DAY_DOVUI,
            'fromdate'  => 0,
            'todate'  => 0,                                    
            ),self::getweek(1,1,0,0,0,0,0,0));
        $arrAward2 = $objNews->getListAwardByWeek(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DAY_DOVUI,
            'fromdate'  => 0,
            'todate'  => 0,                                    
            ),self::getweek(2,1,0,0,0,0,0,0));
        $arrAward3 = $objNews->getListAwardByWeek(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DAY_DOVUI,
            'fromdate'  => 0,
            'todate'  => 0,                                    
            ),self::getweek(3,1,0,0,0,0,0,0));
        $arrAward4 = $objNews->getListAwardByWeek(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DAY_DOVUI,
            'fromdate'  => 0,
            'todate'  => 0,                                    
            ),self::getweek(4,1,0,0,0,0,0,0));
        $arrAward5 = $objNews->getListAwardByWeek(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DAY_DOVUI,
            'fromdate'  => 0,
            'todate'  => 0,                                    
            ),self::getweek(5,1,0,0,0,0,0,0));
        $arrAward6 = $objNews->getListAwardByWeek(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DAY_DOVUI,
            'fromdate'  => 0,
            'todate'  => 0,                                    
            ),self::getweek(6,1,0,0,0,0,0,0));
        $arrAwardBlock = $objNews->getListAwardByWeek(array(            
            'isGearman' => false,
            'object_type'  => OBJECT_TYPE_DOVUI,
            'award'  => AWARD_DAY_DOVUI,
            'fromdate'  => 0,
            'todate'  => 0,                                    
            ),self::getweek(1,0,0,0,0,0,0,0));
            
            
        $week = self::getweek(1,0,$arrAward1,$arrAward2,$arrAward3,$arrAward4,$arrAward5,$arrAward6);
        
        
            
        $this->view->assign(array(            
        'strCateCode' => $strCateCode,
        'currentDate'  => $currentDate,
        'arrAward'  => $arrAward,
        'arrData1'  => $arrAward1,
        'arrData2'  => $arrAward2,
        'arrData3'  => $arrAward3,
        'arrData4'  => $arrAward4,
        'arrData5'  => $arrAward5,
        'arrData6'  => $arrAward6,
        'arrAwardBlock' =>$arrAwardBlock,
        'dateAward' =>$dateAward,
        'week'  => $week
    ));
        $titleMeta = 'Ðố vui World Cup 2014 - VnExpress';
        $keywords = 'World cup 2014, tin tuc , bong da';
        $description = '';
        //Set param
        $this->_request->setParam('block_cate', WORLD_CUP);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', WORLD_CUP);
        //Add script
        $this->view->headTitle($titleMeta);
        $this->view->headMeta()->setName('description', $description);
        $this->view->headMeta()->setName('keywords', $keywords);
    }
    
    public function getweek($weekParam,$isList,$arrAward1,$arrAward2,$arrAward3,$arrAward4,$arrAward5,$arrAward6)
    {
        
        $currentDate = new DateTime();
        $currentDate = $currentDate->format('Y-m-d');
        $week = 1;
        if(DOVUI_SDATE <= $currentDate && $currentDate <= '2014-06-17')
        {
            $week = 1;
            $strWeek = 'Tuần 1 <span>(10/6 - 16/6)</span>';
            
        }
        else if('2014-06-17' < $currentDate && $currentDate <= '2014-06-24')
        {
            $week = 2;
            $strWeek = 'Tuần 2 <span>(17/6 - 23/6)</span>';
        }
        else if('2014-06-24' < $currentDate && $currentDate <= '2014-07-01')
        {
            $week = 3;
            $strWeek = 'Tuần 3 <span>(24/6 - 30/6)</span>';
            if(empty($arrAward3))
            {
                $week = 2;
                $strWeek = 'Tuần 2 <span>(17/6 - 23/6)</span>';
            }
        }
        else if('2014-07-01' < $currentDate && $currentDate <= '2014-07-08')
        {
            $week = 4;
            $strWeek = 'Tuần 4 <span>(01/7 - 07/07)</span>';
            if(empty($arrAward4))
            {
                $week = 3;
                $strWeek = 'Tuần 3 <span>(24/6 - 30/6)</span>';
            }
        }
        else if('2014-07-08' < $currentDate && $currentDate <= '2014-07-15')
        {
            $week = 5;
            $strWeek = 'Tuần 5 <span>(08/07 - 14/07)</span>';
            if(empty($arrAward5))
            {
                $week = 4;
                $strWeek = 'Tuần 4 <span>(01/7 - 07/07)</span>';
            }
        }
        else if('2014-07-15' < $currentDate)
        {
            $week = 6;
            $strWeek = 'Tuần 6 <span>(15/7 - 21/07)</span>';
            if(empty($arrAward6))
            {
                $week = 5;
                $strWeek = 'Tuần 5 <span>(08/07 - 14/07)</span>';
            }
        } 
        
        if($isList == 1)
            $week = $weekParam;
        
        if($isList == 1)
            $week = $weekParam;
        $this->view->assign(array(            
        
        'strweeaward'  => $strWeek
    ));
        
        
        return $week;
    }
}