var PredictMatch = {
    addpredict: function() {
        $(".btn_send").click(function() {
            if ($("#goalteam1").val() == '' || $("#goalteam2").val() == '') {
                Lightbox.showemsg('Thông báo', '<b>Vui lòng nhập đầy đủ và chính xác các thông tin để tham gia dự đoán.</b>', 'Đóng');
                $("#goalteam1").focus();
                return false;
            }
            if (Number($("#goalteam1").val()).toString() == 'NaN' || Number($("#goalteam2").val()).toString() == 'NaN')
            {
                Lightbox.showemsg('Thông báo', '<b>Vui lòng nhập bằng số.</b>', 'Đóng');
                $("#goalteam1").focus();
                return false;
            }
            var goalteam1 = $("#goalteam1").val();
            var goalteam2 = $("#goalteam2").val();
            var matchid = $("#matchid").val();
            $.ajax({
                type: "GET",
                url: urlPredict,
                data: {
                    matchid: matchid,
                    goalteam1: goalteam1,
                    goalteam2: goalteam2
                },
                dataType: 'json',
                async: false,
                success: function(response) {
                    if (response.result == 1) {
                        Lightbox.showemsg('Thông báo', '<b>Dự đoán tỉ số thành công.</b>', 'Đóng');
                        window.location = window.location.href;
                    } else {
                        Lightbox.showemsg('Thông báo', '<b>Dự đoán tỉ số không thành công. Vui lòng thử lại sau. </b>', 'Đóng');
                    }
                    $("#goalteam1").val('');
                    $("#goalteam2").val('');
                }
            });
        });
    }
};
$(document).ready(function() {
    $(".btn_guess").click(function() {
        $("#goalteam1").focus();
    });
    PredictMatch.addpredict();
});
