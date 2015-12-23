function getUrlVars() {
var vars = [], hash;
var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
for (var i = 0; i < hashes.length; i++) {
hash = hashes[i].split('=');

if ($.inArray(hash[0], vars) > -1) {
vars[hash[0]] += "," + hash[1];
}
else {
vars.push(hash[0]);
vars[hash[0]] = hash[1];
}
}

return vars;
}
var CalendarParser = (function(){
    return {
        starDate : 1402333200,
        endDate : 1405960560,
		calendar_parser_base_url:'/dovui',		
        calendar_parser_url:'http://st.f2.vnecdn.net/responsive/j/v15/utils/calendar',
        calendar_parser_id:'calendar',
		calendar_parser_parent: 0,
		calendar_parser_child: 0,
		calendar_parser_child_recursive: 0,
        isloadstatic: 0,
        datepickerloaded: 0,
        blockbox: function(param){
            if (param == 'undefined') {
                return false;
            }
            this.addstatic();
            this._blockboxhtml(param);
            if (this.datepickerloaded == 1) {
                this._blockaction(param);
            }
            else{
                $.getScript( CalendarParser.calendar_parser_url+"/datepicker/js/datepicker.js", function( data, textStatus, jqxhr ) {
                    CalendarParser._blockaction(param);
                    CalendarParser.datepickerloaded = 1;
                });
            }
            

        },
        _blockaction: function(param){
            var prefix = (param.prefix) ? param.prefix : 'block_';
            var divid = (param.id) ? param.id : 'calendarblock';
            var fomatdate = (param.fomatdate) ? param.fomatdate : 'm/d/Y';
            var now3 = new Date();
    		var now4 = new Date();
            var calendarid = prefix+'widgetCalendar';
            var calendarfield = prefix+'widgetField';
            var currentdate	= (param.currentdate) ? param.currentdate : [new Date(now3), new Date(now4)];
    		$("#"+calendarid).DatePicker({
    			flat: true,
    			format: fomatdate, //'d B, Y', // ngohai
    			date: currentdate,
    			calendars: 1, // Chinh so luong thang hien thi - ngohai
    			starts: 1,
                onChange: function(formated) {
				    eval(param.callback)(formated);
			    }
    		});
    		var state = false;
    		$("#" + calendarfield).bind('click', function(){
    			$('#'+calendarid).stop().animate({height: state ? 0 : $('#'+calendarid+' div.datepicker').get(0).offsetHeight}, 500);
    			state = !state;
    			return false;
    		});
    		$('#'+calendarid+' div.datepicker').css('position', 'absolute');
        },
        _blockboxhtml: function(param){
            var prefix = (param.prefix) ? param.prefix : 'block_';
            var divid = (param.id) ? param.id : 'calendarblock';
            
            var widgetCalendar =  document.createElement('div');
            widgetCalendar.setAttribute('id', prefix+'widgetCalendar');
            widgetCalendar.setAttribute('class', 'calendarblockvne');
            var widgetField =  document.createElement('div');
            widgetField.setAttribute('id', prefix+'widgetField');
            var widget =  document.createElement('div');
            widget.setAttribute('id', prefix + 'widget');

            widget.appendChild(widgetField);
            widget.appendChild(widgetCalendar);
            document.getElementById(divid).appendChild(widget);
        },
        addstatic: function(){
            if (this.isloadstatic == 1){
                return false;
            }
			CalendarParser.addCss(CalendarParser.calendar_parser_url+'/datepicker/css/datepicker.css');
			CalendarParser.addCss(CalendarParser.calendar_parser_url+'/datepicker/css/layout.css'); 
                        
            this.isloadstatic = 1;
        },
        parse: function()
        {
            if($('#'+CalendarParser.calendar_parser_id).length > 0 ) 
            {
                this.addstatic();
            }
            else {
                return false;
            }
            this.addHtml();
            if (this.datepickerloaded == 1) {
                this.action();
            }
            else{
                $.getScript( CalendarParser.calendar_parser_url+"/datepicker/js/datepicker.js", function( data, textStatus, jqxhr ) {
                    CalendarParser.action();
                    CalendarParser.datepickerloaded = 1;
                });
            }
            
            
        },
		addCss: function(cssFile) {
			var g = document.createElement('link');
			g.setAttribute('href', cssFile);
			g.setAttribute('type', 'text/css');
			g.setAttribute('rel', 'stylesheet');
			document.getElementsByTagName('head')[0].appendChild(g);
		},
        addHtml: function() {		
            
            var CurrentDate = new Date();
            var cDate = (CurrentDate.getMonth() + 1) + "/" + CurrentDate.getDate() + "/" + CurrentDate.getFullYear();
            
            
            cDate = Date.parse(cDate)/1000;
             
            var parameter = getUrlVars()["day"];    
            var d = new Date(); 
            if(!parameter || !parameter.length)
            {
				
                var Current = new Date();
				
                if(Current.getHours() < 8)
                {
                    d = new Date(Current.getFullYear(), Current.getMonth(), Current.getDate() - 1);
                }
                else
                {
                    d = new Date();
                }
				d = new Date('2014', '06', '21');
            } 
            else
            {
                if(parameter % 1 === 0){
                    if(parameter >= CalendarParser.starDate && parameter <= CalendarParser.endDate && parameter <= cDate)
                    {
						
                        if(parameter == cDate)
						{
							var Current = new Date();
							if(Current.getHours() < 8)
							{
								
								d = new Date(Current.getFullYear(), Current.getMonth(), Current.getDate() - 1);
							}
							else
							{
								d = new Date();
							}
						}
						else
						{
							d = new Date(parameter*1000);
						}
                    }
                    else
                    {
						
                        d =    new Date(cDate*1000);
                    }
                }
                else
                    d = new Date(); 
            }
            var day=d.getDay();
            
            var date=d.getDate();
            
            var month=d.getMonth();
            
            var year=d.getFullYear();
            
            var days=new Array("Chủ nhật","Thứ hai","Thứ ba","Thứ tư","Thứ năm","Thứ sáu","Thứ bảy");
            
            var months=new Array("1","2","3","4","5","6","7","8","9","10","11","12");
            
            //alert(days[day] + ", " + date + " tháng " + months[month] + ", " + year);
                    
            var nowDate = new Date();
            nowDate = [nowDate.getDate(), nowDate.getMonth()+1, nowDate.getFullYear()].join('/');
            var span =  document.createElement('span');
			span.setAttribute('id', 'vne_ttn_current_date1');
			//span.setAttribute('style', 'font : 12px/22px arial !important;');
            span.innerHTML = days[day] + ", " + date + " tháng " + months[month] + ", " + year;
            //alert(days[day] + ", " + date + " tháng " + months[month] + ", " + year);
            /*var ahref = document.createElement('a');
            ahref.setAttribute('href', '#');
            ahref.innerHTML ="Chọn ngày";*/
            
            var widgetField =  document.createElement('div');
            widgetField.setAttribute('id', 'widgetField');
            
            var widgetCalendar =  document.createElement('div');
            widgetCalendar.setAttribute('id', 'widgetCalendar');
            
            var widget =  document.createElement('div');
            widget.setAttribute('id', 'widget');
            
            widgetField.appendChild(span);
            //widgetField.appendChild(ahref);
            
            widget.appendChild(widgetField);
            widget.appendChild(widgetCalendar);
            
            document.getElementById(CalendarParser.calendar_parser_id).appendChild(widget);
            
        },
        action: function(){
            var now3 = $("#current_cate_from").val();
            now3 = (now3) ? now3 : new Date();
            var now4 = $("#current_cate_to").val();
            now4 = (now4) ? now4 : new Date();
    		$('#widgetCalendar').DatePicker({
    			flat: true,
    			format: 'm/d/Y', //'d B, Y', // ngohai
    			date: [now3, now4],
    			calendars: 1, // Chinh so luong thang hien thi - ngohai
    			mode: 'range',
    			starts: 1,
    			onChange: function(formated) {
    			 
                    var stringdate = formated[0];
                    var formated = stringdate.split('-');
                    // Xử lý ngày tháng & redirect url
                    //cuongnm17 begin
                    var ffdate = formated[0].split('/');
                    
                    ffdate = ffdate[1]+'/'+ffdate[0]+'/'+ffdate[2];
                    ffdate = ffdate.split('/');
                    ffdate = ffdate[1]+'/'+ffdate[0]+'/'+ffdate[2];
                    
                    var fromdate = Date.parse(ffdate)/1000;
        			
                    
                    
                    var CurrentDate = new Date();
                    var cDate = (CurrentDate.getMonth() + 1) + "/" + CurrentDate.getDate() + "/" + CurrentDate.getFullYear();
                    cDate = Date.parse(cDate)/1000;
                    if(CurrentDate.getHours() < 8)
                    {
                        CurrentDate = new Date(CurrentDate.getFullYear(), CurrentDate.getMonth(), CurrentDate.getDate() - 1);
                        cDate = (CurrentDate.getMonth() + 1) + "/" + CurrentDate.getDate() + "/" + CurrentDate.getFullYear();
                        cDate = Date.parse(cDate)/1000;
                    }
                    
                    if(fromdate >= CalendarParser.starDate && fromdate <= CalendarParser.endDate && fromdate <= cDate)
                    {
                        var url = '';
                        url = CalendarParser.calendar_parser_base_url+'/?day='+fromdate;
                        window.location.href = url;
		             
                    }
    			}
    		});
            
    		var state = false;
    		$('#widgetField>span, .btn_calenda_dong').on('click', function(){
                state = !state;
                var value_height;
    		    if(state == true)
                {
                    value_height = 266;
                }
                else
                {
                    value_height = $('#widgetCalendar div.datepicker').get(0).offsetHeight;
                }
    			$('#widgetCalendar').stop().animate({height: value_height}, 500);
    			
                //console.log(state_abc);
    			return false;
    		});
    		$('#widgetCalendar div.datepicker').css('position', 'absolute');
            //console.log(state);
        }
    }
})();