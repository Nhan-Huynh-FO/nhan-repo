var PredictMatch = {
    addpredict : function () {
        $(".btn_sudoan").click(function () {
            if($("#goalteam1").val() == -1 || $("#goalteam2").val() == -1){
                Lightbox.showemsg('Dự đoán', '<b>Vui lòng nhập đầy đủ và chính xác các thông tin để tham gia dự đoán.</b>', 'Đóng');
                return false;
            }
            var goalteam1 = $("#goalteam1").val();
            var goalteam2 = $("#goalteam2").val();
            var matchid   = $("#matchid").val();
            $.ajax({
                type: "GET",
                url: urlPredict,
                data: {
                    matchid: matchid,
                    goalteam1 :  goalteam1,
                    goalteam2 :  goalteam2
                },
                dataType: 'json',
                async:false,
                success: function(response) {
                    if (response.result == 1) {
                        Lightbox.showemsg('Dự đoán', '<b>Dự đoán tỉ số thành công.</b>', 'Đóng');
                        //window.location = window.location.href;
                    }else{
                        Lightbox.showemsg('Dự đoán', '<b>Dự đoán tỉ số không thành công. Vui lòng thử lại sau. </b>', 'Đóng');
                    }
                    $("#goalteam1").val('');
                    $("#goalteam2").val('');
                }
            });
        });
    }
};

$(function(){        
    PredictMatch.addpredict();
});



